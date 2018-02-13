<?php

namespace Alphabox\PacktubCawler;

use DOMDocument;
use DOMXPath;
use Monolog\Logger;

class PacktubSite {
    
    private $conn;
    private $logger;
    private $headers;
    private $books;
    private $cookies;
    
    public function __construct( Logger $logger ) {
        $this->logger = $logger;
        $this->books = array();
        $this->headers = array(
            'Accept-language: en'
        );
    }
    
    public function login($username, $password) {
        $this->conn = new Connection('https://www.packtpub.com/register', 'GET', $this->headers);
        
        $dom = new DOMDocument();
        $dom->validateOnParse = false;
        $dom->preserveWhiteSpace = false;
        @$dom->loadHTML( $this->conn->getContent() );
        $domXPath = new DOMXPath( $dom );
        $this->conn->close();
        
        $postData = array(
            'email' => $username,
            'password' => $password,
            'op' => 'Login',
            'form_id' => 'packt_user_login_form'
        );
        foreach( $domXPath->query("//form[@id='packt-user-login-form']//input[@type='hidden' and contains(@name, 'form')]") as $entry ) {
            $postData[$entry->getAttribute('name')] = $entry->getAttribute('value');
        }
        
        // Login to packtpub.com site
        $this->conn = new Connection( 'https://www.packtpub.com/register', 'POST', $this->headers );
        $this->conn->setPostFields( $postData );
        $this->conn->getContent();
        $this->cookies = $this->conn->getCookies();
        $this->conn->close();
    }
    
    public function getAvailableBooks() {
        $this->conn = new Connection( 'https://www.packtpub.com/account/my-ebooks', 'GET', $this->headers, $this->cookies );
        
        libxml_use_internal_errors(true);
        $dom = new DOMDocument();
        $dom->validateOnParse = false;
        $dom->preserveWhiteSpace = false;
        $dom->loadHTML( $this->conn->getContent() );
        libxml_clear_errors();
        $this->conn->close();
        $domXPath = new DOMXPath( $dom );
        
        $this->logger->debug('Parse site for available ebooks.' );
        $this->books = array();
        foreach( $domXPath->query("//div[@class='product-line unseen']") as $entry ) {
            if ( $entry->hasAttribute('title') ) {
                $title = str_replace('/', '_', $entry->getAttribute('title') );
                $this->books[$title] = array();
                foreach( $domXPath->query("./div[@class='product-buttons-line toggle']/div[@class='download-container cf ']/a", $entry) as $child ) {
                    if ( $child->hasAttribute('href') && $child->getAttribute('href') != '#' ) {
                        $end = substr( strrchr( $child->getAttribute('href'), '/' ), 1 );
                        if( $end != '' ) {
                            if ( is_numeric( $end ) ) {
                                $end = 'zip';
                            }
                            $this->books[$title][$end] = 'https://packtpub.com' . $child->getAttribute('href');
                        }
                    }
                }
            }
        }
        return $this->books;
    }
    
    public function downloadBook($bookName, $format, $baseDirectory) {
        if ( ! array_key_exists($bookName, $this->books) ) {
            $this->getAvailableBooks();
            if ( ! array_key_exists($bookName, $this->books) ) {
                return null;
            }
        }
        if( ! is_dir( $baseDirectory . DIRECTORY_SEPARATOR . $bookName )  ) {
            $this->logger->info('Make new directory: ' . $baseDirectory . DIRECTORY_SEPARATOR . $bookName);
            mkdir( $baseDirectory . DIRECTORY_SEPARATOR . $bookName, '0750', true );
        }
        
        if( ! file_exists( $baseDirectory . DIRECTORY_SEPARATOR . $bookName . DIRECTORY_SEPARATOR . $bookName . '.' . $format ) ) {
            $this->logger->info('Download file: ' . $bookName . '.' . $format . ': ' . $this->books[$bookName][$format] );
            $download = new Connection( $this->books[$bookName][$format], 'GET', $this->headers, $this->cookies );
            $download->getContentToFile( $baseDirectory . DIRECTORY_SEPARATOR . $bookName . DIRECTORY_SEPARATOR . $bookName . '.' . $format );
            $download->close();
        }
    }
}