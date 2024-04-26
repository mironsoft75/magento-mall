<?php
namespace Webkul\SpinToWin\Controller\Draw;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Silk\Integrations\Model\Session;
use Webkul\SpinToWin\Helper\Data;
use Webkul\SpinToWin\Helper\Task;
use Webkul\SpinToWin\Model\Config\Source\TaskType;

class Info implements HttpGetActionInterface{

    protected $request;
    protected Session $userSession;
    protected Data $helperData;
    protected Task $helperTask;
    protected TaskType $taskType;
    protected JsonFactory $jsonFactory;
    public function __construct(
        Context $context,
        Session $userSession,
        Data $helperData,
        Task $helperTask,
        TaskType $taskType,
        JsonFactory $jsonFactory
    )
    {
        $this->request = $context->getRequest();
        $this->userSession = $userSession;
        $this->helperData = $helperData;
        $this->helperTask = $helperTask;
        $this->taskType = $taskType;
        $this->jsonFactory = $jsonFactory;
    }

    public function execute()
    {
        $spinId = $this->request->getParam('spin_id',null);
        $back= ['code' => 200,'data' => []];
        if(!empty($spinId)){
            if($this->userSession->isLoggedIn()){
                $spinInfo = $this->helperData->getSpinBySpinId($spinId);
                if($spinInfo->hasData()){
                    $freeNum = $spinInfo->getFreeNum();//免费次数
                    $customerId = $this->userSession->getCustomerId();
                    $reportTime = $this->helperData->getReportTime($customerId,$spinId);
                    // 查询赚取次数
                    $taskList = $this->helperTask->findTaskBySpinId($spinId,$customerId)->getItems();
                    $taskTime = count($taskList);
                    $freeNum = ($freeNum + $taskTime) - $reportTime;
                    if($freeNum < 0){
                        $freeNum = 0;
                    }
                    $taskArr = $this->taskType->getTaskType();
                    foreach($taskList as $obj){
                        if(isset($taskArr[$obj->getType()])){
                            $taskArr[$obj->getType()]= 1;
                        }
                    }
                    $back['data']['tasks']=$taskArr;
                    $back['data']['free_num']=$freeNum;
                }else {
                    $back = ['code' => 1001,'msg' => __('Something went wrong in getting spin wheel.')];
                }
            }else {
                $back = ['code' => 1000,'msg' => __('Please login')];
            }
        }else {
            $back = ['code' => 1001,'msg' => __('Something went wrong in getting spin wheel.')];
        }
        return $this->jsonFactory->create()->setData($back);
    }

}