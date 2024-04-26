<?php


namespace Webkul\SpinToWin\Controller\Ajax;

use Exception;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\SalesRule\Model\CouponGenerator;
use Magento\Framework\Session\SessionManagerInterface;
use Silk\Integrations\Model\Session as UserSession;
use Silk\Integrations\Model\UserLogin;
use Magento\Framework\Exception\LocalizedException;
use Silk\Coupon\Service\CouponSrv;
use Silk\Coupon\Api\CustomerDataInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Controller\ResultFactory;

class Win implements ActionInterface,HttpPostActionInterface
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
     * @var Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    public $cookieMetadata;

    /**
     * @var Magento\Framework\Stdlib\CookieManagerInterface
     */
    public $cookieManager;

    /**
     * @var Magento\Framework\Session\SessionManagerInterface
     */
    public $sessionManager;

    public $userSession;
    public $userLogin;
    protected $couponSrv;
    protected $customerDataInterface;
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
     * @param CouponGenerator $couponGenerator
     * @param CookieMetadataFactory $cookieMetadata
     * @param CookieManagerInterface $cookieManager
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
        CookieMetadataFactory $cookieMetadata,
        CookieManagerInterface $cookieManager,
        SessionManagerInterface $sessionManager,
        \Webkul\SpinToWin\Logger\Logger $logger,
        UserSession $userSession,
        UserLogin $userLogin,
        CouponSrv $couponSrv,
        CustomerDataInterface $customerDataInterface,
        ResourceConnection $resourceConnection,
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
        $this->cookieMetadata = $cookieMetadata;
        $this->cookieManager = $cookieManager;
        $this->sessionManager = $sessionManager;
        $this->userSession = $userSession;
        $this->userLogin = $userLogin;
        $this->couponSrv = $couponSrv;
        $this->customerDataInterface = $customerDataInterface;
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
        try {
            if(!$this->userSession->isLoggedIn()){
                throw new Exception(__("Please login"));
            }
            $customer = $this->userSession->getCustomerData();
            $customerId = $customer->getId();
            $nickName = $customer->getFirstname();
            $email = $customer->getEmail();

            $data = $this->_request->getParams();
            $msg = __('Something went wrong.');
            if (!empty($data['spin_id'])) {
                $spin = $this->infoFactory->create()->load($data['spin_id']);
                if(!$spin->hasData()){
                    throw new Exception("Spin not found");
                }
                $spinId = $spin->getId();
                $freeNum = $spin->getFreeNum();//免费次数
                $isSpinned = $this->helper->getTodayTime($customerId,$spinId);
                $isSpin = 0;
                if($isSpinned < $freeNum){
                    $isSpin = 1;
                }else {
                    if(isset($data['spin_point']) && $data['spin_point']){
                        $isSpin = 2;
                    }else {
                        $code = 1003;
                        $msg = __("Cost %1 Points to Draw",$spin->getPoint());
                    }
                }
                if ($isSpin) {
                    $segmentDetail = $this->calculateResult($spin);
                    $segmentId = $segmentDetail['segmentid'];
                    $result['reduce_point'] = false;
                    if ($segmentId) {
                        $connection = $this->resourceConnection->getConnection();
                        $connection->beginTransaction();
                        try{
                            if(2 == $isSpin){
                                $reduce_point = $spin->getPoint();
                                // 扣积分
                                $lastPoint  = $this->userLogin->setPointsEncrypt([[
                                    'channel' => 'Shop',
                                    'type' => 'cost_points_for_lucky_draw',
                                    'integral' => -$reduce_point,
                                    'businessId' =>'cost_points_for_lucky_draw_'.time()
                                ]],$this->userSession->getAppUserId());
                                $result['point'] = $lastPoint;
                                $result['reduce_point'] = true;
                            }
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
                                    $couponSpecData = [
                                        'rule_id' => $segment->getRuleId(),
                                        'qty' => 1,
                                        'length' => 12,
                                        'format' => 'alphanum',
                                    ];
                                    $coupon = $this->couponGenerator->generateCodes($couponSpecData)[0];
                                    $result['spin_coupon']= $coupon;
                                    $couponRule = $this->couponSrv->findCouponInfoByCouponCode($coupon);
                                    $this->customerDataInterface->saveCollectRecord($customerId,$couponRule->getCouponId());
                                    // collected
                                    $report->setCoupon($coupon);
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
                        }catch(LocalizedException $e){
                            $this->logger->info("win: ".$e->getMessage());
                            $msg = __("Each draw will cost %1 points. and you don`t have enough points",$reduce_point);
                            $code = 1002;
                            $connection->rollBack();
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
                }
            }else {
                throw new Exception("Spin not found");
            }

        }catch (\Exception $e) {
            $code = 1001;
            $msg = __('Something went wrong in getting spin wheel.');
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
