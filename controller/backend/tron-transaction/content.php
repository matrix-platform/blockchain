<?php //>

return new class('TronTransaction') extends matrix\web\backend\GetController {

    protected function postprocess($form, $result) {
        $table = $this->table();
        $table->hash->formStyle('anchor')->href('https://tronscan.org/#/transaction/{{ hash }}')->link(true);
        $table->sender->formStyle('anchor')->href('https://tronscan.org/#/address/{{ sender }}')->link(true);
        $table->receiver->formStyle('anchor')->href('https://tronscan.org/#/address/{{ receiver }}')->link(true);
        $table->fee->readonly(true);
        $table->status->readonly(true);

        $data = $result['data'];

        if ($data['notify_time']) {
            $table->notify_time->readonly(true);
        } else {
            $table->notify_time->invisible(true);
        }

        if ($data['confirm_time']) {
            $table->confirm_time->readonly(true);

            $this->buttons(['renotify' => ['path' => 'tron-transaction/notify/{{ id }}', 'ranking' => 1000]]);
        } else {
            $table->confirm_time->invisible(true);
        }

        return $result;
    }

};
