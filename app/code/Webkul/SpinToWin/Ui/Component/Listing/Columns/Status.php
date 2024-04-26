<?php 

namespace Webkul\SpinToWin\Ui\Component\Listing\Columns;


use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Webkul\SpinToWin\Model\Config\Source\Status as OptionStatus;


/**
 * Class Status
 *
 * @api
 * @since 100.1.0
 */
class Status extends Column 
{

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OptionStatus $optionStatus,
     * @param array $components
     * @param array $data
     */

    protected $optionStatus;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OptionStatus $optionStatus,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->optionStatus = $optionStatus;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);
        $options = $this->optionStatus->toArray();

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }
        $fieldName = $this->getData('name');
        foreach ($dataSource['data']['items'] as &$item) {
            if (isset($options[$item[$fieldName]])) {
                $item[$fieldName] = $options[$item[$fieldName]]->getText();
            }
        }

        return $dataSource;
    }
}