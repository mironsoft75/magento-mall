<?php 

namespace Webkul\SpinToWin\Ui\Component\Listing\Columns;


use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Webkul\SpinToWin\Model\Config\Source\Source as OptionSource;


/**
 * Class Source
 *
 * @api
 * @since 100.1.0
 */
class Source extends Column 
{

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OptionSource $optionSource,
     * @param array $components
     * @param array $data
     */

    protected $optionStatus;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OptionSource $optionSource,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->optionSource = $optionSource;
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);
        $options = $this->optionSource->toArray();

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