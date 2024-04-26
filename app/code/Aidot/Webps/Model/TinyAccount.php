<?php
namespace Aidot\Webps\Model;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;

class TinyAccount extends AbstractModel implements IdentityInterface{
    const CACHE_TAG = 'tiny_account';
    const ENTITY_ID = 'id';
    public function _construct(){
        $this->_init(\Aidot\Webps\Model\ResourceModel\TinyAccount::class);
    }

    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

}