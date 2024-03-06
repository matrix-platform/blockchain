<?php //>

use matrix\blockchain\MerchantHelper;

return new class() extends matrix\web\backend\Controller {

    use MerchantHelper;

    public function available() {
        if ($this->method() === 'POST') {
            $pattern = preg_quote($this->name(), '/');

            return preg_match("/^{$pattern}\/[\w-]+$/", $this->path());
        }

        return false;
    }

    protected function process($form) {
        $data = table('TronTransaction')->filter($this->args()[0])->get();

        if (!$data || !$data['notify_time']) {
            return ['error' => 'error.data-not-found'];
        }

        if (!$this->notifyTronTransaction($data)) {
            return ['error' => 'error.renotify-failed'];
        }

        return [
            'success' => true,
            'type' => 'message',
            'message' => i18n('backend.renotify-success'),
        ];
    }

};
