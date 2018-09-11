<?php
namespace Temando\Temando\Model\Resource\Pickup;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Define model & resource model
     */
    const TEMANDO_PICKUP = 'temando_pickup';

    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        $this->_init(
            'Temando\Temando\Model\Pickup',
            'Temando\Temando\Model\Resource\Pickup'
        );
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
        $this->storeManager = $storeManager;
    }
    protected function _initSelect()
    {

        parent::_initSelect();

        $this->getSelect()->joinLeft(
            ['temando_origin' => $this->getTable('temando_origin')],
            'main_table.origin_id = temando_origin.origin_id',
            array('*')
        );

        $this->getSelect()->joinLeft(
            ['sales_order' => $this->getTable('sales_order')],
            'main_table.order_id = sales_order.entity_id',
            array('increment_id','created_at','shipping_description')
        );

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_authSession = $objectManager->create('Magento\Backend\Model\Auth\Session');

        $user = $this->_authSession->getUser();
        if ($user->getAclRole()!=1) {
            $this->getSelect()->where(
                'user_ids = '.$user->getId() .
                ' OR user_ids LIKE \'%,'.$user->getId().',%\'' .
                ' OR user_ids LIKE \'%,'.$user->getId().'\'' .
                ' OR user_ids LIKE \''.$user->getId().',%\''
            );
        }
    }
}
