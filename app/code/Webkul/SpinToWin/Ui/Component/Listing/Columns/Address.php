<?php 

namespace Webkul\SpinToWin\Ui\Component\Listing\Columns;


use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\View\Element\UiComponent\ContextInterface;


/**
 * Class Status
 *
 * @api
 * @since 100.1.0
 */
class Address extends Column 
{

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */

    protected $optionStatus;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     * @since 100.1.0
     */
    public function prepareDataSource(array $dataSource)
    {
        $dataSource = parent::prepareDataSource($dataSource);

        if (empty($dataSource['data']['items'])) {
            return $dataSource;
        }
        $fieldName = $this->getData('name');
        foreach ($dataSource['data']['items'] as &$item) {
            if(!empty($item['address_id'])){
                $item[$fieldName] = $item['firstname'].' '.$item['lastname'].' '.$item['telephone'].' '
                .$item['street'].' '.$item['city'].' '.$item['region'].' '.$item['country_id'].' '.$item['postcode'];
            }
            
        }

        return $dataSource;
    }
}