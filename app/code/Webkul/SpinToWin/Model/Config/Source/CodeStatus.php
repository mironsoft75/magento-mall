<?php
namespace Webkul\SpinToWin\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class CodeStatus implements OptionSourceInterface{
    const USED= 1;
    const UNUSED = 0;
    const FAILURE = 2;

    public function toOptionArray()
    {
        return [
            ['value' => self::UNUSED,'label' => __('未使用')],
            ['value' => self::USED,'label' => __('已抽中')],
            ['value' => self::FAILURE,'label' => __('失效')]
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