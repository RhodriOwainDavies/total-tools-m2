<?php

namespace Temando\Temando\Block\Product\View;

class Stockcheck extends \Temando\Temando\Block\AbstractBlock
{
    protected $_template = 'Temando_Temando::product/view/stockcheck.phtml';

    /**
     * Block constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array                                            $data
     */
    public function __construct(
        \Temando\Temando\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
}
