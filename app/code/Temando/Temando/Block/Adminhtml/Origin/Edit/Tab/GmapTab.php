<?php

namespace Temando\Temando\Block\Adminhtml\Origin\Edit\Tab;

class GmapTab extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Country factory.
     *
     * @var \Magento\Config\Model\Config\Source\Locale\Country
     */
    protected $_countryFactory;

    /**
     * GmapTab constructor.
     *
     * @param \Magento\Backend\Block\Template\Context            $context
     * @param \Magento\Framework\Registry                        $registry
     * @param \Magento\Framework\Data\FormFactory                $formFactory
     * @param \Magento\Config\Model\Config\Source\Locale\Country $localCountry
     * @param array                                              $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Directory\Model\Config\Source\Country $countryFactory,
        array $data = []
    ) {
        $this->_countryFactory = $countryFactory;
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

        $fieldset = $form->addFieldset(
            'gmap_fieldset',
            [
                'legend' => __('Location Information')
            ]
        );
        
        if ($model->getId()) {
            $fieldset->addField(
                'origin_id',
                'hidden',
                [
                    'name' => 'origin_id'
                ]
            );
        }
        $fieldset->addField(
            'street',
            'text',
            [
                'name' => 'street',
                'label' => __('Street'),
                'title' => __('Street'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'title' => __('City'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'postcode',
            'text',
            [
                'name' => 'postcode',
                'label' => __('Postcode'),
                'title' => __('Postcode'),
                'required' => true
            ]
        );
        $fieldset->addField(
            'region',
            'text',
            [
                'name' => 'region',
                'label' => __('Region'),
                'title' => __('Region'),
                'required' => true
            ]
        );
        $countryOptions = $this->_countryFactory->toOptionArray();
        $fieldset->addField(
            'country',
            'select',
            [
                'name' => 'country',
                'label' => __('Country'),
                'title' => __('Country'),
                'values' => $countryOptions,
                'required' => true
            ]
        );
        $fieldset->addField(
            'latitude',
            'text',
            [
                'name' => 'latitude',
                'label' => __('Latitude'),
                'title' => __('Latitude'),
                'required' => true,
                'readonly' => true,
            ]
        );
        $fieldset->addField(
            'longitude',
            'text',
            [
                'name' => 'longitude',
                'label' => __('Longitude'),
                'title' => __('Longitude'),
                'required' => true,
                'readonly' => true,
            ]
        );
        $fieldset->addField(
            'zoom_level',
            'text',
            [
                'name' => 'zoom_level',
                'label' => __('Zoom Level'),
                'title' => __('Zoom Level'),
                'required' => true,
                'readonly' => true,
            ]
        );

        $mapBlock = $this->getLayout()
            ->createBlock('Temando\Temando\Block\Adminhtml\Origin\Edit\Tab\GmapTab\Renderer\Map');

        $fieldset->addField(
            'googlemap',
            'text',
            [
                'label' => __('Store Map'),
                'name' => 'googlemap',
            ]
        )->setRenderer($mapBlock);

        if (!$model->getId()) {
            $model->setLatitude('0.00000000')
                ->setLongitude('0.00000000')
                ->setZoomLevel(4);
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get registry model.
     *
     * @return \Temando\Temando\Model\Origin
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
        return __('Location Information');
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
        return __('Location Information');
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
