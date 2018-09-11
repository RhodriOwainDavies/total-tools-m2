<?php

namespace Temando\Temando\Helper;

class Magestoredata
{
    protected $_converter;
    protected $_request;
    protected $_originFactory;
    
    public function __construct(
        \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter,
        \Magento\Framework\App\RequestInterface $request,
        \Temando\Temando\Model\OriginFactory $originFactory
    ) {
        $this->_converter = $converter;
        $this->_request = $request;
        $this->_originFactory = $originFactory;
    }
    
    public function aroundGetTreeSelectedValues()
    {
        $originId = $this->_request->getParam('origin_id');
        $methodGetterId = $this->_request->getParam('method_getter_id');
        
        $origin = $this->_originFactory->create()->load($originId);
        $ids = $origin->runGetterMethod($methodGetterId);
    
        return $this->_converter->toTreeArray($ids) ;
    }
}
