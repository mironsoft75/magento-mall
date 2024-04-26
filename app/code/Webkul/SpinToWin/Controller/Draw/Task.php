<?php

namespace Webkul\SpinToWin\Controller\Draw;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\SpinToWin\Helper\Task as HelperTask;
use Silk\Integrations\Model\Session as UserSession;
use Webkul\SpinToWin\Helper\Data as HelperData;

class Task implements ActionInterface,HttpPostActionInterface{

    protected $_request;
    protected HelperTask $helperTask;
    protected UserSession $userSession;
    protected HelperData $helperData;
    protected JsonFactory $jsonFactory;
    public function __construct(
        Context $context,
        HelperTask $helperTask,
        UserSession $userSession,
        HelperData $helperData,
        JsonFactory $jsonFactory
    )
    {
        $this->_request = $context->getRequest();
        $this->helperTask = $helperTask;
        $this->userSession = $userSession;
        $this->helperData = $helperData;
        $this->jsonFactory = $jsonFactory;
    }

    public function execute()
    {
        $code = 200;
        $msg = __("Success");
        $result = [];
        if(!$this->userSession->isLoggedIn()){
            $code= 1000;
            $msg= __("Please Login");
        }else {
            $customerId = $this->userSession->getCustomerId();
            $spinId = $this->_request->getParam('spin_id');
            $spinInfo = $this->helperData->getSpinBySpinId($spinId);
            if($spinInfo->hasData()){
                $type = $this->_request->getParam('type');
                $result = $this->helperTask->saveTask($spinId,$customerId,$type);
                if(!$result){
                    $code= 1001;
                    $msg= __("Task already Have been completed");
                }
            }else{
                $code= 1001;
                $msg= __('Something went wrong in getting spin wheel.');
            }
        }
        return $this->jsonFactory->create()->setData(['code' => $code,'msg' => $msg]);

    }

}