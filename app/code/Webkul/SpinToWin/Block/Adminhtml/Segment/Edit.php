<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\SpinToWin\Block\Adminhtml\Segment;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\SalesRule\Model\Rule;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;

class Edit extends Generic implements TabInterface
{
    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    public $rendererFieldset;
 
    /**
     * @var \Magento\Rule\Block\Conditions
     */
    public $conditions;

    /**
     * @var \Magento\Rule\Block\Actions $ruleActions
     */
    public $actions;

    /**
     * @var \Magento\Backend\Block\Template\Context
     */
    public $context;

    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * @var \Magento\Framework\Data\FormFactory
     */
    public $formFactory;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    public $ruleFactory;

    /**
     * @var \Webkul\SpinToWin\Model\SegmentsFactory
     */
    public $segmentsFactory;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    public $request;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Rule\Block\Actions $ruleActions,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory,
        \Magento\Framework\App\RequestInterface $request,
        array $data = []
    ) {
        $this->rendererFieldset = $rendererFieldset;
        $this->conditions = $conditions;
        $this->ruleFactory = $ruleFactory;
        $this->segmentsFactory = $segmentsFactory;
        $this->_request = $request;
        $this->actions = $ruleActions;
        parent::__construct($context, $registry, $formFactory, $data);
    }
 
    /**
     * Get Tab Label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Add Segment');
    }
 
    /**
     * Get Tab Title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Add Segment');
    }
 
    /**
     * Can Show Tab
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * Is Hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
 
    /**
     * Prepare form before rendering HTML
     *
     * @return Generic
     */
    public function _prepareForm()
    {
        $model = $this->ruleFactory->create();
        if (!$this->getRequest()->getParam('id')) {
            $model->setSpinId($this->getRequest()->getParam('spin_id'));
        } else {
            $segment = $this->segmentsFactory->create()->load($this->getRequest()->getParam('id'));
            if ($segment->getRuleId()) {
                $model->load($segment->getRuleId());
                $model->setRuleDescription($model->getDescription());
            }
            
            $model->addData($segment->getData());
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('segmentrule_');
        $baseFieldset = $form->addFieldset('segment_fieldset', ['legend' => __('Information')]);
        $data = $model->getData();

        $baseFieldset->addField('entity_id', 'hidden', ['name' => 'entity_id']);
        $baseFieldset->addField('spin_id', 'hidden', ['name' => 'spin_id']);
        // $baseFieldset->addField('rule_id', 'hidden', ['name' => 'rule_id']);

        $baseFieldset->addField(
            'label',
            'text',
            [
                'name' => 'label',
                'label' => __('Label'),
                'id' => 'label',
                'title' => __('Label'),
                'required' => true,
                'class' => 'validate-no-html-tags',
                'note' => __('Label is shown on the wheel.')
            ]
        );

        $baseFieldset->addField(
            'heading',
            'text',
            [
                'name' => 'heading',
                'label' => __('Heading'),
                'id' => 'heading',
                'title' => __('Heading'),
                'required' => true,
                'class' => 'validate-no-html-tags',
                'note' => __('Heading is shown on result form as {{Success Title}}.')
            ]
        );

        $baseFieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'id' => 'description',
                'title' => __('Description'),
                'required' => true,
                'class' => 'validate-no-html-tags',
                'note' => __('Description is shown on result form as {{Success Description Text}}.')
            ]
        );

        $baseFieldset->addField(
            'limits',
            'text',
            [
                'name' => 'limits',
                'label' => __('Limit'),
                'id' => 'limits',
                'title' => __('Limit'),
                'required' => false,
                'class' => 'integer validate-zero-or-greater',
                'note' => __('Maximum number of times it can occur.')
            ]
        );

        $baseFieldset->addField(
            'gravity',
            'text',
            [
                'name' => 'gravity',
                'label' => __('Gravity'),
                'id' => 'gravity',
                'title' => __('Gravity'),
                'required' => true,
                'class' => 'integer validate-number-range number-range-0-100',
                'note' => __('Gravity/Probability of occurrence between 0-100.')
            ]
        );

        $baseFieldset->addField(
            'position',
            'text',
            [
                'name' => 'position',
                'label' => __('Position'),
                'id' => 'position',
                'title' => __('Position'),
                'required' => true,
                'class' => 'integer validate-zero-or-greater',
                'note' => __('To manage position on the spin wheel.')
            ]
        );
        $baseFieldset->addField(
            'image',
            'image',
            [
                "name" => "image",
                'label' => __('Image'),
                'id' => 'image',
                'title' => __('Image'),
                'class' => '',
                'required' => true
            ]
        );
        
        $lastfield = $baseFieldset->addField(
            'type',
            'select',
            [
                'name' => 'type',
                'label' => __('Type'),
                'id' => 'type',
                'title' => __('Type'),
                'required' => true,
                'values' => $this->getType()
            ]
        );
        $lastfield = $baseFieldset->addField(
            'spin_type',
            'select',
            [
                'name' => 'spin_type',
                'label' => __('Spin Type'),
                'id' => 'spin_type',
                'title' => __('Spin Type'),
                'required' => true,
                'values' => $this->getSpinType()
            ]
        );

        //$baseFieldset->addField(
        //     'value',
        //     'text',
        //     [
        //         'name' => 'value',
        //         'label' => __('Coupon Value'),
        //         'id' => 'value',
        //         'title' => __('Coupon Value'),
        //         'required' => true,
        //         'class' => 'validate-number',
        //         'note' => __('To manage budget only.')
        //     ]
        // );

        $lastfield->setAfterElementHtml(
            '<script type="text/x-magento-init">
                {
                    "*": {
                        "editsegmentload": {}
                    }
                }
            </script>'
        );

        // $renderer = $this->getLayout()->createBlock(Fieldset::class);
        // $renderer->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
        //     ->setNewChildUrl($this->getUrl(
        //         'sales_rule/promo_quote/newConditionHtml/form/segmentrule_conditions_fieldset/form_namespace/edit_form'
        //     ));
        
        $productset = $form->addFieldset('product_fieldset',['legend' =>__('Product')]);
        $productset->addField(
            'product_sku',
            'text',
            [
                'name' => 'product_sku',
                'label' => __('Product Sku'),
                'id' => 'product_sku',
                'title' => __('Product Sku'),
                'class' => 'validate-no-html-tags',
                'required' => true,
                'note' => __('Add Product Sku')
            ]

        );
        
        $pointset = $form->addFieldset('point_fieldset',['legend' =>__('Point')]);
        $pointset->addField(
            'point',
            'text',
            [
                'name' => 'point',
                'label' => __('Point'),
                'id' => 'point',
                'title' => __('Point'),
                'class' => 'validate-zero-or-greater',
                'required' => true,
                'note' => __('Add Point')
            ]

        );
        $cloudset = $form->addFieldset('cloud_type_fieldset',['legend' =>__('Cloud Type')]);
        $cloudset->addField(
            'cloud_type',
            'select',
            [
                'name' => 'cloud_type',
                'label' => __('Cloud Type'),
                'id' => 'cloud_type',
                'title' => __('Cloud Type'),
                'required' => true,
                'values' => $this->getCloudType()
            ]

        );

        // $fieldset = $form->addFieldset(
        //     'conditions_fieldset',
        //     ['legend' => __('Apply the rule only if the following conditions are met (leave blank for all products).')]
        // )->setRenderer($renderer);

        // $fieldset->addField(
        //     'conditions',
        //     'text',
        //     [
        //         'name' => 'conditions',
        //         'label' => __('Conditions'),
        //         'title' => __('Conditions'),
        //         'required' => true,
        //     ]
        // ) ->setRule($model)
        // ->setRenderer($this->conditions);
 
        // $renderer_action = $this->getLayout()->createBlock(Fieldset::class);
        // $renderer_action->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
        //     ->setNewChildUrl($this->getUrl(
        //         'sales_rule/promo_quote/newActionHtml/form/segmentrule_action_fieldset/form_namespace/edit_form'
        //     ));
            
        // $actionFieldset = $form->addFieldset('action_fieldset', ['legend' => __('Action')])->setRenderer($renderer_action);
        $actionFieldset = $form->addFieldset('action_fieldset', ['legend' => __('Rule Id')]);
        $actionFieldset->addField(
            'rule_id',
            'text',
            [
                'name' => 'rule_id',
                'label' => __('Rule Id'),
                'id' => 'rule_id',
                'title' => __('Rule Id'),
                'class' => 'validate-zero-or-greater',
                'required' => true,
                'note' => __('Add Rule Id')
            ]

        );
        // $data = [];

        // $actionFieldset->addField(
        //     'coupon_source',
        //     'select',
        //     [
        //         'name' => 'coupon_source',
        //         'label' => __('Coupon Source'),
        //         'id' => 'coupon_source',
        //         'title' => __('Coupon Source'),
        //         'required' => true ,
        //         'values' => $this->getCouponSource()
        //     ]
        // );
        // $actionFieldset->addField(
        //     'simple_action',
        //     'select',
        //     [
        //         'name' => 'simple_action',
        //         'label' => __('Apply'),
        //         'id' => 'simple_action',
        //         'title' => __('Apply'),
        //         'required' => true ,
        //         'values' => $this->getActions()
        //     ]
        // );
        // $actionFieldset->addField(
        //     'rule_description',
        //     'textarea',
        //     [
        //         'name' => 'rule_description',
        //         'label' => __('Rule Description'),
        //         'id' => 'rule_description',
        //         'title' => __('Rule Description'),
        //         'class' => 'validate-no-html-tags',
        //         'required' => true
        //     ]
        // );
        // $actionFieldset->addField(
        //     'custom_description',
        //     'textarea',
        //     [
        //         'name' => 'custom_description',
        //         'label' => __('Custom Description'),
        //         'id' => 'custom_description',
        //         'title' => __('Custom Description'),
        //         'class' => 'validate-no-html-tags',
        //         'required' => true
        //     ]
        // );
        // $actionFieldset->addField(
        //     'discount_amount',
        //     'text',
        //     [
        //         'name' => 'discount_amount',
        //         'label' => __('Discount Amount'),
        //         'id' => 'discount_amount',
        //         'title' => __('Discount Amount'),
        //         'class' => 'validate-zero-or-greater',
        //         'required' => true
        //     ]
        // );
        // $actionFieldset->addField(
        //     'discount_qty',
        //     'text',
        //     [
        //         'name' => 'discount_qty',
        //         'label' => __('Maximum Qty Discount is Applied To'),
        //         'id' => 'discount_qty',
        //         'title' => __('Maximum Qty Discount is Applied To'),
        //         'class' => 'validate-zero-or-greater',
        //         'required' => false
        //     ]
        // );
        // $actionFieldset->addField(
        //     'discount_step',
        //     'text',
        //     [
        //         'name' => 'discount_step',
        //         'label' => __('Discount Qty Step (Buy X)'),
        //         'id' => 'discount_step',
        //         'title' => __('Discount Qty Step (Buy X)'),
        //         'class' => 'validate-zero-or-greater',
        //         'required' => false
        //     ]
        // );
        // $actionFieldset->addField(
        //     'stop_rules_processing',
        //     'select',
        //     [
        //         'name' => 'stop_rules_processing',
        //         'label' => __('Discard Subsequent Rules'),
        //         'id' => 'stop_rules_processing',
        //         'title' => __('Discard Subsequent Rules'),
        //         'required' => true ,
        //         'values' => $this->getYesNo()
        //     ]
        // );
        // $actionFieldset->addField('from_date', 'hidden', ['name' => 'from_date']);
        // $actionFieldset->addField(
        //     'to_date',
        //     'date',
        //     [
        //         'name' => 'to_date',
        //         'label' => __('Expiry Date'),
        //         'id' => 'to_date',
        //         'title' => __('Expiry Date'),
        //         'date_format' => 'MM/dd/yyyy',
        //         'required' => true
        //     ]
        // );
        // $actionFieldset->addField(
        //     'action',
        //     'text',
        //     [
        //         'name' => 'apply_to',
        //         'label' => __('Apply To'),
        //         'title' => __('Apply To'),
        //         'required' => true,
        //         'data-form-part' => 'edit_form'
        //     ]
        // ) ->setRule($model)
        // ->setRenderer($this->actions);
        // $model->setFromDate('01/01/1970');
        $form->setValues($model->getData());
        $this->setForm($form);
 
        return parent::_prepareForm();
    }

    /**
     * Get Actions
     *
     * @return array
     */
    private function getActions()
    {
        $applyOptions = [
            ['label' => __('Percent of product price discount'), 'value' =>  Rule::BY_PERCENT_ACTION],
            ['label' => __('Fixed amount discount'), 'value' => Rule::BY_FIXED_ACTION],
            ['label' => __('Fixed amount discount for whole cart'), 'value' => Rule::CART_FIXED_ACTION],
            ['label' => __('Buy X get Y free (discount amount is Y)'), 'value' => Rule::BUY_X_GET_Y_ACTION]
        ];
        return $applyOptions;
    }

    private function getCouponSource(){
        return [
            ['label' => 'App Lucky Draw', 'value' => 1],
            ['label' => 'AiDot Spring Sale Campaign', 'value' => 2]
        ];
    }

    /**
     * Get Yes No
     *
     * @return array
     */
    private function getYesNo()
    {
        $yesNo = [
                    ['label' => __('No'), 'value' => '0'],
                    ['label' => __('Yes'), 'value' => '1']
                ];
        return $yesNo;
    }

    /**
     * Get Yes No
     *
     * @return array
     */
    private function getType()
    {
        $yesNo = [
                    ['label' => __('Win'), 'value' => '1'],
                    ['label' => __('Lose'), 'value' => '0']
                ];
        return $yesNo;
    }

    private function getSpinType(){
        $yesNo = [
            ['label' => __('Coupon'), 'value' => '1'],
            ['label' => __('Product'), 'value' => '2'],
            ['label' => __('Point'), 'value' => '3'],
            ['label' => __('Cloud Server'), 'value' => '4']
        ];
        return $yesNo;
    }

    private function getCloudType(){
        $month=[];
        for($i=1; $i<=12;$i++){
            $month[]=[
                'label' => $i.'Month','value' => $i
            ];
        }
        return $month;
    }
}
