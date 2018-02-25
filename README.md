# packtub-crawler
## Introduction
packtub-crawler is a simple, minimalistic PHP library to crawl your ebooks from https://www.packtpub.com.
You can list your available books, and download those in different formats with source code (if available).

## Installation
Install the latest version with
```bash
$ composer require alphabox/packtub-crawler
```

## Usage
A simple usage example to list your all available ebook.
```php
<?php

include_once __DIR__ . '/vendor/autoload.php';

use Alphabox\PacktubCrawler\PacktubSite;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('stdout');
$logger->pushHandler(new StreamHandler('php://stdout', Logger::DEBUG));

//Login to Packtub site
$logger->info("Login to packtub.com");
$packtub = new PacktubSite($logger);
$packtub->login('user@example.com', "supersecretpassword");

$logger->info("Get and list available books.");
foreach( $packtub->getAvailableBooks() as $title => $formats ) {
    $logger->info($title);
}

?>
```

## License
This project licensed under Apache 2.0 License - see the [LICENSE](LICENSE) file for details