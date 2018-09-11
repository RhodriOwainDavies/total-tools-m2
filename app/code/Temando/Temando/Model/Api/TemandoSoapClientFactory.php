<?php

namespace Temando\Temando\Model\Api;

/**
 * Class TemandoSoapClientFactory
 *
 * @package Temando\Temando\Model\Api
 */

class TemandoSoapClientFactory
{
    /**
     * Factory method for \Temando\Temado\Model\Api\TemandoSoapClient
     *
     * @param string $wsdl
     * @param array $options
     *
     * @return \SoapClient
     */
    public function create($wsdl, \Psr\Log\LoggerInterface $logger, array $options = [])
    {
        return new \Temando\Temando\Model\Api\TemandoSoapClient($wsdl, $logger, $options);
    }
}
