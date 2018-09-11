<?php

namespace Temando\Temando\Block\ListStore;

class MapBox extends \Magestore\Storepickup\Block\AbstractBlock
{
    protected $_template = 'Magestore_Storepickup::liststore/mapbox.phtml';

    public function __construct(
        \Magestore\Storepickup\Block\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }
}
