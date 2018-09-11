<?php

namespace Temando\Temando\Cron;

class Inventory
{
    const PROCESSING_DIR = '/processing';
    const PROCESSED_DIR = '/processed';
    const FULL_IMPORT_PREFIX = 'full';
    const PART_IMPORT_PREFIX = 'part';
    const KEEP_LOG_FILES_FOR_DAYS = 7;
    protected $_debug = false;

    /**
     * Logger
     *
     * @var \Temando\Temando\Logger\Inventory\Logger
     */
    protected $_inventoryImportLogger;

    /**
     * Resource Connection
     *
     * @var \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * Scope Config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * Keep import file time in days
     *
     * @var String
     */
    protected $_keepLogFilesForDays;

    /**
     * Inventory Import Dir
     *
     * @var mixed
     */
    protected $_inventoryImportDir;
    protected $_originInventoryTbl;
    protected $_connection;

    /**
     * Inventory constructor.
     *
     * @param \Temando\Temando\Logger\Logger $inventoryImportLogger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct(
        \Temando\Temando\Logger\Inventory\Logger $inventoryImportLogger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->_inventoryImportLogger = $inventoryImportLogger;
        $this->_scopeConfig = $scopeConfig;
        $this->_resourceConnection = $resourceConnection;

        $this->_inventoryImportDir = $this->_scopeConfig->getValue(
            'temando/inventory/directory',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $this->_keepLogFilesForDays = $this->_scopeConfig->getValue(
            'temando/inventory/days',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (!empty($this->_scopeConfig->getValue(
            'temando/inventory/execute_rows',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        ))) {
            $this->_batchsize = $this->_scopeConfig->getValue(
                'temando/inventory/execute_rows',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
        
        $this->_debug = $this->_scopeConfig->getValue(
            'temando/inventory/logs',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
  
        if ($this->_debug) {
            $this->_inventoryImportLogger->debug(
                '===== Temando Inventory Import Cron ====='
            );
        }
        if ($this->_debug) {
            if (is_numeric($this->_keepLogFilesForDays)) {
                $this->_inventoryImportLogger->debug(
                    'The length of time to preserve logged file is ' .
                    $this->_scopeConfig->getValue(
                        'temando/inventory/days',
                        \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                    ) .
                    ' days'
                );
            } else {
                $this->_keepLogFilesForDays = self::KEEP_LOG_FILES_FOR_DAYS;
                $this->_inventoryImportLogger->debug(
                    'The length of time to preserve logged file is not specified' .
                    ' in system config temando/inventory/days' .
                    ' - continuing with default value ' . $this->_keepLogFilesForDays . ' days'
                );
            }
        }
    }

    public function execute()
    {
        if (is_null($this->_inventoryImportDir)) {
            if ($this->_debug) {
                $this->_inventoryImportLogger->debug(
                    'No directory specified in system config temando/inventory/directory - exit'
                );
            }
            return false;
        }

        if (!file_exists($this->_inventoryImportDir)) {
            if ($this->_debug) {
                $this->_inventoryImportLogger->debug(
                    'The directory sepcified in temando/inventory/directory does not exit - exit'
                );
            }
            return false;
        }

        $this->createFolders();
        $this->deleteOldImports();

        //check if lock file exists
        $lockFile = $this->_inventoryImportDir.'/'.'import.lock';

        if (!file_exists($lockFile)) {
            //create lock file
            $fp = fopen($lockFile, "wb");
            fwrite($fp, "Temando Import Inventory process started at ".date('Y/m/d H:i:s'));
            fclose($fp);

            //setup DB objects
            $this->_connection = $this->_resourceConnection->getConnection();

            //gives table name with prefix
            $this->_originInventoryTbl = $this->_resourceConnection->getTableName('temando_origin_inventory');

            //scan folder for files to import
            $inventoryImportFiles = scandir($this->_inventoryImportDir);

            //for each file in folder
            foreach ($inventoryImportFiles as $index => $importFile) {
                if (is_dir($this->_inventoryImportDir . '/' . $importFile) || $importFile == 'import.lock') {
                    continue;
                }
                    
                //check if it's a CSV file
                $importFileParts = pathinfo($importFile);
                if ($this->_debug) {
                    $this->_inventoryImportLogger->debug(
                        'Processing file ' . $importFile
                    );
                }
                if ((array_key_exists('extension', $importFileParts)) && ($importFileParts['extension'] == "csv")) {
                    //move to processing directory
                    if ($this->_debug) {
                        $this->_inventoryImportLogger->debug(
                            ' Move ' . $this->_inventoryImportDir . $importFile . ' to ' .
                            $this->_inventoryImportDir . self::PROCESSING_DIR . '/' . $importFile
                        );
                    }
                    rename(
                        $this->_inventoryImportDir .'/'. $importFile,
                        $this->_inventoryImportDir . self::PROCESSING_DIR . '/' . $importFile
                    );

                    //open file for reading
                    if (($handle = fopen(
                        $this->_inventoryImportDir . self::PROCESSING_DIR . '/' . $importFile,
                        "r"
                    )) !== false
                    ) {
                        $values = array();
                        
                        // array without duplicate erp_id and sku combination
                        $uniqueArr = array();

                        //read each line in file
                        $index = 0;
                        $start = 1;
                        $totalExecutedTime = 0;
                        while (($data = fgetcsv($handle, $this->_batchsize, ",")) !== false) {
                            $index++;
                            if (!empty(trim($data[0])) && !empty(trim($data[1]))) {
                                $values[] = array(
                                    'erp_id' => trim($data[0]),
                                    'sku' => trim($data[1]),
                                    'units' => trim($data[2])
                                );
                                $uniqueArr[trim($data[0]) . trim($data[1])] = trim($data[2]);
                                
                                if ($index % $this->_batchsize == 0) {
                                    if ($this->_debug) {
                                        $this->_inventoryImportLogger->debug(
                                            ' Execute insert from row # ' . $start . ' to ' . $index
                                        );
                                    }
                                    $totalExecutedTime += $this->executeInsertCSV($values);
                                    // refresh array
                                    $values = [];
                                    $start = $index + 1;
                                }
                            }
                        }
                        
                        // execute the last query
                        if ($index % $this->_batchsize != 0) {
                            if ($this->_debug) {
                                $this->_inventoryImportLogger->debug(
                                    ' Execute insert from row # ' . $start . ' to ' . $index
                                );
                            }
                            $totalExecutedTime += $this->executeInsertCSV($values);
                        }
                        
                        if ($this->_debug) {
                            $this->_inventoryImportLogger->debug(
                                ' Parsed ' . $index . ' rows from CSV file ' . $importFile . ', found ' .
                                count($uniqueArr) . ' unique rows (therefore '.
                                ($index - count($uniqueArr)) . ' duplicates)'
                            );
                            $this->_inventoryImportLogger->debug(
                                ' All queries took ' . $totalExecutedTime . ' seconds'
                            );
                        }
                    } else {
                        //couldn't open file
                        if ($this->_debug) {
                            $this->_inventoryImportLogger->debug(
                                'Couldn\'t open file ' . $importFile
                            );
                        }
                    }
                    
                    //close import file handle
                    fclose($handle);
              
                    //create unique file to rename too
                    $renameImportFile = $importFileParts['filename'].'_'.time().'.csv';

                    //move import file from processing folder to processed folder
                    if ($this->_debug) {
                        $this->_inventoryImportLogger->debug(
                            ' Finished with file ' . $importFile
                        );
                        
                        $this->_inventoryImportLogger->debug(
                            ' Move ' . $this->_inventoryImportDir . self::PROCESSING_DIR . '/' .
                            $importFile . ' to ' . $this->_inventoryImportDir . self::PROCESSED_DIR . '/' .
                            $renameImportFile
                        );
                    }
                    rename(
                        $this->_inventoryImportDir . self::PROCESSING_DIR . '/' . $importFile,
                        $this->_inventoryImportDir . self::PROCESSED_DIR . '/' . $renameImportFile
                    );
                } else {
                    // file is not csv
                    if ($this->_debug) {
                        $this->_inventoryImportLogger->debug(
                            ' File is not CSV extension ' . $importFile
                        );
                    }
                }
            }

            //send notification
            if ($this->_debug) {
                $this->_inventoryImportLogger->debug(
                    'Delete lock file'
                );
            }
            unlink($lockFile);
        } else {
            //import already in process or last one failed :(
            if ($this->_debug) {
                $this->_inventoryImportLogger->debug(
                    'Process is locked'
                );
            }
        }
        if ($this->_debug) {
            $this->_inventoryImportLogger->debug(
                'Finished execute()'
            );
        }
        return $this;
    }

    /**
     * Execute Insert query to temando_origin_inventory table
     *
     * @param array $store_id
     *
     * @return float
     */
    private function executeInsertCSV($arr)
    {
        $executedTime = 0;
        $insert = "INSERT INTO " . $this->_originInventoryTbl . " (erp_id, sku, units) VALUES ";
        foreach ($arr as $row) {
            $insert .= "('" . $row['erp_id'] . "', '" . addSlashes($row['sku']) . "', '" .
                $row['units'] . "'),";
        }
        $insert = rtrim($insert, ',') .
            " ON DUPLICATE KEY UPDATE `units` = VALUES(`units`);";
    
        if ($this->_debug) {
            $this->_inventoryImportLogger->debug(
                ' -- Query size: ' . round((mb_strlen($insert, '8bit') / 1024)) . " KB"
            );
        }
        
        $start = microtime(true);
        try {
            $this->_connection->query($insert);
        } catch (Exception $e) {
            if ($this->_debug) {
                $this->_inventoryImportLogger->debug($e->getMessage());
            }
        }
        $end = microtime(true);
        $executedTime = $end - $start;
        if ($this->_debug) {
            $this->_inventoryImportLogger->debug(
                ' -- Executed time: ' . ($executedTime * 1000) . " ms"
            );
        }
        return $executedTime;
    }
    
