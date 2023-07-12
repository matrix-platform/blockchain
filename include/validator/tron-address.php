<?php //>

use GuzzleHttp\Client;
use Tron\Address;
use Tron\Api;
use Tron\TRX;

return function ($value, $options) {
    $api = new Api(new Client(['base_uri' => cfg('tron-api.uri')]));
    $trx = new TRX($api);

    return $trx->validateAddress(new Address($value));
};
