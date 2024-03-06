<?php //>

return new class() extends matrix\web\api\Controller {

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

        return ['success' => true, 'data' => $data];
    }

};
