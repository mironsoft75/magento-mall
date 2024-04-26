<?php
namespace Aidot\Webps\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ImageWebpsLog extends AbstractDb{

    
    public function _construct(){
        $this->_init('image_webps_log','id');
    }

}
