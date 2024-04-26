<?php
namespace Aidot\Webps\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class TinyAccount extends AbstractDb{

    
    public function _construct(){
        $this->_init('tiny_account','id');
    }

}
