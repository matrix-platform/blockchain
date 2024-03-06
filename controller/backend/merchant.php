<?php //>

return new class('Merchant') extends matrix\web\backend\ListController {

    protected function init() {
        $this->columns([
            'merchant_no',
            'title',
            'api_url',
        ]);

        $this->defaultRanking(['merchant_no']);
    }

};
