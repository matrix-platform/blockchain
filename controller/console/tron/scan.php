<?php //>

// php www/index.php /console/tron/scan

use IEXBase\TronAPI\Support\Keccak;
use IEXBase\TronAPI\Tron;
use Web3\Contracts\Ethabi;
use Web3\Contracts\Types\Address;
use Web3\Contracts\Types\Boolean;
use Web3\Contracts\Types\Bytes;
use Web3\Contracts\Types\DynamicBytes;
use Web3\Contracts\Types\Integer;
use Web3\Contracts\Types\Str;
use Web3\Contracts\Types\Uinteger;
use matrix\blockchain\TronHelper;
use matrix\cli\Mutex;

return new class() extends matrix\cli\Controller {

    use Mutex, TronHelper;

    private $abi;
    private $contract;
    private $decimals;
    private $event;
    private $rate;
    private $tron;

    protected function init() {
        $this->abi = new Ethabi([
            'address' => new Address(),
            'bool' => new Boolean(),
            'bytes' => new Bytes(),
            'dynamicBytes' => new DynamicBytes(),
            'int' => new Integer(),
            'string' => new Str(),
            'uint' => new Uinteger(),
        ]);

        $this->contract = cfg('tron-api.trc-usdt-contract-address');
        $this->decimals = cfg('tron-api.trc-usdt-decimals');
        $this->event = Keccak::hash('Transfer(address,address,uint256)', 256);
        $this->rate = 10 ** $this->decimals;
        $this->tron = new Tron();
    }

    protected function process($form) {
        $current = $this->getNowBlockNumber();

        if (!$current) {
            return ['message' => i18n('error.tron-now-block-not-found')];
        }

        $file = get_data_file('last-tron-block-number', false);
        $last = intval(@file_get_contents($file)) ?: ($current - 1);

        $wallets = array_column(table('TronWallet')->filter()->list(['address']), 'id', 'address');
        $collections = array_column(table('TronTransaction')->filter(['type' => 2, 'status' => 1])->list(['hash']), 'id', 'hash');

        while ($current > $last) {
            $list = $this->scan(++$last);

            if ($list === false) {
                return ['message' => i18n('error.tron-block-not-found')];
            }

            foreach ($list as $item) {
                if (@$wallets[$item['receiver']] && !table('TronTransaction')->filter(['hash' => $item['hash']])->count()) {
                    $item['type'] = 1;
                    $item['confirm_time'] = now();
                    $item['status'] = 2;

                    table('TronTransaction')->insert($item);

                    continue;
                }

                if (@$wallets[$item['sender']] && !table('TronWallet')->filter(['id' => $wallets[$item['sender']], 'merchant.trc_usdt_wallet' => $item['receiver']])->count()) {
                    table('TronException')->insert($item);
                }

                if (@$collections[$item['hash']]) {
                    table('TronTransaction')->filter($collections[$item['hash']])->updateOne(['fee' => $item['fee'], 'confirm_time' => now(), 'status' => 2]);
                }
            }

            file_put_contents($file, "{$last}");
        }

        return ['success' => true];
    }

    protected function transaction() {
        return null;
    }

    private function mutex() {
        return '.TronScan';
    }

    private function scan($number) {
        $list = $this->getBlock($number);

        if (!is_array($list)) {
            return false;
        }

        $result = [];

        foreach ($list as $tx) {
            $contract = @$tx['contract_address'];

            if (!$contract || $this->tron->fromHex($contract) !== $this->contract || !@$tx['log'] || @$tx['receipt']['result'] !== 'SUCCESS') {
                continue;
            }

            foreach ($tx['log'] as $log) {
                if (@$log['address'] && @$log['topics'][0] === $this->event) {
                    $data = $this->abi->decodeParameters(['uint256'], $log['data']);
                    $from = substr($this->abi->decodeParameter('address', $log['topics'][1]), 2);
                    $to = substr($this->abi->decodeParameter('address', $log['topics'][2]), 2);

                    $result[] = [
                        'hash' => $tx['id'],
                        'sender' => $this->tron->fromHex("41{$from}"),
                        'receiver' => $this->tron->fromHex("41{$to}"),
                        'amount' => round(intval($data[0]->value) / $this->rate, $this->decimals),
                        'fee' => @$tx['fee'] ? round(intval($tx['fee']) / 1000000, 6) : 0,
                    ];

                    break;
                }
            }
        }

        return $result;
    }

};
