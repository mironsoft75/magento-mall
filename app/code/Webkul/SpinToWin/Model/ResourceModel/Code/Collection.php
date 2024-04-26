<?php
namespace Webkul\SpinToWin\Model\ResourceModel\Code;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection{
    public function _construct(){
        $this->_init(
            \Webkul\SpinToWin\Model\CloudCode::class,
            \Webkul\SpinToWin\Model\ResourceModel\CloudCode::class
        );
    }

}