<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\SpinToWin\Helper;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * Constructor
     *
     * @param \Webkul\SpinToWin\Logger\Logger $logger
     * @param \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory
     * @param \Webkul\SpinToWin\Model\WheelFactory $wheelFactory
     * @param \Webkul\SpinToWin\Model\InfoFactory $infoFactory
     * @param \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $store
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Filesystem\Io\File $io
     * @param \Magento\Framework\Filesystem\DirectoryList $directoryList
     * @param \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase
     * @param \Magento\Framework\Filesystem $filesystem
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param CookieMetadataFactory $cookieMetadata
     * @param CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Json\Helper\Data $jsonData
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\SalesRule\Model\CouponFactory $couponFactory
     * @param \Magento\SalesRule\Model\RuleFactory $saleRuleFactory
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(
        \Webkul\SpinToWin\Logger\Logger $logger,
        \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory,
        \Webkul\SpinToWin\Model\WheelFactory $wheelFactory,
        \Webkul\SpinToWin\Model\InfoFactory $infoFactory,
        \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory,
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $store,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Filesystem\Io\File $io,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\MediaStorage\Helper\File\Storage\Database $coreFileStorageDatabase,
        \Magento\Framework\Filesystem $filesystem,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        CookieMetadataFactory $cookieMetadata,
        CookieManagerInterface $cookieManager,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\SalesRule\Model\RuleFactory $saleRuleFactory,
        SessionManagerInterface $sessionManager
    ) {
        $this->logger = $logger;
        $this->segmentsFactory = $segmentsFactory;
        $this->wheelFactory = $wheelFactory;
        $this->infoFactory = $infoFactory;
        $this->reportsFactory = $reportsFactory;
        $this->store = $store;
        $this->timezone = $timezone;
        $this->jsonHelper = $jsonHelper;
        $this->cacheTypeList = $cacheTypeList;
        $this->directoryList = $directoryList;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->cookieMetadata = $cookieMetadata;
        $this->cookieManager = $cookieManager;
        $this->jsonData = $jsonData;
        $this->couponFactory = $couponFactory;
        $this->saleRuleFactory = $saleRuleFactory;
        $this->coreFileStorageDatabase = $coreFileStorageDatabase;
        $this->sessionManager = $sessionManager;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA);
        parent::__construct($context);
    }

    /**
     * Get Layout Wheel View
     *
     * @return array
     */
    public function getLayoutWheelView()
    {
        $layoutWheelView = [
            'full' => __('Full View'),
            'split' => __('Split View')
        ];
        return $layoutWheelView;
    }

    /**
     * Get Layout Trigger Button Position
     *
     * @return array
     */
    public function getLayoutTriggerButtonPosition()
    {
        $layoutTriggerButtonPosition = [
            'bottom-right' => __('Bottom Right'),
            'top-right' => __('Top Right'),
            'bottom-left' => __('Bottom Left'),
            'top-left' => __('Top Left'),
            'middle-left' => __('Middle Left'),
            'middle-right' => __('Middle Right')
        ];
        return $layoutTriggerButtonPosition;
    }

    /**
     * Get Layout Position
     *
     * @return array
     */
    public function getLayoutPosition()
    {
        $layoutPosition = [
            'left' => __('Left'),
            'right' => __('Right')
        ];
        return $layoutPosition;
    }

    /**
     * Get Pages
     *
     * @return array
     */
    public function getPages()
    {
        $pages = [
            'cms-index-index' => __('Home Page'),
            'customer-account-login' => __('Login Page'),
            'customer-account-create' => __('Registration Page'),
            'catalog-product-view' => __('Product View Page'),
            'catalog-category-view' => __('Category Page'),
            'catalogsearch-result-index' => __('Product Search Page'),
            'checkout-cart-index' => __('Cart Page'),
            'checkout-index-index' => __('Checkout Page'),
            'other' => __('Other pages')
        ];
        return $pages;
    }

    /**
     * Get Events
     *
     * @return array
     */
    public function getEvents()
    {
        $events = [
            'after' => __('After x seconds'),
            'immediate' => __('Immediately'),
            'scroll' => __('When scroll to x %'),
            'exit' => __('When Exit')
        ];
        return $events;
    }

    /**
     * Get Allow
     *
     * @return array
     */
    public function getAllow()
    {
        $allow = [
            '0' => __("All"),
            '1' => __('Select Specifics')
        ];
        return $allow;
    }

    /**
     * Get Spin to win Button Image
     *
     * @return array
     */
    public function getSpintowinButtonImage()
    {
        $spintowinButtonImage = [
            'red' => 'spintowin/image/red.png',
            'yellow' => 'spintowin/image/yellow.png',
            'green' => 'spintowin/image/green.png',
            'purple' => 'spintowin/image/purple.png',
            'blue' => 'spintowin/image/blue.png',
        ];
        return $spintowinButtonImage;
    }

    /**
     * Get Spin to win Pin Image
     *
     * @return array
     */
    public function getSpintowinPinImage()
    {
        $spintowinPinImage = [
            'red' => 'spintowin/image/red_pin.png',
            'green' => 'spintowin/image/green_pin.png',
            'yellow' => 'spintowin/image/yellow_pin.png',
            'purple' => 'spintowin/image/purple_pin.png',
        ];
        return $spintowinPinImage;
    }

    /**
     * Get Layout View
     *
     * @return array
     */
    public function getLayoutView()
    {
        $layoutView = [
            'popup' => __('Pop Up Dialog'),
            'slide' => __('Sidebar Slide')
        ];
        return $layoutView;
    }

    /**
     * Get Background Repeat Properties
     *
     * @return array
     */
    public function getBackgroundRepeatProperties()
    {
        $backgroundRepeatProperties = [
            'repeat' => __('Repeat'),
            'repeat-x' => __('Repeat Horizontal'),
            'repeat-y' => __('Repeat Vertical'),
            'no-repeat' => __('No Repeat'),
            'space' => __('Space'),
            'round' => __('Round'),
            'initial' => __('Initial'),
        ];
        return $backgroundRepeatProperties;
    }

    /**
     * Media Directory path
     *
     * @return string
     */
    public function getMediaDirectory()
    {
        return $this->store->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    }

    /**
     * Current Date Time
     *
     * @return datetime
     */
    public function getCurrentDateTime()
    {
        // return $this->timezone->date()->format('Y-m-d H:i:s');
        return date('Y-m-d H:i:s');
    }

     /**
      * Current Date Time
      *
      * @return datetime
      */
    public function getCurrentDate()
    {
        return $this->timezone->date()->format('Y-m-d');
    }

    /**
     * Flush Cache
     */
    public function cacheFlush()
    {
        $types = ['full_page'];
        foreach ($types as $type) {
            $this->cacheTypeList->cleanType($type);
        }
    }

   /**
    * Check coupon validity
    *
    * @param string $couponCode
    * @return array
    */
    public function checkValidCoupon($couponCode)
    {
        $msg = '';
        $success = true;
        $couponCode = trim($couponCode);
        if ($couponCode) {
            $ruleId =  $this->couponFactory->create()->loadByCode($couponCode)->getRuleId();
            $rule = $this->saleRuleFactory->create()->load($ruleId);
            if ($rule->getSegmentId()) {
                $this->checkoutSession->setWkSpinRule($ruleId);
                if ($this->scopeConfig->getValue('spintowin/general/email_validation')) {
                    if ($this->customerSession->isLoggedIn()) {
                        $email = $this->customerSession->getCustomer()->getEmail();
                        $reports = $this->reportsFactory->create()
                                            ->getCollection()
                                            ->addFieldToFilter('coupon', $couponCode);
                       
                        if ($reports->getSize()) {
                            
                            $report = $reports->getLastItem();
                            list($success,$msg) =  $this->isValidUser($report->getEmail(), $email);
                           
                        }
                    } else {
                        $success = false;
                        $msg = __('You need to log in to use this Coupon.');
                    }
                }
            }
        }
        return ['success'=>$success, 'msg'=>$msg];
    }

    /**
     * Is Valid User
     *
     * @param mixed $reportEmail
     * @param mixed $email
     * @return void
     */
    public function isValidUser($reportEmail, $email)
    {
        $success = true;
        $msg = '';
        if ($reportEmail != $email) {
            $success = false;
            $msg = __('You are not authorised to use this Coupon.');
           
        }
        return [$success,$msg];
    }

    /**
     * Save File
     *
     * @param string $fileName
     *
     * @return string
     */
    public function saveFile($fileName)
    {
        $newFileName = substr_replace($fileName, time(), strrpos($fileName, '.'), 0);
        $baseTmpImagePath = $this->getFilePath($this->directoryList->getPath('media')
                    .'/tmp/catalog/product', $fileName);
        $baseImagePath = $this->getFilePath($this->directoryList->getPath('media')
                    .'/spintowin', $newFileName);
        $this->coreFileStorageDatabase->copyFile(
            $baseTmpImagePath,
            $baseImagePath
        );
        $this->mediaDirectory->renameFile(
            $baseTmpImagePath,
            $baseImagePath
        );
        return $newFileName;
    }

    /**
     * Get Path
     *
     * @param string $path
     * @param string $imageName
     *
     * @return void
     */
    public function getFilePath($path, $imageName)
    {
        return rtrim($path, '/') . '/' . ltrim($imageName, '/');
    }

    /**
     * Get Wheel Data
     *
     * @param int $spinId
     *
     * @return \Webkul\SpinToWin\Model\WheelFactory
     */
    public function getWheelData($spinId)
    {
        $wheel = $this->wheelFactory->create()->load($spinId, 'spin_id');
        $segmentsLabel = $this->segmentsFactory->create()
                                        ->getCollection()
                                        ->addFieldToFilter('spin_id', $spinId)
                                        ->setOrder('position', 'ASC')
                                        ->getColumnValues('label');
        $wheel->setSegmentsLabel($segmentsLabel);
        $wheel->setMediaUrl($this->getMediaDirectory());
        return $wheel;
    }

    /**
     * Get Spin To Show
     *
     * @return \Webkul\SpinToWin\Model\InfoFactory
     */
    public function getSpin()
    {
        $notInclude = $this->jsonData->jsonDecode(
            $this->cookieManager->getCookie('spintowin_spins') ?
                    $this->cookieManager->getCookie('spintowin_spins'): "[0]"
        );
        $this->unsetCookieForExpireCoupon();
       
        if ($this->customerSession->isLoggedIn()) {
            $email = $this->customerSession->getCustomer()->getEmail();
            $tempnotInclude = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('email', $email)
                                ->getColumnValues('spin_id');
            $notInclude = array_merge($notInclude, $tempnotInclude);
        }
        if (empty($notInclude)) {
            $notInclude = [0];
        }
        $currentDateTime = $this->getCurrentDateTime();
        $spin = $this->infoFactory->create();
        $collection = $this->infoFactory->create()->getCollection()
                                    ->addFieldToFilter(
                                        ['start_date', 'start_date'],
                                        [
                                            ['null' => true],
                                            ['lteq' => $currentDateTime]
                                        ]
                                    )
                                    ->addFieldToFilter(
                                        ['end_date', 'end_date'],
                                        [
                                            ['null' => true],
                                            ['gt' => $currentDateTime]
                                        ]
                                    )
                                    ->addFieldToFilter('status', 1)
                                    ->addFieldToFilter('entity_id', ['nin'=>$notInclude])
                                    ->setOrder('priority', 'DESC');
        foreach ($collection as $spinModel) {
            if (in_array($this->store->getStore()->getWebsiteId(), explode(',', $spinModel->getWebsiteIds()))) {
                if ($spinModel->getSegments()->getSize()) {
                    $spin = $spinModel;
                    break;
                }
            }
        }
        return $spin;
    }

    /**
     * Get Spin Ids
     *
     * @return array
     */
    public function getSpinIds()
    {
        $spinIds = [];
        $notInclude = [];
        if ($this->customerSession->isLoggedIn()) {
            $email = $this->customerSession->getCustomer()->getEmail();
            $notInclude = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('email', $email)
                                ->getColumnValues('spin_id');
        }
        if (empty($notInclude)) {
            $notInclude = [0];
        }
        $currentDateTime = $this->getCurrentDateTime();
        $collection = $this->infoFactory->create()->getCollection()
                                    ->addFieldToFilter(
                                        ['start_date', 'start_date'],
                                        [
                                            ['null' => true],
                                            ['lteq' => $currentDateTime]
                                        ]
                                    )
                                    ->addFieldToFilter(
                                        ['end_date', 'end_date'],
                                        [
                                            ['null' => true],
                                            ['gt' => $currentDateTime]
                                        ]
                                    )
                                    ->addFieldToFilter('status', 1)
                                    ->addFieldToFilter('entity_id', ['nin'=>$notInclude])
                                    ->setOrder('priority', 'DESC');
        foreach ($collection as $spinModel) {
            if (in_array($this->store->getStore()->getWebsiteId(), explode(',', $spinModel->getWebsiteIds()))) {
                if ($spinModel->getSegments()->getSize()) {
                    $spinIds[] = $spinModel->getId();
                }
            }
        }
        return $spinIds;
    }

    /**
     * Unset Cookies
     *
     * @return void
     */
    public function unsetCookieForExpireCoupon()
    {
        $spintowinCoupon = $this->jsonData->jsonDecode(
            $this->cookieManager->getCookie('spintowin_coupon') ?
                    $this->cookieManager->getCookie('spintowin_coupon'):"[]"
        );
        
        $currentDateTime = $this->getCurrentDate();
        $coupon = isset($spintowinCoupon['segment']['coupon'])?$spintowinCoupon['segment']['coupon']:'';
       
        $salesrule = $this->couponFactory->create()->getCollection();
        $salesrule->join(
            ["salesrule" => $salesrule->getTable("salesrule")],
            'main_table.rule_id = salesrule.rule_id'
        )->addFieldToFilter('main_table.code', ['eq'=>$coupon]);
        $salesrule->getSelect()->where('salesrule.to_date >= '."'$currentDateTime'");
       
        if (!$salesrule->getSize()) {
            $this->clearCookies();
        }
    }

    /**
     * Clear Cookies
     *
     * @return void
     */
    public function clearCookies()
    {
        $metadata = $this->cookieMetadata->createPublicCookieMetadata()
        ->setDuration(-3600)
        ->setPath($this->sessionManager->getCookiePath())
        ->setDomain($this->sessionManager->getCookieDomain());
        $this->cookieManager->setPublicCookie(
            'spintowin_spins',
            $this->jsonData->jsonEncode("[]"),
            $metadata
        );

        $this->cookieManager->setPublicCookie(
            'spintowin_coupon',
            $this->jsonData->jsonEncode("[]"),
            $metadata
        );
    }

    public function getSpinBySpinId($spinId=0,$source_type=0){
        $currentDateTime = $this->getCurrentDateTime();
        // $spin = $this->infoFactory->create();
        $collection = $this->infoFactory->create()->getCollection()
                
                ->addFieldToFilter(
                ['end_date', 'end_date'],
                [
                    ['null' => true],
                    ['gt' => $currentDateTime]
                ]
            )
            ->addFieldToFilter('status', 1)
            ->setOrder('priority', 'DESC')->setPageSize(1);
        
        if(!empty($spinId)){
            $collection->addFieldToFilter('entity_id',$spinId);
            $collection->addFieldToFilter(
                ['start_date', 'start_date'],
                [
                    ['null' => true],
                    ['lteq' => $currentDateTime]
                ]
            );
        }else {
            if(1 == $source_type){//如果是APP
                $collection->addFieldToFilter(
                    ['start_date', 'start_date'],
                    [
                        ['null' => true],
                        ['lteq' => $currentDateTime]
                    ]
                );
            }
            $collection->addFieldToFilter('source_type',$source_type);
        }
        $spin = $collection->getFirstItem();
        $spin->getSegments();
        return $spin;
    }
    public function getTodayTime($customerId,$spinId){
        // $currentDate = $this->timezone->date();
        $startTime = date('Y-m-d 00:00:00');
        $endTime = date('Y-m-d 23:59:59');
        $count = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('spin_id', $spinId)
                                ->addFieldToFilter('customer_id', $customerId)
                                ->addFieldToFilter('timestamp',['lteq' => $endTime])
                                ->addFieldToFilter('timestamp',['gteq' => $startTime])
                                ->getSize();
        return $count;                        
    }

    /**
     * 获取用户转盘抽奖次数
     */
    public function getReportTime($customerId,$spinId){
        $count = $this->reportsFactory->create()
            ->getCollection()
            ->addFieldToFilter('spin_id', $spinId)
            ->addFieldToFilter('customer_id', $customerId)
            ->getSize();
        return $count;
    }

    public function ceshi($username){
        // 计算字符串长度，无论汉字还是英文字符全部为1
        $length = mb_strlen($username,'utf-8');
        // 截取第一部分代码
        $firstStr1 = mb_substr($username,0,ceil($length/4),'utf-8');
        // 截取中间部分代码
        $firstStr = mb_substr($username,ceil($length/4),floor($length/2),'utf-8');
        // （方法一）截取剩余字符串
        $firstStr2 = mb_substr($username,ceil($length/4) + floor($length/2), floor(($length+1)/2 - 1),'utf-8');
        
        return $firstStr1 . str_repeat("*",mb_strlen($firstStr,'utf-8')) . $firstStr2;
    }
    
}
