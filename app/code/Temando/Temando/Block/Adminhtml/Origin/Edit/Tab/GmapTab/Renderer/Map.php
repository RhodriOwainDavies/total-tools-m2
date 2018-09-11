<?php

namespace Temando\Temando\Block\Adminhtml\Origin\Edit\Tab\GmapTab\Renderer;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\UrlInterface;

class Map extends \Magento\Backend\Block\Widget implements RendererInterface
{
    protected $_template = 'Temando_Temando::origin/map.phtml';

    /**
     * Location Input IDs
     *
     * @var array
     */
    protected $_locationInputIds = [
        'address',
        'zoom_level',
        'city',
        'zipcode',
        'country_id',
        'latitude',
        'longitude',
        'zoom_level',
    ];

    /**
     * JSON Keys
     *
     * @var array
     */
    protected $_jsonKeys = [
        'latitude',
        'longitude',
        'zoom_level',
        'marker_icon',
    ];

    /**
     * Core Registry.
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * System Config.
     *
     * @var \Magestore\Storepickup\Model\SystemConfig
     */
    protected $_systemConfig;

    /**
     * Map constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\Storepickup\Model\SystemConfig $systemConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\Storepickup\Model\SystemConfig $systemConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_coreRegistry = $registry;
        $this->_systemConfig = $systemConfig;
    }

    /**
     * Get Google API Key.
     *
     * @param null $store
     *
     * @return mixed
     */
    public function getGoolgeApiKey($store = null)
    {
        return $this->_systemConfig->getGoolgeApiKey();
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
     * Get Registry Model
     *
     * @return \Temando\Temando\Model\Origin
     */
    public function getRegistryModel()
    {
        return $this->_coreRegistry->registry('temando_origin');
    }

    /**
     * Get HTML ID Prefix
     *
     * @return mixed
     */
    public function getHtmlIdPrefix()
    {
        return $this->getElement()->getForm()->getHtmlIdPrefix();
    }

    /**
     * Get Selector Element
     *
     * @param string $elementId
     *
     * @return string
     */
    public function getSelectorElement($elementId = '')
    {
        return '#'.$this->getHtmlIdPrefix().$elementId;
    }

    /**
     * Get Option Map JSON
     *
     * @return string
     */
    public function getOptionMapJson()
    {
        $store = $this->getRegistryModel();

        foreach ($this->_locationInputIds as $input) {
            $store->setData('input_'.$input, $this->getSelectorElement($input));
            $this->_jsonKeys[] = 'input_'.$input;
        }

        return $store->toJson($this->_jsonKeys);
    }
}
