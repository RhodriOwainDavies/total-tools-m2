<?php

namespace Temando\Temando\Block\Adminhtml\Origin\Edit\Tab;

class ImageGalleryTab extends \Magento\Backend\Block\Widget\Form\Generic implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
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

        /*
         * General Field Set
         */
        $fieldset = $form->addFieldset(
            'imagegallery_fieldset',
            [
                'legend' => __('Image Gallery'),
            ]
        );

        $fieldset->addField(
            'gallery',
            'Temando\Temando\Block\Adminhtml\Widget\Form\Element\Gallery',
            [
                'label' => __('Image Gallery'),
                'title' => __('Image Gallery'),
            ]
        )->setRenderer(
            $this->getLayout()->createBlock('Temando\Temando\Block\Adminhtml\Widget\Form\Renderer\Gallery')
        );

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
        return __('Image Gallery');
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
        return __('Image Gallery');
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
