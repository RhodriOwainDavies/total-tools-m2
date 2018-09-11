<?php

namespace Temando\Temando\Block\Adminhtml\Origin\Edit\Tab;

class FallbackInventoryTab extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * Temando Helper.
     *
     * @var \Temando\Temando\Helper\Data
     */
    protected $_helper;

    /**
     * FallbackInventoryTab constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Temando\Temando\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Temando\Temando\Helper\Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->getRegistryModel();
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('origin_');

         /* Fallback Stores Field Set */
        $fieldset = $form->addFieldset(
            'fallback_stores_fieldset',
            [
                'legend' => __('Fallback Stores'),
                'class' => 'fieldset-wide',
                'collapsable' => true
            ]
        );
                 
        for ($i = 1; $i <= 5; $i++) {
            $fieldset->addField(
                'supporting_origin_' . $i,
                'select',
                [
                    'name' => 'supporting_origins[]',
                    'label' => __('Fallback Store ' . $i),
                    'title' => __('Fallback Store ' . $i),
                    'required' => false,
                    'values' => $this->_helper->getOriginOptionArray()
                ]
            );
        }
        
        /* Inventory Stores Field Set */
        $fieldset = $form->addFieldset(
            'inventory_fieldset',
            [
                'legend' => __('Inventory'),
                'class' => 'fieldset-wide',
                'collapsable' => true
            ]
        );
        $origin = $model->getData();
        $inventoryStr = '';
        if (array_key_exists('erp_id', $origin)) {
            $inventoryArray = $this->_helper->getOriginsInventory(array($origin));
            if (!is_null($inventoryArray)) {
                foreach ($inventoryArray as $index => $inventory) {
                    $inventoryStr .= $inventory['sku'] . ',' . $inventory['units'] . "\n";
                }
            }
        }
        
        $fieldset->addField(
            'inventory',
            'textarea',
            [   'name' => 'inventory',
                'label' => __('Inventory'),
                'title' => __('Inventory'),
                'required' => false,
                'readonly' => true
            ]
        );

        $formValues = $model->getData();
        if ($model->getSupportingOrigins()) {
            $supportingOrigins = explode(',', $model->getSupportingOrigins());
            for ($i = 1; $i <= 5; $i++) {
                $formValues['supporting_origin_' . $i] = $supportingOrigins[$i - 1];
            }
        }
        $formValues['inventory'] = $inventoryStr;
        $form->setValues($formValues);
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get registry model.
     *
     * @return \Temando\Temando\Model\Store
     */
    public function getRegistryModel()
    {
        return $this->_coreRegistry->registry('temando_origin');
    }

    /**
     * Return Tab label.
     *
     * @return string
     *
     * @api
     */
    public function getTabLabel()
    {
        return __('Fallback stores & Inventory');
    }

    /**
     * Return Tab title.
     *
     * @return string
     *
     * @api
     */
    public function getTabTitle()
    {
        return __('Fallback stores & Inventory');
    }

    /**
     * Can show tab in tabs.
     *
     * @return bool
     *
     * @api
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden.
     *
     * @return bool
     *
     * @api
     */
    public function isHidden()
    {
        return false;
    }
}
