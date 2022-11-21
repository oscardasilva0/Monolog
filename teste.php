<?php
require_once 'vendor/autoload.php';

require_once 'LoggerPT.php';

use Monolog\Logger;
use Patmed\LoggerPT as Log;

//(new Log('5_25'))->log('testeees');
(new Log('5_25'))->sqlite('Terona', Logger::INFO);
