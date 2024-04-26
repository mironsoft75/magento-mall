<?php

namespace Webkul\SpinToWin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CloudCode extends AbstractDb{
    public function _construct(){
        $this->_init('spintowin_cloud_code','id');
    }
}