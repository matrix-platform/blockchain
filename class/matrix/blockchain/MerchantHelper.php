<?php //>

namespace matrix\blockchain;

trait MerchantHelper {

    private function notify($merchant, $params) {
        $params['merchant'] = $merchant['merchant_no'];

        logging('tron-notify')->info($merchant['merchant_no'], $params);

        $data = base64_encode(json_encode($params));

        $request = [
            'data' => $data,
            'hash' => hash('sha256', "{$data}{$merchant['api_secret']}"),
        ];

        $headers = [
            'Content-Type: application/json',
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $merchant['api_url']);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($request));

        $raw = curl_exec($ch);

        curl_close($ch);

        $response = json_decode($raw, true);

        logging('tron-notify')->info($merchant['merchant_no'], $response ?: [$raw]);

        return @$response['success'];
    }

    private function notifyTronTransaction($data) {
        switch ($data['type']) {
        case 1:
            $name = 'receiver';
            $params = ['type' => 1, 'hash' => $data['hash'], 'sender' => $data['sender'], 'receiver' => $data['receiver'], 'amount' => $data['amount']];
            break;
        case 2:
            $name = 'sender';
            $params = ['type' => 3, 'hash' => $data['hash'], 'sender' => $data['sender'], 'receiver' => $data['receiver'], 'amount' => $data['amount'], 'fee' => $data['fee']];
            break;
        case 3:
            $name = 'receiver';
            $params = ['type' => 2, 'hash' => $data['hash'], 'sender' => $data['sender'], 'receiver' => $data['receiver'], 'amount' => $data['amount'], 'fee' => $data['fee']];
            break;
        default:
            $params = null;
        }

        if ($params) {
            $wallet = table('TronWallet')->filter(['address' => $params[$name]])->get();
            $merchant = table('Merchant')->filter($wallet['merchant_id'])->get();

            return $this->notify($merchant, $params);
        }

        return false;
    }

}
