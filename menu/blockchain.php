<?php //>

return [

    'merchants' => ['icon' => 'fas fa-building', 'ranking' => 2300, 'parent' => null],

        'merchant' => ['icon' => 'far fa-building', 'ranking' => 100, 'parent' => 'merchants', 'group' => true, 'tag' => 'query'],

            'merchant/' => ['parent' => 'merchant', 'tag' => 'query'],

            'merchant/delete' => ['parent' => 'merchant', 'tag' => 'delete'],

            'merchant/insert' => ['parent' => 'merchant', 'tag' => 'insert'],

            'merchant/new' => ['parent' => 'merchant', 'tag' => 'insert'],

            'merchant/update' => ['parent' => 'merchant', 'tag' => 'update'],

    'tron' => ['icon' => 'fas fa-coins', 'ranking' => 2320, 'parent' => null],

        'tron-wallet' => ['icon' => 'fas fa-wallet', 'ranking' => 100, 'parent' => 'tron', 'group' => true, 'tag' => 'query'],

        'tron-transaction' => ['icon' => 'fas fa-list-ul', 'ranking' => 200, 'parent' => 'tron', 'group' => true, 'tag' => 'query'],

            'tron-transaction/' => ['parent' => 'tron-transaction', 'tag' => 'query'],

            'tron-transaction/notify' => ['parent' => 'tron-transaction', 'tag' => 'query'],

];
