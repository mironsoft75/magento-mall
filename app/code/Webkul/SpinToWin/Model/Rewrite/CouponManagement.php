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

namespace Webkul\SpinToWin\Model\Rewrite;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Silk\Referral\Service\ReferralCouponSrv;

class CouponManagement extends \Magento\Quote\Model\CouponManagement
{

    protected ReferralCouponSrv $referralCouponSrv;
    /**
     * Constructor
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Webkul\SpinToWin\Helper\Data $helper
     * @param ReferralCouponSrv $referralCouponSrv
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Webkul\SpinToWin\Helper\Data $helper,
        ReferralCouponSrv $referralCouponSrv
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->helper = $helper;
        $this->referralCouponSrv = $referralCouponSrv;
        parent::__construct($quoteRepository);
    }

    /**
     * Set
     *
     * @param int $cartId
     * @param mixed $couponCode
     * @return void
     */
    public function set($cartId, $couponCode)
    {
        $isValid = $this->helper->checkValidCoupon($couponCode);
        if (!$isValid['success']) {
            throw new CouldNotSaveException($isValid['msg']);
        }

        /** @var  \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        if (!$quote->getItemsCount()) {
            throw new NoSuchEntityException(__('The "%1" Cart doesn\'t contain products.', $cartId));
        }
        if (!$quote->getStoreId()) {
            throw new NoSuchEntityException(__('Cart isn\'t assigned to correct store'));
        }
        $quote->getShippingAddress()->setCollectShippingRates(true);
        $validateObj = $this->validateVoucher($couponCode,$quote);
        $couponCode = $validateObj['couponCode'];
        $isVoucher = $validateObj['is_voucher'] ? 1 : 0;
        try {
            $quote->setCouponCode($couponCode);
            $quote->setIsVoucher($isVoucher);
            $this->quoteRepository->save($quote->collectTotals());
        } catch (LocalizedException $e) {
            throw new CouldNotSaveException(__('The coupon code couldn\'t be applied: ' .$e->getMessage()), $e);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __("The coupon code couldn't be applied. Verify the coupon code and try again."),
                $e
            );
        }
        if ($quote->getCouponCode() != $couponCode) {
            throw new NoSuchEntityException(__("The coupon code isn't valid. Verify the code and try again."));
        }
        return $couponCode;
    }

    /**
     * 验证抵扣券
     */
    public function validateVoucher($couponCode,$quote){
        $oldCouponCode = $quote->getCouponCode();
        $isVoucher = $this->referralCouponSrv->validateVoucher($couponCode);
        if(empty($oldCouponCode)){
            return ['is_voucher' => $isVoucher,'couponCode' =>$couponCode];
        }
        $oldCouponCodeArr= explode(',',$oldCouponCode);
        if(count($oldCouponCodeArr) >1){
            if($isVoucher){
                $couponCode = $oldCouponCode.','.$couponCode;
            }
            return ['is_voucher' => $isVoucher,'couponCode' => $couponCode];
        }else {
            $oldIsVoucher = $this->referralCouponSrv->validateVoucher($oldCouponCode);
            if($oldIsVoucher){
                if($isVoucher){
                    $couponCode = $oldCouponCode.','.$couponCode;
                }
            }
            return ['is_voucher' => $isVoucher,'couponCode' =>$couponCode];
        }
        
    }
}
