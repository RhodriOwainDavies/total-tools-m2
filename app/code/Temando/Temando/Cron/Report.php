<?php

namespace Temando\Temando\Cron;

class Report
{
    const TEMANDO_REPORT_DIR = '/temando_report';
     
    /**
     * Directory List
     *
     * @var \Magento\Framework\App\Filesystem\DirectoryList
     */
    protected $_directoryList;

    /**
     * Log
     *
     * @var \Temando\Temando\Logger\Inventory\Logger
     */
    protected $_logger;

    /**
     * Scope Config Interface
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $_csvProcessor;

    /**
     * File System
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * Shipment Collection
     *
     * @var \Temando\Temando\Model\ResourceModel\Shipment\Collection
     */
    protected $_shipment;

    /**
     * Pickup Collection
     *
     * @var \Temando\Temando\Model\ResourceModel\Pickup\Collection
     */
    protected $_pickup;

    /**
     * Origin Collection
     *
     * @var \Temando\Temando\Model\ResourceModel\Origin\Collection
     */
    protected $_origin;

    /**
     * Transport Builder
     *
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $_transportBuilder;

    /**
     * Store Manager Interface
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Shipment Status
     *
     * @var \Temando\Temando\Model\System\Config\Source\Shipment\Status
     */
    protected $_shipmentStatus;

    /**
     * Pickup Status
     *
     * @var \Temando\Temando\Model\System\Config\Source\Pickup\Status
     */
    protected $_pickupStatus;

    /**
     * Temando Quote Collection
     *
     * @var \Temando\Temando\Model\ResourceModel\Quote\Collection
     */
    protected $_quote;

    /**
     * Sale Order Collection
     *
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $_order;
    
    protected $_shipmentReportPath;
    protected $_shipmentReportFile;
    protected $_pickupReportPath;
    protected $_pickupReportFile;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\Filesystem $filesystem,
        \Temando\Temando\Model\ResourceModel\Shipment\Collection $shipment,
        \Temando\Temando\Model\ResourceModel\Pickup\Collection $pickup,
        \Temando\Temando\Model\ResourceModel\Origin\Collection $origin,
        \Temando\Temando\Model\ResourceModel\Quote\Collection $quote,
        \Magento\Sales\Model\ResourceModel\Order\Collection $order,
        \Temando\Temando\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Temando\Temando\Model\System\Config\Source\Shipment\Status $shipmentStatus,
        \Temando\Temando\Model\System\Config\Source\Pickup\Status $pickupStatus
    ) {
        $this->_logger = $logger;
        $this->_scopeConfig = $scopeConfig;
        $this->_directoryList = $directoryList;
        $this->_csvProcessor = $csvProcessor;
        $this->_filesystem = $filesystem;
        $this->_shipment = $shipment;
        $this->_pickup = $pickup;
        $this->_origin = $origin;
        $this->_quote = $quote;
        $this->_order = $order;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_shipmentStatus = $shipmentStatus;
        $this->_pickupStatus = $pickupStatus;
    }

    public function execute()
    {
        $email = $this->_scopeConfig->getValue(
            'temando/report/email',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        $name = $this->_scopeConfig->getValue(
            'temando/report/name',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (empty($email) || empty($name)) {
            $this->_logger->debug('Please enter name and email under Temando Setting > Daily Shipment Report');
            return;
        }

        $dir = $this->_directoryList->getPath('var') . self::TEMANDO_REPORT_DIR;

        try {
            if (!file_exists($dir)) {
                mkdir($dir);
            }
        } catch (Exception $e) {
            $this->_logger->debug('Shipmentreport.php fail to create a directory');
            $this->_logger->debug($e->getMessage());
        }

        $date = date("Y-m-d", strtotime("-3 Months"));
        
        $this->_shipmentReportFile = 'temando-shipment-' . $date . '.csv';
        $this->_shipmentReportPath = $dir . '/' . $this->_shipmentReportFile;
        
        $this->_pickupReportFile = 'temando-pickup-' . $date . '.csv';
        $this->_pickupReportPath = $dir . '/' . $this->_pickupReportFile;
        
        $this->createShipmentReport($date);
        $this->createPickupReport($date);
        try {
            // send an email with temando and pickup csv report
            $this->sendCsvReport($name, $email);
        } catch (Exception $e) {
            $this->_logger->debug('execute');
            $this->_logger->debug($e->getMessage());
        }
    }

    /**
     * Create shipment report
     */
    public function createShipmentReport($date)
    {
        try {
            $shipmentCollection = $this->_shipment
                ->addFieldToSelect('order_created_at')
                ->addFieldToSelect('shipment_id')
                ->addFieldToSelect('order_id')
                ->addFieldToSelect('status')
                ->addFieldToSelect('customer_selected_quote_description')
                ->addFieldToFilter('order_created_at', array('gt' => $date))
                ->load()
                ->join(
                    ['origin' => $this->_origin->getTable('temando_origin')],
                    'main_table.origin_id = origin.origin_id',
                    ['name']
                )->join(
                    ['temando_quote' => $this->_quote->getTable('temando_quote')],
                    'main_table.customer_selected_quote_id = temando_quote.quote_id',
                    ['total_price']
                )->join(
                    ['sales_order' => $this->_order->getTable('sales_order')],
                    'main_table.order_id = sales_order.entity_id',
                    ['increment_id']
                );
        } catch (Exception $e) {
            $this->_logger->debug('createShipmentReport');
            $this->_logger->debug($e->getMessage());
        }
        // set heading for csv
        $data = array(
                    array(
                        'order_created_at' => 'order created at',
                        'shipment_id' => 'shipment id',
                        'order_id' => 'order id',
                        'order_increment_id' => 'order_increment_id',
                        'status' => 'status',
                        'customer_selected_quote_description' => 'quote selected',
                        'anticipated_cost' => 'anticipated cost',
                        'name' => 'origin'
                    )
                );

        foreach ($shipmentCollection->getData() as $record) {
            $data[] = array(
                'order_created_at' => $record['order_created_at'],
                'shipment_id' => $record['shipment_id'],
                'order_id' => $record['order_id'],
                'order_increment_id' =>  $record['increment_id'],
                'status' => (string)$this->_shipmentStatus->getOptionLabel($record['status']),
                'customer_selected_quote_description' => $record['customer_selected_quote_description'],
                'anticipated_cost' => $record['total_price'],
                'name' => $record['name']
            );
        }
        
        $this->saveCSV($this->_shipmentReportPath, $data);
    }

