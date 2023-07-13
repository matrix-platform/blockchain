<?php //>

return new class('TrcWallet') extends matrix\web\backend\ListController {

    protected function init() {
        $this->columns([
            'username' => 'member.username',
            'address',
            'create_time',
            'status',
        ]);
    }

};
