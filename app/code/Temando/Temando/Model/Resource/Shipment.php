<?php
namespace Temando\Temando\Model\Resource;

class Shipment extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * \Psr\Log\LoggerInterface
     *
     * @var
     */
    protected $_logger;

    /**
     * \Magento\Backend\Model\Auth\Session
     *
     * @var
     */
    protected $_authSession;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Psr\Log\LoggerInterface $logger
     * @param string|null $resourcePrefix
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Psr\Log\LoggerInterface $logger,
        $resourcePrefix = null
    ) {
        $this->_authSession = $authSession;
        $this->_logger = $logger;

        parent::__construct($context, $resourcePrefix);
        $this->_init('temando_shipment', 'shipment_id');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */

    protected function _construct()
    {
        $this->_init('temando_shipment', 'shipment_id');
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param \Temando\Temando\Model\Origin $object
     *
     * @return \Zend_Db_Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $select = parent::_getLoadSelect($field, $value, $object);

        $user = $this->_authSession->getUser();
        //

        return $select;
    }
}
