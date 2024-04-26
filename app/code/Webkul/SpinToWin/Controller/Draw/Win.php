<?php


namespace Webkul\SpinToWin\Controller\Draw;

use Exception;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Silk\Integrations\Model\Session as UserSession;
use Silk\Integrations\Model\UserLogin;
use Silk\Coupon\Service\CouponSrv;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\Result\JsonFactory;
use Webkul\SpinToWin\Helper\Task;

class Win implements ActionInterface,HttpPostActionInterface
{
    protected $_request;
    /**
     * @var \Webkul\SpinToWin\Helper\Data
     */
    public $helper;

    /**
     * @var \Webkul\SpinToWin\Model\InfoFactory
     */
    public $infoFactory;

    /**
     * @var \Webkul\SpinToWin\Model\ReportsFactory
     */
    public $reportsFactory;

    /**
     * @var \Webkul\SpinToWin\Model\SegmentsFactory
     */
    public $segmentsFactory;

    /**
     * @var \Webkul\SpinToWin\Logger\Logger
     */
    public $logger;    


    public $userSession;
    public $userLogin;
    protected $couponSrv;
    protected Task $taskHelper;
    protected ResourceConnection $resourceConnection;
    protected $jsonFactory;
    /**
     * Constructor
     *
     * @param Context $context
     * @param \Magento\Framework\Json\Helper\Data $jsonData
     * @param \Webkul\SpinToWin\Model\InfoFactory $infoFactory
     * @param \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory
     * @param \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory
     * @param \Webkul\SpinToWin\Helper\Data $helper
     * @param \Webkul\SpinToWin\Logger\Logger $logger
     */
    public function __construct(
        Context $context,
        \Webkul\SpinToWin\Model\InfoFactory $infoFactory,
        \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory,
        \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory,
        \Webkul\SpinToWin\Helper\Data $helper,
        Task $taskHelper,
        \Webkul\SpinToWin\Logger\Logger $logger,
        UserSession $userSession,
        UserLogin $userLogin,
        CouponSrv $couponSrv,
        ResourceConnection $resourceConnection,
        JsonFactory $jsonFactory
    ) {
        $this->_request = $context->getRequest();
        $this->helper = $helper;
        $this->taskHelper = $taskHelper;
        $this->infoFactory = $infoFactory;
        $this->reportsFactory = $reportsFactory;
        $this->segmentsFactory = $segmentsFactory;
        $this->logger = $logger;
        $this->userSession = $userSession;
        $this->userLogin = $userLogin;
        $this->couponSrv = $couponSrv;
        $this->resourceConnection = $resourceConnection;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Checks the Spin
     *
     */
    public function execute()
    {
        $code = 200;
        $result = [];
        if(!$this->userSession->isLoggedIn()){
            $code= 1000;
            $msg= __("Please Login");
        }else {
            try {
                $customer = $this->userSession->getCustomerData();
                $customerId = $customer->getId();
                $nickName = $customer->getFirstname();
                $email = $customer->getEmail();
                $spinId = $this->_request->getParam('spin_id');
                $msg = __('Something went wrong.');
                if (!empty($spinId)) {
                    $spin = $this->infoFactory->create()->load($spinId);
                    if(!$spin->hasData()){
                        throw new Exception("Spin not found");
                    }
                    $spinId = $spin->getId();
                    $freeNum = $spin->getFreeNum();//免费次数
                    $reportTime = $this->helper->getReportTime($customerId,$spinId);
                    // 查询赚取次数
                    $taskTime = $this->taskHelper->getTaskSizeBySpinId($spinId,$customerId);
                    if($reportTime < ($freeNum + $taskTime)){
                        $segmentDetail = $this->calculateResult($spin);
                        $segmentId = $segmentDetail['segmentid'];
                        if ($segmentId) {
                            $connection = $this->resourceConnection->getConnection();
                            $connection->beginTransaction();
                            try{
                                
                                $segment = $this->segmentsFactory->create()->load($segmentId);
                                $msg = __('Successfully spinned.');
                                $result['type'] = $segment->getType();
                                $result['spin_type'] = $segment->getSpinType();
                                $result['segment_id'] = $segment->getId();
                                $report = $this->reportsFactory->create();
                                $status = 0;
                                $spinType = $segment->getSpinType();
                                $report->setSpinType($spinType);
                                $report->setSegmentImage($segment->getImage());
                                $report->setCreatedAt($this->helper->getCurrentDateTime());
                                if ($segment->getType()) {
                                    if(1 == $spinType){
                                        $coupon = $this->couponSrv->createGeneratorCouponByRuleId($segment->getRuleId(),$customerId);
                                        // collected
                                        $report->setSegmentUrl($segment->getDescription());
                                        $report->setCoupon($coupon->getCode());
                                        $result['coupon_code'] = $coupon->getCode();
                                        $result['segment_url'] = $segment->getDescription();
                                        $result['nickname'] = $this->helper->ceshi($nickName);
                                    }elseif(2 == $spinType){//product
                                        $report->setSegmentProductSku($segment->getProductSku());
                                        $result['spin_product_sku']= $segment->getProductSku();
                                    }else {//win point
                                        $win_point = $segment->getPoint();
                                        $report->setSegmentPoint($win_point);
                                        // 请求添加积分
                                        $lastPoint  = $this->userLogin->setPointsEncrypt([[
                                            'channel' => 'Shop',
                                            'type' => 'gain_points_by_lucky_draw',
                                            'integral' => $win_point,
                                            'businessId' =>'gain_points_by_lucky_draw_'.time()
                                        ]],$this->userSession->getAppUserId());
                                        $result['spin_point'] = $win_point;
                                        $result['point'] = $lastPoint;
                                    }
                                    $status = 1;
                                }
                                $report->setSpinId($spinId);
                                $report->setCustomerId($customerId);
                                $report->setEmail($email);
                                $report->setName($nickName);
                                $report->setResult($segment->getType());
                                $report->setStatus($status);
                                $report->setSegmentId($segment->getId());
                                $report->setSegmentLabel($segment->getLabel());
                                $report->save();
                                $result['reports_id'] = $report->getId();
                                $segment->setAvailed($segment->getAvailed() + 1);
                                $segment->save();
                                $this->checkIfSpinAvailable($spinId);
                                $connection->commit();
                            }catch(Exception $e){
                                $connection->rollBack();
                                $code = 1001;
                                $msg = __('Something went wrong in getting spin wheel.');
                                $this->logger->info("win: ".$e->getMessage());
                            }
    
                        } else {
                            $infoModel = $this->infoFactory->create()->load($spin->getId());
                            $infoModel->setStatus(0)->save();
                            $msg = __('Something went wrong.');
                        }
                    }else {
                        //次数不够
                        $code= 1002;
                        $msg= __("Not enough times");
                    }
                    
                }else {
                    throw new Exception("Spin not found");
                }
    
            }catch (\Exception $e) {
                $code = 1001;
                $msg = __('Something went wrong in getting spin wheel.');
            }
        }
        
        $back= ['code' => $code,'msg' => $msg,'data' => $result];
        $resultJson = $this->jsonFactory->create()->setData($back);
        return $resultJson;
    }

    /**
     * Calculate Result
     *
     * @param \Webkul\SpinToWin\Model\InfoFactory $spin
     * @return array
     */
    public function calculateResult($spin)
    {
        $segmentsArray = [];
        $segmentId = 0;
        $sum = 0;
        $result = [];
        $segments = $spin->getSegments()->setOrder('position', 'ASC');
        $i = 0;
        foreach ($segments as $segment) {
            $i++;
            $segmentsArray[$segment->getId()] = $i;
            if ($segment->getLimits()===null
            || ($segment->getLimits()!==null
            && $segment->getAvailed()<$segment->getLimits())) {
                $sum += $segment->getGravity();
                $result[$segment->getId()] = $sum;
            }
        }
        $indx = 0;
        if (!empty($result)) {
            $random = random_int(0, max($result) - 1);
            foreach ($result as $key => $value) {
                if ($random < $value) {
                    $segmentId = $key;
                    $indx = $segmentsArray[$segmentId];
                    break;
                }
            }
        }
        return ['segmentid'=>$segmentId, 'segment'=>$indx];
    }

    /**
     * To check if any more spin available
     *
     * @param int $spinId
     * @return void
     */
    public function checkIfSpinAvailable($spinId)
    {
        $spin = $this->infoFactory->create()->load($spinId);
        $isAvailable = false;
        $segments = $spin->getSegments();
        foreach ($segments as $segment) {
            if ($segment->getLimits()===null
            || ($segment->getLimits()!==null
            && $segment->getAvailed()<$segment->getLimits())) {
                $isAvailable = true;
                break;
            }
        }
        if (!$isAvailable) {
            $spin->setStatus(0)->save();
        }
    }




}
