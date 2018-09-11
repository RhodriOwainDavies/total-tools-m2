<?php

namespace Temando\Temando\Block;

class AbstractBlock extends \Magento\Framework\View\Element\Template
{
    /**
     * System Config.
     *
     * @var \Magestore\Storepickup\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * Image Helper.
     *
     * @var \Magestore\Storepickup\Helper\Image
     */
    protected $_imageHelper;

    /**
     * Origin Collection Factory.
     *
     * @var \Magestore\Storepickup\Model\ResourceModel\Store\CollectionFactory
     */
    protected $_originCollectionFactory;

    /**
     * Tag Collection Factory.
     *
     * @var \Magestore\Storepickup\Model\ResourceModel\Tag\CollectionFactory
     */
    protected $_tagCollectionFactory;

    /**
     * Core registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * AbstractBlock constructor.
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        \Temando\Temando\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_systemConfig = $context->getSystemConfig();
        $this->_imageHelper = $context->getImageHelper();
        $this->_originCollectionFactory = $context->getOriginCollectionFactory();
        $this->_tagCollectionFactory = $context->getTagCollectionFactory();
        $this->_coreRegistry = $context->getCoreRegistry();
    }

    /**
     * Get System Config.
     *
     * @return \Magestore\Storepickup\Model\SystemConfig
     */
    public function getSystemConfig()
    {
        return $this->_systemConfig;
    }

    /**
     * Render block HTML.
     *
     * @return string
     */
    protected function _toHtml()
    {
        return $this->_systemConfig->isEnableFrontend() ? parent::_toHtml() : '';
    }

    /**
     * Get Origin Collection.
     *
     * @return \Magestore\Storepickup\Model\ResourceModel\Store\Collection
     */
    public function getOriginCollection()
    {
        return $this->_originCollectionFactory->create();
    }

    /**
     * Get Tag Collection.
     *
     * @return \Magestore\Storepickup\Model\ResourceModel\Tag\Collection
     */
    public function getTagCollection()
    {
        return $this->_tagCollectionFactory->create();
    }

    public function getMediaUrlImage($imagePath = '')
    {
        return $this->_imageHelper->getMediaUrlImage($imagePath);
    }
}
