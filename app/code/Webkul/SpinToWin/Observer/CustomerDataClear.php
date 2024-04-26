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
namespace Webkul\SpinToWin\Observer;

use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;

class CustomerDataClear implements \Magento\Framework\Event\ObserverInterface
{

    /**
     * Constructor
     *
     * @param \Magento\Framework\Json\Helper\Data $jsonData
     * @param CookieMetadataFactory $cookieMetadata
     * @param CookieManagerInterface $cookieManager
     * @param SessionManagerInterface $sessionManager
     * @param \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory
     * @param \Webkul\SpinToWin\Helper\Data $helperData
     */
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonData,
        CookieMetadataFactory $cookieMetadata,
        CookieManagerInterface $cookieManager,
        SessionManagerInterface $sessionManager,
        \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory,
        \Webkul\SpinToWin\Helper\Data $helperData
    ) {
        $this->jsonData = $jsonData;
        $this->cookieMetadata = $cookieMetadata;
        $this->cookieManager = $cookieManager;
        $this->sessionManager = $sessionManager;
        $this->reportsFactory = $reportsFactory;
        $this->helperData = $helperData;
    }

    /**
     * Main
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $email =  $observer->getCustomer()->getEmail();
        $isClearCookieData = $this->checkClearCookieData($email);
        if ($isClearCookieData) {
            $this->helperData->clearCookies();
        }
    }

    /**
     * Check Clear Cookie data
     *
     * @param mixed $email
     * @return boolean
     */
    public function checkClearCookieData($email)
    {
        $spintowinCouponCookie = $this->cookieManager->getCookie('spintowin_coupon');
        $flag = true;
        if (!empty($spintowinCouponCookie)) {
            $decodeSpinToWin = $this->jsonData->jsonDecode($spintowinCouponCookie);
            $coupon = isset($decodeSpinToWin['segment']['coupon'])?$decodeSpinToWin['segment']['coupon']:'';
            $report = $this->reportsFactory->create()->getCollection()
            ->addFieldToFilter('coupon', $coupon)
            ->addFieldToFilter('email', $email);
            if ($report->getSize()) {
                $flag = false;
            }
        }
        return $flag;
    }
}
