<?php

namespace Temando\Temando\Block\Adminhtml\Widget\Form\Renderer;

use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Gallery extends \Magento\Backend\Block\Template implements RendererInterface
{
    protected $_template = 'Magestore_Storepickup::widget/form/renderer/gallery.phtml';

    /**
     * Abstract Element.
     *
     * @var \Magento\Framework\Data\Form\Element\AbstractElement
     */
    protected $_element;

    /**
     * Gallery constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    /**
     * Get Element.
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     * Set Element.
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     */
    public function setElement($element)
    {
        $this->_element = $element;
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
     * Getter for static view file URL.
     *
     * @param $fileId
     *
     * @return string
     */
    public function getAssetRepoUrl($fileId)
    {
        return $this->_assetRepo->getUrl($fileId);
    }

    /**
     * Get html id.
     *
     * @return array|string
     */
    public function getHtmlId()
    {
        return $this->_escaper->escapeHtml($this->getElement()->getHtmlId());
    }

    /**
     * Get url to upload files.
     *
     * @return string
     */
    public function getUploadUrl()
    {
        return $this->_escaper->escapeHtml($this->getElement()->getUploadUrl());
    }

    /**
     * Get maximum file size to upload in bytes.
     *
     * @return int
     */
    public function getFileMaxSize()
    {
        return $this->getElement()->getFileMaxSize();
    }

    /**
     * Get Image JSON Data.
     *
     * @return mixed
     */
    public function getImageJsonData()
    {
        return $this->getElement()->getImageJsonData();
    }

    public function getMaximumImageCount()
    {
        return $this->getElement()->getMaximumImageCount();
    }
}
