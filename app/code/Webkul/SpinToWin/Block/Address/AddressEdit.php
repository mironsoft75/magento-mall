<?php

namespace Webkul\SpinToWin\Block\Address;
use Magento\Customer\Block\Address\Edit as CustomerEdit;

class AddressEdit extends CustomerEdit{

    public function getTitle()
    {
        return  __('Shipping Address');
    }
}
