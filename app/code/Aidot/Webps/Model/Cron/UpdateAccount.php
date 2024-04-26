<?php
namespace Aidot\Webps\Model\Cron;
use Aidot\Webps\Model\ResourceModel\Account\CollectionFactory;
/**
 * 更新账号免费使用次数
 */

class UpdateAccount {

    protected CollectionFactory $collectionFactory;
    public function __construct(
        CollectionFactory $collectionFactory
    )
    {
        $this->collectionFactory = $collectionFactory;
    }

    public function execute(){
        $collection = $this->collectionFactory->create();
        $items = $collection->getItems();
        foreach($items as $item){
            $item->setFreeNum(498)->save();
        }
        return $this;
    }

}