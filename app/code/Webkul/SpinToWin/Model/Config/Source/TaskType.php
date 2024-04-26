<?php
namespace Webkul\SpinToWin\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TaskType implements OptionSourceInterface{
    const BROWSE= 1;
    const SHARE = 2;
    const COMMUNITY = 3;

    public function toOptionArray()
    {
        return [
            ['value' => self::BROWSE,'label' => __('Browse Products')],
            ['value' => self::SHARE,'label' => __('Share with Your Friends')],
            ['value' => self::COMMUNITY,'label' => __('Post on Community')]
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

    public function getTaskType(){
        $array= [];
        foreach ($this->toOptionArray() as $item) {
            $array[$item['value']] = 0;
        }
        return $array;
    }


}
