<?php
namespace Webkul\SpinToWin\Helper;
use Webkul\SpinToWin\Model\ResourceModel\Task\CollectionFactory as TaskCollectionFactory;
use Webkul\SpinToWin\Model\TaskFactory as TaskModelFactory;
use Webkul\SpinToWin\Model\ResourceModel\TaskFactory as TaskResourceFactory;

class Task{

    protected TaskCollectionFactory $taskCollectionFactory;
    protected TaskResourceFactory $taskResourceFactory;
    protected TaskModelFactory $taskModelFactory;
    public function __construct(
        TaskCollectionFactory $taskCollectionFactory,
        TaskResourceFactory $taskResourceFactory,
        TaskModelFactory $taskModelFactory
    )
    {
        $this->taskCollectionFactory = $taskCollectionFactory;
        $this->taskResourceFactory = $taskResourceFactory;
        $this->taskModelFactory = $taskModelFactory;
    }

    public function findTaskBySpinId($spinId,$customerId){
        return $this->taskCollectionFactory->create()->addFieldToFilter('spin_id',$spinId)->addFieldToFilter('customer_id',$customerId);
    }

    public function getTaskSizeBySpinId($spinId,$customerId){
        $collection = $this->findTaskBySpinId($spinId,$customerId);
        return $collection->getSize();
    }

    /**
     * 保存任务完成记录
     */
    public function saveTask($spinId,$customerId,$type){
        $task = $this->taskCollectionFactory->create()->addFieldToFilter('spin_id',$spinId)
            ->addFieldToFilter('customer_id',$customerId)->addFieldToFilter('type',$type)->getFirstItem();
        if($task->hasData()){
            return false;
        }
        $task->setData([
            'spin_id' => $spinId,
            'customer_id' => $customerId,
            'type' => $type
        ]);
        return $this->taskResourceFactory->create()->save($task);    
    }

}