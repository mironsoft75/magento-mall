<?php

namespace Webkul\SpinToWin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ProductAddress extends AbstractDb{
    public function _construct(){
        $this->_init('spintowin_product_address','address_id');
    }
}