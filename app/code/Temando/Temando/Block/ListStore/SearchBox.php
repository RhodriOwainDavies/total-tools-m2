<?php

namespace Temando\Temando\Block\ListStore;

class SearchBox extends \Magestore\Storepickup\Block\AbstractBlock
{
    protected $_template = 'Temando_Temando::liststore/searchbox.phtml';

    /**
     * Local Country.
     *
     * @var \Magento\Config\Model\Config\Source\Locale\Country
     */
    protected $_localCountry;

    /**
     * Temando Helper.
     *
     * @var \Magento\Directory\Helper\Data
     */
    protected $_directoryHelper;

    /**
     * Block constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Magestore\Storepickup\Block\Context $context,
        \Magento\Directory\Helper\Data $directoryHelper,
        \Magento\Config\Model\Config\Source\Locale\Country $localCountry,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_directoryHelper = $directoryHelper;
        $this->_localCountry = $localCountry;
    }

    /**
     * Get Region JSON.
     *
     * @return string
     */
    public function getRegionJson()
    {
        return $this->_directoryHelper->getRegionJson();
    }

    /**
     * Get tag icon.
     *
     * @param \Magestore\Storepickup\Model\Tag $tag
     *
     * @return string
     */
    public function getTagIcon(\Magestore\Storepickup\Model\Tag $tag)
    {
        return $tag->getTagIcon() ? $this->_imageHelper->getMediaUrlImage($tag->getTagIcon())
        : $this->getViewFileUrl('Magestore_Storepickup::images/Hospital_icon.png');
    }

    /**
     * Get Tag HTML
     *
     * @param \Magestore\Storepickup\Model\Tag $tag
     *
     * @return string
     */
    public function getTagHtml(\Magestore\Storepickup\Model\Tag $tag)
    {
        $tagFormat = '<li data-tag-id="%s" class="tag-icon icon-filter text-center">';
        $tagFormat .= '<img src="%s" class="img-responsive"/><p>%s</p></li>';

        return sprintf($tagFormat, $tag->getId(), $this->getTagIcon($tag), $tag->getTagName());
    }

    /**
     * Get Country Option.
     *
     * @return array
     */
    public function getCountryOption()
    {
        return $this->_localCountry->toOptionArray();
    }
}
