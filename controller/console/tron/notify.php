<?php //>

// php www/index.php /console/tron/notify

use matrix\blockchain\MerchantHelper;
use matrix\cli\Mutex;

return new class() extends matrix\cli\Controller {

    use MerchantHelper, Mutex;

    protected function process($form) {
        $table = table('TronTransaction');

        foreach ($table->filter([$table->notify_time->isNull(), 'status' => 2])->list() as $row) {
            if ($this->notifyTronTransaction($row)) {
                $table->filter($row['id'])->updateOne(['notify_time' => now()]);
            }
        }

        //--

        $table = table('TronException');

        foreach ($table->filter($table->notify_time->isNull())->list() as $row) {
            $data = ['type' => 6, 'hash' => $row['hash'], 'sender' => $row['sender'], 'receiver' => $row['receiver'], 'amount' => $row['amount'], 'fee' => $row['fee']];
            $name = 'sender';

            $wallet = table('TronWallet')->filter(['address' => $data[$name]])->get();
            $merchant = table('Merchant')->filter($wallet['merchant_id'])->get();

            if ($this->notify($merchant, $data)) {
                $table->filter($row['id'])->updateOne(['notify_time' => now()]);
            }
        }

        //--

        return ['success' => true];
    }

    protected function transaction() {
        return null;
    }

    private function mutex() {
        return '.TronScan';
    }

};
