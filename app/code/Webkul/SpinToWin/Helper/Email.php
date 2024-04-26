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

use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Customer\Model\Session;

class Email extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Magento\Customer\Model\Session
     */
    public $customerSession;
    
    /**
     * @var Magento\Framework\Translate\Inline\StateInterface
     */
    public $inlineTranslation;
    
    /**
     * @var Magento\Framework\Mail\Template\TransportBuilder
     */
    public $transportBuilder;
    
    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    public $storeManager;
    
    /**
     * @var Magento\Customer\Api\CustomerRepositoryInterface
     */
    public $customer;
    
    /**
     * @var \Webkul\SpinToWin\Logger\Logger
     */
    public $logger;

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param CustomerRepositoryInterface $customer
     * @param \Webkul\SpinToWin\Logger\Logger $logger
     * @param Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        CustomerRepositoryInterface $customer,
        \Webkul\SpinToWin\Logger\Logger $logger,
        Session $customerSession
    ) {
        parent::__construct($context);
        $this->customerSession = $customerSession;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->customer = $customer;
        $this->logger = $logger;
    }
    
    /**
     * Generate  Template
     *
     * @param mixed $emailTemplateVariables
     * @param mixed $senderInfo
     * @param mixed $receiverInfo
     * @param int $emailTempId
     *
     * @return void
     */
    public function generateTemplate(
        $emailTemplateVariables,
        $senderInfo,
        $receiverInfo,
        $emailTempId
    ) {
        $area = \Magento\Framework\App\Area::AREA_FRONTEND;
        try {
            $template =  $this->transportBuilder->setTemplateIdentifier($emailTempId)->setTemplateOptions(
                ['area' => $area, 'store' => $this->storeManager->getStore()->getId()]
            )->setTemplateVars($emailTemplateVariables)->setFrom($senderInfo)->addTo(
                $receiverInfo['email'],
                $receiverInfo['name']
            );
            return $this;
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    /**
     * Send Coupon Notification
     *
     * @param mixed $email
     * @param string $name
     * @param mixed $segment
     * @return void
     */
    public function sendCouponNotification($email, $name, $segment)
    {
        try {
            $receiverInfo = [
                'name' => $name,
                'email' => $email
            ];

            $senderInfo = [
                'name' => 'AiDot Team',
                'email' => $this->scopeConfig->getValue('spintowin/general/manager_email')
            ];
            $spinType = $segment->getSpinType();
            if(1 == $spinType){
                $subject= "Coupon Code from AiDot Spring Sale Cam";
                $emailTempId= $this->scopeConfig->getValue('spintowin/general/send_coupon_to_customer');
            }else {
                $subject= "Cloud Service Redemption Code from AiDot Spring Sale Campaign";
                $emailTempId= $this->scopeConfig->getValue('spintowin/general/send_cloud_code_to_customer');
            }
            $code= $segment->getCode();

            $emailTempVariables = [
                'subject' => __($subject),
                'segmentlabel' => __($segment->getLabel()),
                'code' => $code,
            ];
            $this->generateTemplate(
                $emailTempVariables,
                $senderInfo,
                $receiverInfo,
                $emailTempId
            );
            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }
}
