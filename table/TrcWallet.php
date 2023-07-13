<?php //>

use matrix\db\column\CreateTime;
use matrix\db\column\Integer;
use matrix\db\column\Text;
use matrix\db\Table;

$tbl = new Table('custom_trc_wallet');

$tbl->add('member_id', Integer::class)
    ->associate('member', 'Member')
    ->readonly(true)
    ->required(true);

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
    ->options(load_options('trc-wallet-status'))
    ->required(true);

return $tbl;
