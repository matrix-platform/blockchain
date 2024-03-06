<?php //>

use matrix\db\column\CreateTime;
use matrix\db\column\Double;
use matrix\db\column\Integer;
use matrix\db\column\Text;
use matrix\db\column\Timestamp;
use matrix\db\Table;

$tbl = new Table('custom_tron_transaction');

$tbl->add('hash', Text::class)
    ->readonly(true)
    ->required(true)
    ->unique(true);

$tbl->add('sender', Text::class)
    ->readonly(true)
    ->required(true);

$tbl->add('receiver', Text::class)
    ->readonly(true)
    ->required(true);

$tbl->add('type', Integer::class)
    ->options(load_options('tron-tx-type'))
    ->readonly(true)
    ->required(true);

$tbl->add('amount', Double::class)
    ->readonly(true)
    ->required(true);

$tbl->add('fee', Double::class)
    ->default(0)
    ->required(true);

$tbl->add('collection_id', Integer::class)
    ->invisible(true);

$tbl->add('notify_time', Timestamp::class);

$tbl->add('confirm_time', Timestamp::class);

$tbl->add('create_time', CreateTime::class)
    ->required(true);

$tbl->add('status', Integer::class)
    ->options(load_options('tron-tx-status'))
    ->required(true);

return $tbl;
