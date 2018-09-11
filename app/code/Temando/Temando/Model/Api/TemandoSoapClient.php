<?php

namespace Temando\Temando\Model\Api;

class TemandoSoapClient extends \SoapClient
{
    protected $_debug = true;
    protected $_logger;
    
    /**
     * Factory method for \Temando\Temado\Model\Api\TemandoSoapClient
     *
     * @param string $wsdl
     * @param array $options
     *
     * @return \SoapClient
     */
    public function __construct($wsdl, \Psr\Log\LoggerInterface $logger, $options)
    {
        $this->_logger = $logger;
        try {
            parent::__construct($wsdl, $options);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
    
    /**
     * Overwrite SoapClient __doRequest method
     *
     * @param string $request
     * @param string $location
     * @param string $action
     * @param int $version
     * @param bool $one_way
     *
     * @return string $response
     */
    public function __doRequest($request, $location, $action, $version, $one_way = 0)
    {
        if ($this->_debug) {
            $this->_logger->debug($request);
        }
        $response = parent::__doRequest($request, $location, $action, $version, $one_way);
        if ($this->_debug) {
            $this->_logger->debug($response);
        }
        return $response;
    }
}
