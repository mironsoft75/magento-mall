<?php

namespace Webkul\SpinToWin\Block\Widget;

use \Webkul\SpinToWin\Helper\Data;
use Webkul\SpinToWin\Model\Config\Source\TaskType;

class Lottery extends \Magento\Framework\View\Element\Template implements \Magento\Widget\Block\BlockInterface
{

    protected Data $dataHelper;
    protected TaskType $taskType;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        Data $dataHelper,
        TaskType $taskType,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->dataHelper = $dataHelper;
        $this->taskType = $taskType;
        $this->setTemplate('widget/lottery.phtml');
    }

    public function getSpinInfo($spinId)
    {
        $spinObj = $this->dataHelper->getSpinBySpinId($spinId);
        $data = [];
        if ($spinObj->hasData()) {
            $spinId = $spinObj->getId();
            $data = [
                'spin_id' => $spinId,
                'start_date' => strtotime($spinObj->getStartDate()),
                'end_date' => strtotime($spinObj->getEndDate()),
                'rule' => $spinObj->getSpinRule(),
                'segments' => []
            ];
            $mediea = $this->dataHelper->getMediaDirectory();
            foreach ($spinObj->getSegments() as $segment) {
                $data['segments'][] = [
                    'label' => $segment->getLabel(),
                    // 'type' => $segment->getType(),
                    // 'spin_type' => $segment->getSpinType(),
                    'segment_id' => $segment->getEntityId(),
                    'image' => $mediea . $segment->getImage()
                ];
            }
            $data['tasks'] = $this->taskType->toOptionArray();
        }
        return $data;
    }
}
