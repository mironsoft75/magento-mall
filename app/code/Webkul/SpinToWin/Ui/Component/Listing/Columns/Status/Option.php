<?php
namespace Webkul\SpinToWin\Ui\Component\Listing\Columns\Status;
use Magento\Framework\Data\OptionSourceInterface;
use Webkul\SpinToWin\Model\Config\Source\Status as OptionStatus;

class Option implements OptionSourceInterface{

    protected $optionStatus;
    /**
     * @var array
     */
    protected $options;

    public function __construct(OptionStatus $optionStatus)
    {
        $this->optionStatus = $optionStatus;
    }

    public function toOptionArray()
    {
        if ($this->options === null) {
            $options = $this->optionStatus->toOptionArray();
            $this->options = $options;
        }
        return $this->options;
    }

}