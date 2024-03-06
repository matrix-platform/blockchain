<?php //>

// php www/index.php /console/tron/collect

use matrix\blockchain\MerchantHelper;
use matrix\blockchain\TronHelper;
use matrix\cli\Mutex;

return new class() extends matrix\cli\Controller {

    use MerchantHelper, Mutex, TronHelper;

    private $decimals;
    private $notified;

    protected function init() {
        $this->decimals = cfg('tron-api.trc-usdt-decimals');
        $this->notified = [];
    }

    protected function process($form) {
        $table = table('TronTransaction');

        foreach ($table->filter(['type' => 3, 'status' => 1])->list() as $row) {
            $tx = $this->getTransaction($row['hash']);

            if ($tx) {
                $table->filter($row['id'])->updateOne([
                    'fee' => round(intval(@$tx['fee']) / 1000000, 6),
                    'confirm_time' => now(),
                    'status' => 2,
                ]);
            }
        }

        //--

        $list = [];

        foreach ($table->filter(['type' => 1, $table->collection_id->isNull(), 'status' => 2])->list() as $row) {
            $list[$row['receiver']][] = $row;
        }

        foreach ($list as $receiver => $rows) {
            if ($table->filter(['sender' => $receiver, 'type' => 2, 'status' => 1])->count()) {
                continue;
            }

            $amount = 0;

            foreach ($rows as $row) {
                $amount = round($amount + $row['amount'], $this->decimals);
            }

            if ($amount < 1) {
                continue;
            }

            $wallet = table('TronWallet')->filter(['address' => $receiver])->get();
            $merchant = table('Merchant')->filter($wallet['merchant_id'])->get();

            if ($amount < $merchant['trc_usdt_threshold']) {
                continue;
            }

            //--

            $usdt = $this->getUsdtBalance($receiver);

            if (!is_numeric($usdt)) {
                return ['message' => i18n('error.trc-usdt-balance-not-found')];
            }

            if ($usdt < $amount) {
                $this->notify($merchant, ['type' => 7, 'sender' => $receiver, 'amount' => $amount]);

                continue;
            }

            //--

            $trx = $this->getTrxBalance($receiver);

            if (!is_numeric($trx)) {
                return ['message' => i18n('error.trx-balance-not-found')];
            }

            if ($trx < $merchant['trx_safety_amount']) {
                if (!$table->filter(['receiver' => $receiver, 'type' => 3, 'status' => 1])->count()) {
                    $this->checkMerchantTrx($merchant);

                    $tx = $this->transferTrx($merchant, $receiver, $merchant['trx_recharge_amount']);

                    if (!$tx) {
                        return ['message' => i18n('error.tron-transfer-trx-failed')];
                    }

                    $data = $table->insert([
                        'hash' => $tx->txID,
                        'sender' => $merchant['trx_wallet'],
                        'receiver' => $receiver,
                        'type' => 3,
                        'amount' => $merchant['trx_recharge_amount'],
                        'status' => 1,
                    ]);

                    $this->notify($merchant, ['type' => 4, 'hash' => $data['hash'], 'sender' => $data['sender'], 'receiver' => $data['receiver'], 'amount' => $data['amount']]);
                }

                continue;
            }

            //--

            $tx = $this->transferUsdt($wallet, $merchant['trc_usdt_wallet'], $usdt);

            if (!$tx) {
                return ['message' => i18n('error.tron-transfer-trc-usdt-failed')];
            }

            $data = $table->insert([
                'hash' => $tx->txID,
                'sender' => $receiver,
                'receiver' => $merchant['trc_usdt_wallet'],
                'type' => 2,
                'amount' => $usdt,
                'status' => 1,
            ]);

            $this->notify($merchant, ['type' => 5, 'hash' => $data['hash'], 'sender' => $data['sender'], 'receiver' => $data['receiver'], 'amount' => $data['amount']]);

            //--

            $table->filter(['id' => array_column($rows, 'id')])->update(['collection_id' => $data['id']]);
        }

        return ['success' => true];
    }

    protected function transaction() {
        return null;
    }

    private function checkMerchantTrx($merchant) {
        if (!@$this->notified[$merchant['id']]) {
            $trx = $this->getTrxBalance($merchant['trx_wallet']);

            if (is_numeric($trx) && $trx < $merchant['trx_safety_balance']) {
                if ($this->notify($merchant, ['type' => 8, 'amount' => $trx])) {
                    $this->notified[$merchant['id']] = true;
                }
            }
        }
    }

    private function mutex() {
        return "TronScan";
    }

};
