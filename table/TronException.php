<?php //>

use matrix\db\column\CreateTime;
use matrix\db\column\Double;
use matrix\db\column\Text;
use matrix\db\column\Timestamp;
use matrix\db\Table;

$tbl = new Table('custom_tron_exception', false);

$tbl->add('hash', Text::class)
    ->readonly(true);

$tbl->add('sender', Text::class)
    ->readonly(true);

$tbl->add('receiver', Text::class)
    ->readonly(true);

$tbl->add('amount', Double::class)
    ->readonly(true);

$tbl->add('notify_time', Timestamp::class);

$tbl->add('create_time', CreateTime::class);

return $tbl;
