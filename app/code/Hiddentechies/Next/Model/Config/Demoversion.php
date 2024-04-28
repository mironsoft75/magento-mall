<?php

namespace Hiddentechies\Next\Model\Config;

class Demoversion implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function toOptionArray()
    {
        return [
            ['value' => '0', 'label' => __('All')],
            ['value' => 'demo01', 'label' => __('Demo 1')]
        ];
    }
}
