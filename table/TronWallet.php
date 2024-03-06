<?php //>

use matrix\db\column\CreateTime;
use matrix\db\column\Integer;
use matrix\db\column\Text;
use matrix\db\Table;

$tbl = new Table('custom_tron_wallet');

$tbl->add('merchant_id', Integer::class)
    ->associate('merchant', 'Merchant')
    ->required(true);

$tbl->add('username', Text::class);

$tbl->add('address', Text::class)
    ->readonly(true)
    ->required(true)
    ->unique(true);

$tbl->add('hex_address', Text::class)
    ->invisible(true)
    ->readonly(true)
    ->required(true);

$tbl->add('private_key', Text::class)
    ->invisible(true)
    ->readonly(true)
    ->required(true);

$tbl->add('create_time', CreateTime::class)
    ->required(true);

$tbl->add('status', Integer::class)
    ->default(1)
    ->options(load_options('tron-wallet-status'))
    ->required(true);

return $tbl;
