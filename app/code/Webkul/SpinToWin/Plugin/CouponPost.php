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

namespace Webkul\SpinToWin\Plugin;

use Magento\Framework\Controller\ResultFactory;

class CouponPost
{
    /**
     * Constructor
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Webkul\SpinToWin\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Magento\Framework\Controller\ResultFactory $resultFactory,
        \Webkul\SpinToWin\Helper\Data $helper
    ) {
        $this->request = $request;
        $this->messageManager = $messageManager;
        $this->resultFactory = $resultFactory;
        $this->redirect = $redirect;
        $this->helper = $helper;
    }

    /**
     * Around Execute
     *
     * @param \Magento\Checkout\Controller\Cart\CouponPost $subject
     * @param \Closure $proceed
     * @return void
     */
    public function aroundExecute(
        \Magento\Checkout\Controller\Cart\CouponPost $subject,
        \Closure $proceed
    ) {
        $data = $this->request->getParams();
        if ((!isset($data['remove']) || !$data['remove']) && isset($data['coupon_code'])) {
            $isValid = $this->helper->checkValidCoupon($data['coupon_code']);
            if ($isValid['success']) {
                $result = $proceed();
                return $result;
            } else {
                $this->messageManager->addError($isValid['msg']);
                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setUrl($this->redirect->getRefererUrl());
                return $resultRedirect;
            }
        } else {
            $result = $proceed();
            return $result;
        }
    }
}
