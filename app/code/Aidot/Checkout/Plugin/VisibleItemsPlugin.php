<?php
namespace Aidot\Checkout\Plugin;

class VisibleItemsPlugin {

    public function afterGetAllVisibleItems(\Magento\Quote\Model\Quote $subject,$items) {
        if(!empty($items)){
            foreach($items as $k=>$item){
                if(!$item->getIsActive()){
                    unset($items[$k]);
                }
            }
            $items = array_values($items);
        }
        return $items;
    }

}