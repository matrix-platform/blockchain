<?php //>

use matrix\blockchain\TronHelper;

return new class() extends matrix\web\api\Controller {

    use TronHelper;

    protected function process($form) {
        $raw = @$form['data'];
        $data = json_decode(base64_decode($raw), true);
        $merchant = is_string(@$data['merchant']) ? model('Merchant')->queryByMerchantNo($data['merchant']) : null;

        if (!$merchant) {
            return ['error' => 'error.merchant-not-found'];
        }

        if (hash('sha256', "{$raw}{$merchant['api_secret']}") !== @$form['hash']) {
            return ['error' => 'error.invalid-hash'];
        }

        if (is_string(@$data['username']) && strlen($data['username'])) {
            $wallet = model('TronWallet')->find(['merchant_id' => $merchant['id'], 'username' => $data['username'], 'status' => 1]);

            if (!$wallet) {
                $wallet = $this->initTronWallet($merchant['id'], $data['username']);
            }

            return ['success' => true, 'address' => $wallet['address']];
        } else {
            return ['error' => 'error.username-required'];
        }
    }

};
