<?php
namespace Webkul\SpinToWin\Helper;

use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Webkul\SpinToWin\Helper\Container\IdentityContainer;

class ProductEmail extends \Magento\Framework\App\Helper\AbstractHelper{

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

    public $identityContainer;
     /**
     * @var \Webkul\SpinToWin\Logger\Logger
     */
    public $logger;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        IdentityContainer $identityContainer,
        \Webkul\SpinToWin\Logger\Logger $logger
    )
    {
        parent::__construct($context);
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->identityContainer = $identityContainer;
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
        $emailTempId
    ) {
        $area = \Magento\Framework\App\Area::AREA_FRONTEND;
        try {
            $template = $this->transportBuilder->setTemplateIdentifier($emailTempId)->setTemplateOptions(
                ['area' => $area, 'store' => $this->storeManager->getStore()->getId()]
            )->setTemplateVars($emailTemplateVariables)->setFromByScope($this->identityContainer->getEmailIdentity(),$this->identityContainer->getStoreId())->addTo(
                $this->identityContainer->getSendToEmail(),
                $this->identityContainer->getSendToUsername()
            );
            $copyTo = $this->identityContainer->getEmailCopyTo();

            if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'bcc') {
                foreach ($copyTo as $email) {
                    $this->transportBuilder->addBcc($email);
                }
            }
            return $this;
        } catch (\Exception $e) {
            $this->logger->info($e->getMessage());
        }
    }

    /**
     *  Notification
     *
     * @return void
     */
    public function sendNotification($email)
    {
        if($this->identityContainer->isEnabled()){
            try {
                $emailTempVariables = [
                    'email' => $email
                ];
                $this->generateTemplate(
                    $emailTempVariables,
                    $this->identityContainer->getTemplateId()
                );
                $transport = $this->transportBuilder->getTransport();
                $transport->sendMessage();
                $this->inlineTranslation->resume();
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
            }
        }
        
    }

}