<?php

namespace Temando\Temando\Block\Adminhtml\Origin\Edit\Tab\GmapTab;

class Region extends \Magento\Backend\Block\Template
{
    protected $_template = 'Temando_Temando::origin/region.phtml';

    /**
     * Directory Helper.
     *
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryHelper;

    /**
     * Region constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Directory\Helper\Data $directoryHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_directoryHelper = $directoryHelper;
    }

    /**
     * Get Region JSON
     *
     * @return string
     */
    public function getRegionJson()
    {
        return $this->_directoryHelper->getRegionJson();
    }

    /**
     * Get registry model.
     *
     * @return \Temando\Temando\Model\Origin
     */
    public function getStore()
    {
        return $this->getParentBlock()->getRegistryModel();
    }
}
