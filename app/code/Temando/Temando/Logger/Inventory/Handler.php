<?php

/*
 * Cron Custom Log Handler
 */

namespace Temando\Temando\Logger\Inventory;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    protected $fileName = "/var/log/inventory-import.log";
    protected $loggerType = \Monolog\Logger::DEBUG;
}
