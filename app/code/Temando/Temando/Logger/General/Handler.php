<?php

/*
 * Cron Custom Log Handler
 */

namespace Temando\Temando\Logger\General;

class Handler extends \Magento\Framework\Logger\Handler\Base
{
    protected $fileName = "/var/log/temando.log";
    protected $loggerType = \Monolog\Logger::DEBUG;
}
