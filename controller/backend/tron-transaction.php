<?php //>

return new class('TronTransaction') extends matrix\web\backend\ListController {

    protected function init() {
        $table = $this->table();
        $table->hash->listStyle('anchor')->href('https://tronscan.org/#/transaction/{{ hash }}')->link(true);
        $table->sender->listStyle('anchor')->href('https://tronscan.org/#/address/{{ sender }}')->link(true);
        $table->receiver->listStyle('anchor')->href('https://tronscan.org/#/address/{{ receiver }}')->link(true);

        $this->columns([
            'type',
            'amount',
            'sender',
            'receiver',
            'hash',
            'confirm_time',
            'status',
        ]);

        $this->defaultRanking(['-id']);
    }

};
