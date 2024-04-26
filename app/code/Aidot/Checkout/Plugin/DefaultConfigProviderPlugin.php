<?php
namespace Aidot\Checkout\Plugin;
use Magento\Checkout\Model\Session as CheckoutSession;


class DefaultConfigProviderPlugin {

    protected CheckoutSession $checkoutSession;
    public function __construct(
        CheckoutSession $checkoutSession
    )
    {
        $this->checkoutSession = $checkoutSession;
    }

    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $config){
        
        if(isset($config['quoteData']['initPayment']['totals']['items'])){
            $quote = $this->checkoutSession->getQuote();
            // 过滤不激活的子购物车
            $quoteItems = $quote->getAllItems();
            $quoteMap = [];
            foreach($quoteItems as $quoteItem){
                if($quoteItem->getIsActive()){
                    $quoteMap[$quoteItem->getId()] = $quoteItem;
                }
            }
            $items = $config['quoteData']['initPayment']['totals']['items'];
            $list= [];
            $qty= 0;
            foreach($items as $item){
                if(isset($quoteMap[$item['item_id']])){
                    $qty +=$item['qty'];
                    $list[]=$item;
                }
                
            }
            $config['quoteData']['initPayment']['totals']['items']= $list;
            $config['quoteData']['initPayment']['totals']['items_qty'] = $qty;
            $config['quoteData']['items_count']= count($list);
            $config['quoteData']['items_qty']= $qty;
            $config['totalsData']['items_qty']= $qty;
        }
        return $config;
    }

}