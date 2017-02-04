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

    public function hasFunction($functionName) {
        return (is_array($this->gearmanMetrics) && array_key_exists('status', $this->gearmanMetrics) && array_key_exists($functionName, $this->gearmanMetrics['status']));
    }

    public function getRunningTasksByFunction($functionName) {
        $numberTasks = 0;

        if ($this->hasFunction($functionName)) {
            $numberTasks = intval($this->gearmanMetrics['status'][$functionName]['running']);
        }

        return $numberTasks;
    }

    public function getNumberOfWorkersByFunction($functionName) {
        $numberWorkers = 0;

        if ($this->hasFunction($functionName)) {
            $numberWorkers = intval($this->gearmanMetrics['status'][$functionName]['workers']);
        }

        return $numberWorkers;
    }

    public function getUnfinishedTasksByFunction($functionName) {
        $numberTasks = 0;

        if ($this->hasFunction($functionName)) {
            $numberTasks = intval($this->gearmanMetrics['status'][$functionName]['unfinished']);
        }

        return $numberTasks;
    }

    private function pollServer() {
        if (is_null($this->gearmanServer)) {
            throw new GearmanStatusException('No gearman server configured', 1486230420);
        }

        $errorNumber = 0;
        $errorString = '';

        $socket = @fsockopen($this->gearmanServer->getHost(), $this->gearmanServer->getPort(), $errorNumber, $errorString, $this->gearmanServer->getTimeout());
        if ($socket != NULL) {
            $this->command($socket, 'status');
            $this->command($socket, 'workers');
            fclose($socket);
        } else {
            throw new GearmanStatusException($errorString, $errorNumber);
        }
    }

    protected function command($socket, $command) {
        fwrite($socket, $command . PHP_EOL);
        while (!feof($socket)) {
            $line = trim(fgets($socket, 4096));
            if ($line == '.') {
                break;
            }
            $parserResult = call_user_func(__NAMESPACE__ . '\Parser\GearmanParser::' . $command . 'Line', $line);
            if (!empty($parserResult)) {
                $this->gearmanMetrics[$command] = $parserResult;
            }
        }
    }
}