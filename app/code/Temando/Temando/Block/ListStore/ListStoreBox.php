<?php

namespace Temando\Temando\Block\ListStore;

class ListStoreBox extends \Temando\Temando\Block\AbstractBlock
{
    /**
     * Block constructor
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Temando\Temando\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
}
