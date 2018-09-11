<?php

namespace Temando\Temando\Model;

use Magento\Framework\Model\AbstractModel;

class Pickup extends AbstractModel
{
    /**
     * Magento sales order object
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_salesOrder;
    
    /**
     * Temando Origin object
     *
     * @var \Temando\Temando\Model\Origin
     */
    protected $_origin;
    
    
    protected $_salesOrderFactory;
    protected $_originFactory;

    public function __construct(
        \Magento\Sales\Model\OrderFactory $salesOrderFactory,
        \Temando\Temando\Model\OriginFactory $originFactory,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_salesOrderFactory = $salesOrderFactory;
        $this->_originFactory = $originFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
    
    
    
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('Temando\Temando\Model\ResourceModel\Pickup');
    }

    /**
     * Get Order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        $this->_salesOrder = $this->_salesOrderFactory->create()->load($this->getOrderId());
        return $this->_salesOrder;
    }

    /**
     * Get Origin.
     *
     * @return \Temando\Temando\Model\Origin
     */
    public function getOrigin()
    {
        $this->_origin = $this->_originFactory->create()->load($this->getOriginId());
        return $this->_origin;
    }
    
    /**
     * Get Customer Selected Origin.
     *
     * @return \Temando\Temando\Model\Origin
     */
    
    public function getCustomerSelectOrigin()
    {
        $this->_origin = $this->_originFactory->create()->load($this->getCustomerSelectedOrigin());
        return $this->_origin;
    }

    /**
     * Get pickslip filename.
     *
     * @return string
     */
    public function getPickslipFilename()
    {
        return "pickslip-pickup-".$this->getId().'.pdf';
    }
}