    private function createFolders()
    {
        if (!file_exists($this->_inventoryImportDir)) {
            mkdir($this->_inventoryImportDir);
            mkdir($this->_inventoryImportDir.self::PROCESSING_DIR);
            mkdir($this->_inventoryImportDir.self::PROCESSED_DIR);
        } else {
            if (!file_exists($this->_inventoryImportDir.self::PROCESSING_DIR)) {
                mkdir($this->_inventoryImportDir.self::PROCESSING_DIR);
            }
            if (!file_exists($this->_inventoryImportDir.self::PROCESSED_DIR)) {
                mkdir($this->_inventoryImportDir.self::PROCESSED_DIR);
            }
        }
    }

    private function deleteOldImports()
    {
        $now = time();
        $files = scandir($this->_inventoryImportDir.self::PROCESSED_DIR);
        // remove . and .. out of array
        $files = array_diff($files, array('.','..'));

        foreach ($files as $file) {
            if (is_dir($this->_inventoryImportDir . self::PROCESSED_DIR . '/' . $file)) {
                continue;
            }
                
            $absoluteFile = $this->_inventoryImportDir.self::PROCESSED_DIR . '/' . $file;
            if (is_file($absoluteFile)) {
                $fileParts = pathinfo($absoluteFile);
                if ((array_key_exists('extension', $fileParts)) && ($fileParts['extension'] == "csv")) {
                    if ($now - fileatime($absoluteFile) >= 60 * 60 * 24 * $this->_keepLogFilesForDays) {
                        if ($this->_debug) {
                            $this->_inventoryImportLogger->debug(
                                $absoluteFile . ' is deleted because it is old enough'
                            );
                        }
                        unlink($absoluteFile);
                    }
                } else {
                    if ($this->_debug) {
                        $this->_inventoryImportLogger->debug(
                            $absoluteFile . ' is not a CSV file'
                        );
                    }
                }
            } else {
                if ($this->_debug) {
                    $this->_inventoryImportLogger->debug(
                        $absoluteFile . ' is not a file'
                    );
                }
            }
        }
    }
}
