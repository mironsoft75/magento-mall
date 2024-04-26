<?php
namespace Webkul\SpinToWin\Model\ResourceModel\Product;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection {
    public function _construct(){
        $this->_init(\Webkul\SpinToWin\Model\ProductAddress::class,\Webkul\SpinToWin\Model\ResourceModel\ProductAddress::class);
    }
}