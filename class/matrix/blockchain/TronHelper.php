<?php //>

namespace matrix\blockchain;

use Exception;
use GuzzleHttp\Client;
use Tron\Address;
use Tron\Api;
use Tron\TRC20;
use Tron\TRX;
use matrix\utility\Func;

trait TronHelper {

    private function generateAddress() {
        $api = new Api(new Client(['base_uri' => cfg('tron-api.uri')]));
        $trx = new TRX($api);

        try {
            return $trx->generateAddress();
        } catch (Exception $ignore) {
            return false;
        }
    }

    private function getBlock($number) {
        $uri = cfg('tron-api.uri');

        try {
            $client = new Client();

            $response = $client->request('POST', "{$uri}/walletsolidity/gettransactioninfobyblocknum", [
                'body' => "{\"num\":{$number}}",
                'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception $ignore) {
            return false;
        }
    }

    private function getNowBlockNumber() {
        $uri = cfg('tron-api.uri');

        try {
            $client = new Client();
            $response = $client->request('GET', "{$uri}/walletsolidity/getnowblock", ['headers' => ['Accept' => 'application/json']]);
            $data = json_decode($response->getBody());

            return isset($data->blockID) ? $data->block_header->raw_data->number : null;
        } catch (Exception $ignore) {
            return false;
        }
    }

    private function getTransaction($id) {
        $uri = cfg('tron-api.uri');

        try {
            $client = new Client();

            $response = $client->request('POST', "{$uri}/walletsolidity/gettransactioninfobyid", [
                'body' => "{\"value\":\"{$id}\"}",
                'headers' => ['Accept' => 'application/json', 'Content-Type' => 'application/json'],
            ]);

            return json_decode($response->getBody(), true);
        } catch (Exception $ignore) {
            return false;
        }
    }

    private function getTrxBalance($address) {
        $api = new Api(new Client(['base_uri' => cfg('tron-api.uri')]));
        $trx = new TRX($api);

        try {
            return $trx->balance(new Address($address));
        } catch (Exception $ignore) {
            return false;
        }
    }

    private function getUsdtBalance($address) {
        $api = new Api(new Client(['base_uri' => cfg('tron-api.uri')]));
        $config = ['contract_address' => cfg('tron-api.trc-usdt-contract-address'), 'decimals' => cfg('tron-api.trc-usdt-decimals')];
        $trc = new TRC20($api, $config);

        try {
            return $trc->balance(new Address($address, '', $trc->tron->address2HexString($address)));
        } catch (Exception $ignore) {
            return false;
        }
    }

    private function initTronWallet($merchant_id = 0, $username = null) {
        $secret = cfg('tron-api.data-secret');
        $resource = $secret ? $this->generateAddress() : null;

        if (@$resource->address) {
            return table('TronWallet')->insert([
                'merchant_id' => $merchant_id,
                'username' => $username,
                'address' => $resource->address,
                'hex_address' => Func::encrypt($resource->hexAddress, $secret),
                'private_key' => Func::encrypt($resource->privateKey, $secret),
            ]);
        }
    }

    private function transferTrx($merchant, $address, $amount) {
        $api = new Api(new Client(['base_uri' => cfg('tron-api.uri')]));
        $trx = new TRX($api);

        $from = new Address($merchant['trx_wallet'], $merchant['trx_private_key']);
        $to = new Address($address);

        try {
            return $trx->transfer($from, $to, $amount);
        } catch (Exception $ignore) {
            return false;
        }
    }

    private function transferUsdt($wallet, $address, $amount) {
        $api = new Api(new Client(['base_uri' => cfg('tron-api.uri')]));
        $config = ['contract_address' => cfg('tron-api.trc-usdt-contract-address'), 'decimals' => cfg('tron-api.trc-usdt-decimals')];
        $trc = new TRC20($api, $config);

        $secret = cfg('tron-api.data-secret');
        $from = new Address($wallet['address'], Func::decrypt($wallet['private_key'], $secret), Func::decrypt($wallet['hex_address'], $secret));
        $to = new Address($address, '', $trc->tron->address2HexString($address));

        try {
            return $trc->transfer($from, $to, $amount);
        } catch (Exception $ignore) {
            return false;
        }
    }

}
