<?php //>

use matrix\db\column\Boolean;
use matrix\db\column\Date;
use matrix\db\column\Double;
use matrix\db\column\Integer;
use matrix\db\column\Text;
use matrix\db\column\Textarea;
use matrix\db\column\Url;
use matrix\db\Table;

$tbl = new Table('custom_merchant');

$tbl->add('merchant_no', Text::class)
    ->required(true)
    ->unique(true);

$tbl->add('title', Text::class)
    ->required(true);

$tbl->add('api_url', Url::class)
    ->required(true);

$tbl->add('api_secret', Text::class)
    ->required(true);

$tbl->add('trc_usdt_wallet', Text::class)
    ->required(true)
    ->tab('tron')
    ->validation('tron-address');

$tbl->add('trc_usdt_threshold', Double::class)
    ->default(1)
    ->required(true)
    ->tab('tron');

$tbl->add('trx_wallet', Text::class)
    ->required(true)
    ->tab('tron')
    ->validation('tron-address');

$tbl->add('trx_private_key', Textarea::class)
    ->required(true)
    ->tab('tron')
    ->traceable(false);

$tbl->add('trx_safety_balance', Integer::class)
    ->default(0)
    ->required(true)
    ->tab('tron');

$tbl->add('trx_recharge_amount', Integer::class)
    ->default(0)
    ->required(true)
    ->tab('tron');

$tbl->add('trx_safety_amount', Integer::class)
    ->default(0)
    ->required(true)
    ->tab('tron');

$tbl->add('begin_date', Date::class);

$tbl->add('expire_date', Date::class);

$tbl->add('disabled', Boolean::class)
    ->default(false)
    ->required(true);

return $tbl;
