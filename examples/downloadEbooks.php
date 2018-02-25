<?php

include_once __DIR__ . '/../vendor/autoload.php';

use Alphabox\PacktubCrawler\PacktubSite;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('stdout');
$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

//Login to Packtub site
$logger->info("Login to packtub.com");
$packtub = new PacktubSite($logger);
$packtub->login('xxxx@xxxxxx.xx', "xxxxxxxx");

$logger->info("Get and list available books.");
foreach( $packtub->getAvailableBooks() as $title => $formats ) {
    $logger->info($title);
}

?>
