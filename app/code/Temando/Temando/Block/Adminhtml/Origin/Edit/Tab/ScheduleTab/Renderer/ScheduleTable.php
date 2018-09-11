<?php

namespace Temando\Temando\Block\Adminhtml\Origin\Edit\Tab\ScheduleTab\Renderer;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class ScheduleTable extends \Magento\Backend\Block\Widget implements RendererInterface
{
    protected $_template = 'Temando_Temando::origin/scheduletable.phtml';

    /**
     * Core Registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * Model Url instance.
     *
     * @var \Magento\Backend\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * ScheduleTable constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\UrlFactory $backendUrlFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\Model\UrlFactory $backendUrlFactory,
        array $data = []
    ) {
        
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_backendUrl = $backendUrlFactory->create();
    }

    /**
     * Preparing global layout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $tableGrid = $this->getLayout()
            ->createBlock('Temando\Temando\Block\Adminhtml\Origin\Edit\Tab\ScheduleTab\TableGrid');

        $store = $this->getRegistryModel();
        $tableGrid->setData('schedule_id', $store->getScheduleId());
        $this->setChild('schedule_table_grid', $tableGrid);

        return parent::_prepareLayout();
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
     * Render form element as HTML.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);

        return $this->toHtml();
    }

    /**
     * Get Registry Store.
     *
     * @return \Temando\Temando\Model\Origin
     */
    public function getRegistyStore()
    {
        return $this->_coreRegistry->registry('temando_origin');
    }

    /**
     * Get url to load schedule table grid by ajax.
     *
     * @return string
     */
    public function getAjaxLoadScheduleUrl()
    {
        return $this->_backendUrl->getUrl('storepickupadmin/store/scheduletable');
    }
}
