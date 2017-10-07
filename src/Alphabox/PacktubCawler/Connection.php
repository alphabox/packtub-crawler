<?php

namespace Alphabox\PacktubCawler;

class Connection {

    private $uri;

    private $method;

    private $headers;

    private $ch;

    public function __construct( $uri, $method, $headers, $cookiesPath = null ) {
        $this->uri = $uri;
        $this->method = $method;
        $this->headers = $headers;

        $this->ch = curl_init( $uri );
        curl_setopt( $this->ch, CURLOPT_FOLLOWLOCATION, true );
        curl_setopt( $this->ch, CURLOPT_HTTPHEADER, $this->headers );
        curl_setopt( $this->ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $this->ch, CURLOPT_CONNECTTIMEOUT, 5 );
        
        if ( $cookiesPath != null ) {
            curl_setopt( $this->ch, CURLOPT_COOKIEFILE, $cookiesPath );
            curl_setopt( $this->ch, CURLOPT_COOKIEJAR, $cookiesPath );
        }
    }

    public function setPostFields($postFields) {
        curl_setopt( $this->ch, CURLOPT_POSTFIELDS, $postFields );
    }

    public function getContent() {
        return curl_exec( $this->ch );
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
