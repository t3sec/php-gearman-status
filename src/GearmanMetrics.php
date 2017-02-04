<?php

namespace T3sec\GearmanStatus;

use T3sec\GearmanStatus\Exception\GearmanStatusException;


class GearmanMetrics
{
    /**
     * @var  array
     */
    private $gearmanMetrics;

    /**
     * @var  GearmanServer
     */
    private $gearmanServer;


    /**
     * Metrics constructor.
     *
     * @var  GearmanServer  $gearmanServer  (optional) GearmanServer
     */
    public function __construct($gearmanServer = null)
    {
        if (!is_null($gearmanServer)) {
            $this->setGearmanServer($gearmanServer);
        }
    }

    public function setGearmanServer(GearmanServer $gearmanServer) {
        $this->gearmanServer = $gearmanServer;
        $this->gearmanMetrics = null;
    }

    public function getRawData() {
        if (is_null($this->gearmanMetrics) || !is_array($this->gearmanMetrics)) {
            $this->pollServer();
        }
        return $this->gearmanMetrics;
    }

    private function pollServer() {
        if (is_null($this->gearmanServer)) {
            throw new GearmanStatusException('No gearman server configured', 1486230420);
        }

        $errorNumber = 0;
        $errorString = '';

        $socket = @fsockopen($this->gearmanServer->getHost(), $this->gearmanServer->getPort(), $errorNumber, $errorString, $this->gearmanServer->getTimeout());
        if ($socket != NULL) {
            fwrite($socket, "status\n");
            while (!feof($socket)) {
                $line = fgets($socket, 4096);
                if ($line == ".\n") {
                    break;
                }
                if (preg_match("~^(.*)[ \t](\d+)[ \t](\d+)[ \t](\d+)~", $line, $matches)) {
                    $function = $matches[1];
                    $this->gearmanMetrics['operations'][$function] = array(
                        'function' => $function,
                        'total' => $matches[2],
                        'running' => $matches[3],
                        'connectedWorkers' => $matches[4],
                    );
                }
            }
            fwrite($socket, "workers\n");
            while (!feof($socket)) {
                $line = fgets($socket, 4096);
                if ($line == ".\n") {
                    break;
                }
                // FD IP-ADDRESS CLIENT-ID : FUNCTION
                if (preg_match("~^(\d+)[ \t](.*?)[ \t](.*?) : ?(.*)~", $line, $matches)) {
                    $fd = $matches[1];
                    $this->gearmanMetrics['connections'][$fd] = array(
                        'fd' => $fd,
                        'ip' => $matches[2],
                        'id' => $matches[3],
                        'function' => $matches[4],
                    );
                }
            }
            fclose($socket);
        } else {
            throw new GearmanStatusException($errorString, $errorNumber);
        }
    }
}