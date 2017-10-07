<?php

include_once __DIR__ . '/../vendor/autoload.php';

use Alphabox\PacktubCawler\PacktubSite;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

const DOWNLOAD_PATH = '/home/books';
const COOKIE_FILE_PATH = __DIR__ . '/cookie.txt';

$logger = new Logger('stdout');
$logger->pushHandler(new StreamHandler('php://stdout', Logger::INFO));

$headers = array( 'Accept-language: en' );

//Login to Packtub site
$logger->info("Login to packtub.com");
$packtub = new PacktubSite($logger, COOKIE_FILE_PATH);
$packtub->login('xxxx@xxxxxx.xx', "xxxxxxxx");

$logger->info("Download all ebook with all format.");
foreach( $packtub->getAvailableBooks() as $title => $formats ) {
    foreach( $formats as $format => $url ) {
        $packtub->downloadBook($title, $format, DOWNLOAD_PATH);        
    }
}

?>
