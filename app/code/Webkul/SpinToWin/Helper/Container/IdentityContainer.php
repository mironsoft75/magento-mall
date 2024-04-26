<?php

namespace Webkul\SpinToWin\Helper\Container;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;

class IdentityContainer {

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;


    protected $storeId;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

      /**
     * Configuration paths
     */
    const XML_PATH_EMAIL_COPY_METHOD = 'spintowin/draw_product/copy_method';
    const XML_PATH_EMAIL_COPY_TO = 'spintowin/draw_product/copy_to';
    const XML_PATH_EMAIL_IDENTITY = 'spintowin/draw_product/identity';
    const XML_PATH_EMAIL_TEMPLATE = 'spintowin/draw_product/notice_template';
    const XML_PATH_EMAIL_ENABLED = 'spintowin/draw_product/enabled';
    const XML_PATH_EMAIL_TO= 'spintowin/draw_product/send_to_email';
    const XML_PATH_TO_USERNAME = 'spintowin/draw_product/send_to_username';

    /**
     * set storeId 
     * @param int $storeId
     * @return void
     */
    public function setStoreId($storeId){
        $this->storeId = $storeId;
    }

    /**
     * @return int $storeId
     */
    public function getStoreId(){
        if(empty($this->storeId)){
            $this->storeId = $this->storeManager->getStore()->getId();
        }
        return $this->storeId;
    }


    /**
     * Is email enabled
     * @return bool
     */
    public function isEnabled(){
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_EMAIL_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStoreId()
        );
    }

    /**
     * Return store configuration value
     *
     * @param string $path
     * @param int $storeId
     * @return mixed
     */
    protected function getConfigValue($path, $storeId=0)
    {
        if(empty($storeId)) {
            $storeId = $this->getStoreId();
        }
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

     /**
     * Return email copy_to list
     *
     * @return array|bool
     */
    public function getEmailCopyTo()
    {
        $data = $this->getConfigValue(self::XML_PATH_EMAIL_COPY_TO);
        if (!empty($data)) {
            return array_map('trim', explode(',', $data));
        }
        return false;
    }

    /**
     * Return email copy method
     *
     * @return mixed
     */
    public function getCopyMethod()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_COPY_METHOD);
    }


    /**
     * Return template id
     *
     * @return mixed
     */
    public function getTemplateId()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_TEMPLATE);
    }

    /**
     * Return email identity
     *
     * @return mixed
     */
    public function getEmailIdentity()
    {
        return $this->getConfigValue(self::XML_PATH_EMAIL_IDENTITY);
    }

    /**
     * @return mixed
     */
    public function getSendToEmail(){
        return $this->getConfigValue(self::XML_PATH_EMAIL_TO);
    }

    /**
     * @return mixed
     */
    public function getSendToUsername(){
        return $this->getConfigValue(self::XML_PATH_TO_USERNAME);
    }



}