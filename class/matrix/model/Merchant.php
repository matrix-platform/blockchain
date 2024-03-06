<?php //>

namespace matrix\model;

use matrix\db\Model;

class Merchant extends Model {

    public function queryByMerchantNo($merchant_no) {
        if (!$merchant_no) {
            return null;
        }

        return $this->find([
            'merchant_no' => $merchant_no,
            $this->table->begin_date->notNull()->lessThanOrEqual(today()),
            $this->table->expire_date->isNull()->or()->greaterThan(today()),
            'disabled' => false,
        ]);
    }

}
