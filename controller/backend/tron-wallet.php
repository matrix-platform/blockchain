<?php //>

return new class('TronWallet') extends matrix\web\backend\ListController {

    protected function init() {
        $this->columns([
            'merchant_no' => 'merchant.merchant_no',
            'merchant_title' => 'merchant.title',
            'username',
            'address',
            'create_time',
            'status',
        ]);

        $this->defaultRanking(['merchant_no', 'address']);
    }

};
