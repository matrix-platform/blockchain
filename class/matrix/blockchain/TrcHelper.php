<?php //>

namespace matrix\blockchain;

use GuzzleHttp\Client;
use Tron\Api;
use Tron\TRX;

trait TrcHelper {

    private function generateAddress() {
        $api = new Api(new Client(['base_uri' => cfg('tron-api.uri')]));
        $trx = new TRX($api);

        return $trx->generateAddress();
    }

    private function initTrcWallet($member_id) {
        $table = table('TrcWallet');
        $data = $table->filter(['member_id' => $member_id, 'status' => 1])->get();

        if (!$data) {
            $address = $this->generateAddress();

            $data = $table->insert([
                'member_id' => $member_id,
                'address' => $address->address,
                'hex_address' => $address->hexAddress,
                'private_key' => $address->privateKey,
            ]);
        }

        return $data;
    }

}
