<?php
namespace Webkul\SpinToWin\Model;
use \Magento\Framework\Model\AbstractModel;
use \Magento\Framework\DataObject\IdentityInterface;

class Task extends AbstractModel implements IdentityInterface{
    const CACHE_TAG = 'spintowin_task';
    const ENTITY_ID = 'id';
    public function _construct(){
        $this->_init(\Webkul\SpinToWin\Model\ResourceModel\Task::class);
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