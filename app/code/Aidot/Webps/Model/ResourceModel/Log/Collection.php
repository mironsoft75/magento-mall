<?php
namespace Aidot\Webps\Model\ResourceModel\Log;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection{
    public function _construct(){
        $this->_init(\Aidot\Webps\Model\ImageWebpsLog::class,\Aidot\Webps\Model\ResourceModel\ImageWebpsLog::class);
    }

}