<?php
/**
 * Created by PhpStorm.
 * User: ma.krause
 * Date: 04.02.17
 * Time: 18:27
 */

namespace T3sec\GearmanStatus;


class GearmanServer
{
    const DEFAULT_PORT = 4730;

    const DEFAULT_TIMEOUT = 5;


    /**
     * @var string
     */
    private $host;

    /*
     * @var int
     */
    private $port;

    /**
     * @var int
     */
    private $timeout;


    /**
     * GearmanServer constructor.
     *
     * @var  string  $host  (optional) host name
     * @var  int  $port  (optional) port number
     * @var  int  $timeout  (optional) timeout
     */
    public function __construct($host = '127.0.0.1', $port = self::DEFAULT_PORT, $timeout = self::DEFAULT_TIMEOUT)
    {
        $this->host = $this->validateHost($host);
        $this->port = $this->validatePort($port);
        $this->timeout = $this->validateTimeout($timeout);
    }

    /**
     * @return  string  host name
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * @return  int  port number
     */
    public function getPort() {
        return $this->port;
    }

    /**
     * @return  int  timeout
     */
    public function getTimeout() {
        return $this->timeout;
    }

    /**
     * @param  string  $host  host name
     * @return  mixed  host name
     * @throws  \InvalidArgumentException in case of invalid host name
     */
    private function validateHost($host) {
        if (!is_null($host) && is_string($host) && !empty($host)) {
            return $host;
        } else {
            throw new \InvalidArgumentException('Gearman server host name invalid', 1486230410);
        }
    }

    /**
     * @param  int  $port  port number
     * @return  int  port number
     * @throws \InvalidArgumentException in case of invalid port number
     */
    private function validatePort($port) {
        if (filter_var($port, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 65535)))) {
            return intval($port);
        } else {
            throw new \InvalidArgumentException('Gearman server port number invalid', 1486230411);
        }
    }

    /**
     * @param  int  $timeout  server timeout
     * @return  int  server timeout
     * @throws \InvalidArgumentException in case of invalid timeout
     */
    private function validateTimeout($timeout) {
        if (filter_var($timeout, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1, 'max_range' => 60)))) {
            return intval($timeout);
        } else {
            throw new \InvalidArgumentException('Gearman server timeout invalid', 1486230412);
        }
    }
}