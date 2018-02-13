<?php

namespace Alphabox\PacktubCawler;

class Connection {

    private $uri;

    private $method;

    private $headers;

    private $ch;
    
    private $cookies;

    public function __construct( $uri, $method, $headers, $cookies = null ) {
        $this->uri = $uri;
        $this->method = $method;
        $this->headers = $headers;
        $this->cookies = $cookies;

        $this->ch = curl_init( $uri );
        $this->setCookies($this->cookies);
        
        curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $this->ch, CURLOPT_HTTPHEADER, $this->headers );
        curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $this->ch, CURLOPT_CONNECTTIMEOUT, 5 );
    }

    public function setPostFields($postFields) {
        curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $postFields );
    }

    public function getContent() {
        $content = curl_exec( $this->ch );
        $this->cookies = curl_getinfo($this->ch, CURLINFO_COOKIELIST);
        return $content;
    }
    
    public function setCookies($cookies) {
        if ( is_array( $cookies ) ) {
            foreach ($cookies as $cookie) {
                curl_setopt($this->ch, CURLOPT_COOKIELIST, $cookie);            
            }            
        } else {
            curl_setopt($this->ch, CURLOPT_COOKIELIST, null);
        }
    }

    public function getCookies() {
        return $this->cookies;
    }
    
    public function getContentToFile( $path ) {
        $fd = fopen( $path, 'w' );
        curl_setopt( $this->ch, CURLOPT_FILE, $fd );
        curl_exec( $this->ch );
        fclose( $fd );
    }

    public function close() {
        curl_close( $this->ch );
    }

    public function __destruct() {
        if ( is_resource($this->ch) ) {
            curl_close( $this->ch );
        }
    }
    
}

?>
