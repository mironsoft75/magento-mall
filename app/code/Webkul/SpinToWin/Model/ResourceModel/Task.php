<?php
namespace Webkul\SpinToWin\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Task extends AbstractDb{
    

    public function _construct(){
        $this->_init('spintowin_task','id');
    }
}
