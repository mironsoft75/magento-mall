<?php

namespace Webkul\SpinToWin\Model;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;

class ProductAddress extends AbstractModel implements IdentityInterface{
    const CACHE_TAG = 'spintowin_product_address';
    const ENTITY_ID = 'address_id';
    public function _construct(){
        $this->_init(\Webkul\SpinToWin\Model\ResourceModel\ProductAddress::class);
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