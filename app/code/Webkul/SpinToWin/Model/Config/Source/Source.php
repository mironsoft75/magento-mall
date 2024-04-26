<?php
namespace Webkul\SpinToWin\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Source implements OptionSourceInterface{
    const All= 0;
    const APP = 1;
    const PC = 2;

    public function toOptionArray()
    {
        return [
            ['value' => self::All,'label' => __('All')],
            ['value' => self::APP,'label' => __('App')],
            ['value' => self::PC,'label' => __('Pc')]
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