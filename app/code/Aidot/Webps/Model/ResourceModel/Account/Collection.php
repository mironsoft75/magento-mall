<?php
namespace Aidot\Webps\Model\ResourceModel\Account;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection{
    public function _construct(){
        $this->_init(\Aidot\Webps\Model\TinyAccount::class,\Aidot\Webps\Model\ResourceModel\TinyAccount::class);
    }

}