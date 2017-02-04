# php-gearman-status
Library for getting status and statistic data of a Gearman Jobserver 

## Requirements

* php >=5.3.0


## Installation

```bash
$ composer require t3sec/gearman-status
```

## Usage

1. Namespacing

```php
use T3sec\GearmanStatus\GearmanMetrics;
use T3sec\GearmanStatus\GearmanServer;
```


2. Basic Example

```php
$server = new GearmanServer();
$gearmanMetrics = new GearmanMetrics($server);

var_dump($gearmanMetrics->getRawData());
```

Uses default configuration of a Gearman Jobserver and returns raw metrics as array.


3. Advanced Example
 
```php
$server = new GearmanServer('192.1.1.10', 4444);
$gearmanMetrics = new GearmanMetrics($server);

$numWorkers = $gearmanMetrics->getNumberOfWorkersByFunction('ReverseIpLookup');
$unfinishedTasks = $gearmanMetrics->getUnfinishedTasksByFunction('ReverseIpLookup');
```

Retrieves metrics of a Gearman jobserver at IP 192.1.1.10 listening at port 4444.
Number of connected workers and unfinished tasks are returned.

4. Exception handling

* \InvalidArgumentException is thrown if a GermanServer is configured with invalid settings
* T3sec\GearmanStatus\Exception\GearmanStatusException is thrown if the connection to the Gearman jobserver cannot be established

