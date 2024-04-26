<?php


namespace Webkul\SpinToWin\Controller\Win;

use Exception;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\SalesRule\Model\CouponGenerator;
use Magento\Framework\Session\SessionManagerInterface;
use Silk\Integrations\Model\Session as UserSession;
use Silk\Integrations\Model\UserLogin;
use Magento\Framework\Exception\LocalizedException;
use Silk\Referral\Service\SubscriberSrv;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultFactory;
use Webkul\SpintoWin\Model\ResourceModel\Code\CollectionFactory as CodeCollectionFactory;
use Webkul\SpintoWin\Model\ResourceModel\Reports\CollectionFactory as ReportCollectionFactory;
use Webkul\SpintoWin\Helper\Email as EmailHelper;

class Draw implements ActionInterface,HttpPostActionInterface
{
    protected $_request;
    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    public $jsonData;

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

    /**
     * @var Magento\SalesRule\Model\CouponGenerator
     */
    public $couponGenerator;


    /**
     * @var Magento\Framework\Session\SessionManagerInterface
     */
    public $sessionManager;

    public $userSession;
    public $userLogin;
    protected SubscriberSrv $subscriberSrv;
    protected ResourceConnection $resourceConnection;
    protected CodeCollectionFactory $codeCollectionFactory;
    protected ReportCollectionFactory $reportCollectionFactory;
    protected EmailHelper $emailHelper;
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
     * @param CouponGenerator $couponGenerator
     * @param SessionManagerInterface $sessionManager
     * @param \Webkul\SpinToWin\Logger\Logger $logger
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Webkul\SpinToWin\Model\InfoFactory $infoFactory,
        \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory,
        \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory,
        \Webkul\SpinToWin\Helper\Data $helper,
        CouponGenerator $couponGenerator,
        SessionManagerInterface $sessionManager,
        \Webkul\SpinToWin\Logger\Logger $logger,
        UserSession $userSession,
        UserLogin $userLogin,
        SubscriberSrv $subscriberSrv,
        ResourceConnection $resourceConnection,
        CodeCollectionFactory $codeCollectionFactory,
        ReportCollectionFactory $reportCollectionFactory,
        EmailHelper $emailHelper,
        ResultFactory $jsonFactory
    ) {
        $this->_request = $context->getRequest();
        $this->jsonData = $jsonData;
        $this->helper = $helper;
        $this->infoFactory = $infoFactory;
        $this->reportsFactory = $reportsFactory;
        $this->segmentsFactory = $segmentsFactory;
        $this->logger = $logger;
        $this->couponGenerator = $couponGenerator;
        $this->sessionManager = $sessionManager;
        $this->userSession = $userSession;
        $this->userLogin = $userLogin;
        $this->subscriberSrv = $subscriberSrv;
        $this->resourceConnection = $resourceConnection;
        $this->codeCollectionFactory = $codeCollectionFactory;
        $this->reportCollectionFactory = $reportCollectionFactory;
        $this->emailHelper = $emailHelper;
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
        try {
            $data = $this->_request->getParams();
            $msg = '';
            if (!empty($data['spin_id']) && !empty($data['email'])) {
                $email = $data['email'];
                $spin = $this->infoFactory->create()->load($data['spin_id']);
                if(!$spin->hasData()){
                    throw new Exception("Something went wrong in getting spin wheel.");
                }
                if(strtotime($spin->getStartDate()) > time()){
                    throw new Exception(__("Spring Sale is about to start."));
                }
                $spinId = $spin->getId();
                $reportModel = $this->reportCollectionFactory->create()->addFieldToFilter('email',$email)->addFieldToFilter('spin_id',$spinId)->getFirstItem();
                if($reportModel->hasData()){
                    throw new Exception(__("Each email address can only subscribe once."));
                }
                //todo 订阅判断
                $customerEmail = '';
                $customerId = 0;
                if($this->userSession->isLoggedIn()){
                    $customerId = $this->userSession->getId();
                    $customerEmail = $this->userSession->getCustomerData()->getEmail();
                }
                $this->subscriberSrv->subscriber($email,$customerId,$customerEmail);
                
                $segmentDetail = $this->calculateResult($spin);
                $segmentId = $segmentDetail['segmentid'];
                if ($segmentId) {
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
                        if(1 == $spinType){//coupon
                            $couponSpecData = [
                                'rule_id' => $segment->getRuleId(),
                                'qty' => 1,
                                'length' => 12,
                                'format' => 'alphanum',
                            ];
                            $coupon = $this->couponGenerator->generateCodes($couponSpecData)[0];
                            $result['spin_coupon']= $coupon;
                            $segment->setCode($coupon);
                            // collected
                            $report->setCoupon($coupon);
                        }elseif(2 == $spinType){//product
                            $report->setSegmentProductSku($segment->getProductSku());
                            $result['spin_product_sku']= $segment->getProductSku();
                        }elseif(4 == $spinType) {//win cloud server
                            $cloudType= $segment->getCloudType();
                            $codeArr = $this->codeCollectionFactory->create()->addFieldToFilter('spin_id',$spinId)->
                                    addFieldToFilter('cloud_type',$cloudType)->addFieldToFilter('status',0);
                            $codeCount = count($codeArr->getItems());
                            if(!$codeCount){
                                $segment->setAvailed($segment->getLimits())->save();
                                throw new Exception("Something went wrong,please try again");
                            }else {
                                $codeObj = $codeArr->getFirstItem();
                                $result['cloud_code']= $codeObj->getCode();
                                $segment->setCode($codeObj->getCode());
                            }
                        }
                        $status = 1;
                    }
                    $connection = $this->resourceConnection->getConnection();
                    $connection->beginTransaction();
                    try{
                        $report->setSpinId($spinId);
                        $report->setEmail($email);
                        $report->setName($email);
                        $report->setResult($segment->getType());
                        $report->setStatus($status);
                        $report->setSegmentId($segment->getId());
                        $report->setSegmentLabel($segment->getLabel());
                        $report->save();
                        $result['reports_id'] = $report->getId();
                        if(!empty($codeObj)){
                            $codeObj->setReportsId($report->getId());
                            $codeObj->setStatus(1);
                            $codeObj->save();
                        }
                        $segment->setAvailed($segment->getAvailed() + 1);
                        $segment->save();
                        $this->checkIfSpinAvailable($spinId);
                        $connection->commit();
                        if(1 == $spinType || 4 == $spinType){//如果是优惠券或者云服务 发送邮件
                            $this->emailHelper->sendCouponNotification($email,$email,$segment);
                        }
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
                throw new Exception("Spin not found");
            }

        }catch (\Exception $e) {
            $code = 1001;
            $msg = __($e->getMessage());
        }
        $back= ['code' => $code,'msg' => $msg,'data' => $result];
        $resultJson = $this->jsonFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($back);
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
