<?php
namespace Webkul\SpinToWin\Model\ResourceModel\Task;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection{
    public function _construct(){
        $this->_init(\Webkul\SpinToWin\Model\Task::class,\Webkul\SpinToWin\Model\ResourceModel\Task::class);
    }

}