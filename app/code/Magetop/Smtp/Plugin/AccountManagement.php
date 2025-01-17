<?php
/**
 * Magetop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magetop.com license that is
 * available through the world-wide-web at this URL:
 * https://www.magetop.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magetop
 * @package     Magetop_Smtp
 * @copyright   Copyright (c) Magetop (https://www.magetop.com/)
 * @license     https://www.magetop.com/LICENSE.txt
 */

namespace Magetop\Smtp\Plugin;

use Exception;
use Magento\Customer\Model\AccountManagement as CustomerAccountManagement;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;

/**
 * Class AccountManagement
 * @package Magetop\Smtp\Plugin
 */
class AccountManagement
{
    /**
     * @var CheckoutSession
     */
    protected $checkoutSession;

    /**
     * @var CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * AccountManagement constructor.
     *
     * @param CheckoutSession $checkoutSession
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $cartRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->cartRepository  = $cartRepository;
    }

    /**
     * @param CustomerAccountManagement $subject
     * @param $result
     * @param $customerEmail
     *
     * @return false|mixed
     * @SuppressWarnings("Unused")
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterIsEmailAvailable(CustomerAccountManagement $subject, $result, $customerEmail)
    {
        $cartId = $this->checkoutSession->getQuote()->getId();

        if (!$cartId) {
            return $result;
        }

        /** @var Quote $quote */
        $quote = $this->cartRepository->getActive($cartId);
        $quote->setCustomerEmail($customerEmail);

        try {
            $this->cartRepository->save($quote);

            return $result;
        } catch (Exception $e) {
            return false;
        }
    }
}
