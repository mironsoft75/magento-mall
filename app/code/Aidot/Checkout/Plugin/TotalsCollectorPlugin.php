<?php
namespace Aidot\Checkout\Plugin;
use \Magento\Quote\Model\Quote;
use \Magento\Quote\Model\Quote\Address;

class TotalsCollectorPlugin {
    protected $request;
    public function __construct(
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->request = $request;
    }

    public function beforeCollectAddressTotals(\Magento\Quote\Model\Quote\TotalsCollector $subject,Quote $quote,Address $address){
            // 过滤不激活的子购物车
            // $path = $this->request->getUri()->getPath();
            // if(isset($_SERVER['HTTP_REFERER'])){
            //     $url =  $_SERVER['HTTP_REFERER'];
            //     $url = isset(parse_url($url)['path']) ? parse_url($url)['path'] :'';
            // }else {
            //     $url = $path;
            // }
            
            $items = $address->getAllItems();
            // if($quote->getTotalsInfo() || ('/checkout/cart/' == $url && $path != '/checkout/') || !count($items)){//如果是totalsInfo请求的 或者是checkout/cart页面请求
            //     return [$quote,$address];
            // }
            if(!count($items)){
                return [$quote,$address];
            }
            $list= [];
            $qty= 0;
            $count = 0;
            foreach($items as $item){
                if($item->getIsActive() && $item->getId()){
                    $list[]=$item;
                    if(empty($item->getParentItemId())){
                        $qty +=$item->getQty();
                        ++$count;
                    }
                    
                }
            }
            if($qty){
                $quote->setItemsQty($qty);
                $quote->setItemsCount($count);
                if(!empty($address->getQuote()->getItems())){
                    $address->getQuote()->setItems($list);
                }
            }
            $address->setAllItems($list);
            $address->setCachedItemsAll($list);
            return [$quote,$address];
        }

}