    /**
     * Create Pickup report
     */
    public function createPickupReport($date)
    {
        $pickupCollection = $this->_pickup
            ->addFieldToSelect('order_id')
            ->addFieldToSelect('status')
            ->addFieldToSelect('origin_id')
            ->addFieldToSelect('customer_selected_origin')
            ->addFieldToSelect('order_created_at')
            ->addFieldToSelect('ready_date')
            ->addFieldToSelect('collected_date')
            ->addFieldToFilter('order_created_at', array('gt' => $date))
            ->load();
    
        // set heading for csv
        $data = array(
                    array(
                        'order_id' => 'order id',
                        'status' => 'status',
                        'customer_selected_origin' => 'customer selected store',
                        'origin_id' => 'Admins select store',
                        'order_created_at' => 'order created at',
                        'ready_date' => 'ready date',
                        'collected_date' => 'collected date'
                    )
                );

        foreach ($pickupCollection->getData() as $record) {
            // convert status to label
            $record['status'] = (string)$this->_pickupStatus->getOptionLabel($record['status']);
            array_push($data, $record);
        }
        $this->saveCSV($this->_pickupReportPath, $data);
    }

    /**
     * Save csv file
     */
    public function saveCSV($filePath, $data)
    {
        try {
            /* pass data to write in csv file */
            $this->_csvProcessor
                ->setEnclosure('"')
                ->setDelimiter(',')
                ->saveData($filePath, $data);
        } catch (Exception $e) {
            $this->_logger->debug($e->getMessage());
        }
    }

    /**
     * Send an email
     */
    public function sendCsvReport($name, $email)
    {
        $store = $this->_storeManager->getStore()->getId();
        $transport = $this->_transportBuilder
            ->setTemplateIdentifier('temando_report_template')
            ->setTemplateOptions(['area' => 'frontend', 'store' => $store])
            ->setTemplateVars(
                [
                    'store' => $this->_storeManager->getStore(),
                ]
            )
            ->setFrom('general')
            ->addTo($email, $name)
            ->addAttachment(
                file_get_contents($this->_shipmentReportPath),
                \Zend_Mime::TYPE_OCTETSTREAM,
                \Zend_Mime::DISPOSITION_ATTACHMENT,
                \Zend_Mime::ENCODING_BASE64,
                $this->_shipmentReportFile
            )
            ->addAttachment(
                file_get_contents($this->_pickupReportPath),
                \Zend_Mime::TYPE_OCTETSTREAM,
                \Zend_Mime::DISPOSITION_ATTACHMENT,
                \Zend_Mime::ENCODING_BASE64,
                $this->_pickupReportFile
            )
            ->getTransport()
            ->sendMessage();
    }
}
