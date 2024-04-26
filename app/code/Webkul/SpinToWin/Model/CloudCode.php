<?php

namespace Webkul\SpinToWin\Model;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;

class CloudCode extends AbstractModel implements IdentityInterface{
    const CACHE_TAG = 'spintowin_cloud_code';
    const ENTITY_ID = 'id';
    public function _construct(){
        $this->_init(\Webkul\SpinToWin\Model\ResourceModel\CloudCode::class);
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