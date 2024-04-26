<?php
namespace Webkul\SpinToWin\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface{
    const TO_REDEEM= 1;
    const N_A = 0;
    const REDEEMED = 2;

    public function toOptionArray()
    {
        return [
            ['value' => self::N_A,'label' => __('N/A')],
            ['value' => self::TO_REDEEM,'label' => __('To Redeem')],
            ['value' => self::REDEEMED,'label' => __('Redemed')]
        ];
    }

     /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $array = [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = $item['label'];
        }
        return $array;
    }

}