<?php
/**
 *  Copyright(c) 2017 rryqszq4 ngxphp@gmail.com
 */

class memcached {

    const VERSION = "0.01";

    private $socket = null;

    public function __construct() {
        try {
            $this->socket = new ngx_socket_tcp();
        } catch (Exception $e) {
            print $e->getMessage()."\n";
        }
    }

    public function set_timeout($timeout = 1000) {
        if (!$this->socket) {
            throw new Exception("Socket not initialized on set timeout.");
        }else {
            $this->socket->settimeout($timeout);
        }

        return $this;
    }

    public function connect($host = "127.0.0.1", $port = 11211) {
        if (!$this->socket) {
            throw new Exception("Socket not initialized on connect.");
        } else {
            $this->socket->connect($host, $port);
        }

        return $this;
    }

    public function close() {
        if (!$this->socket) {
            throw new Exception("Socket not initialized on close.");
        } else {
            $this->socket->close();
        }
    }

    public function add($key = '', $value = '', $expire = 0) {
        if (!$this->socket) {
            throw new Exception("Socket not initialized at add.");
        }

        $flags = 0;

        $len = strlen($value);

        $req = "add {$key} {$flags} {$expire} {$len}\r\n{$value}\r\n";

        $this->socket->send($req);

        $data = $this->socket->receive();

        if ($data == "STORED\r\n") {
            return true;
        }

        return false;
    }

    public function set($key = '', $value = '', $expire = 0) {
        if (!$this->socket) {
            throw new Exception("Socket not initialized at set.");
        }

        $flags = 0;

        $len = strlen($value);

        $req = "set {$key} {$flags} {$expire} {$len}\r\n{$value}\r\n";

        $this->socket->send($req);

        $data = $this->socket->receive();

        if ($data == "STORED\r\n") {
            return true;
        }

        return false;
    }

    public function replace($key = '', $value = '', $expire = 0) {
        if (!$this->socket) {
            throw new Exception("Socket not initialized at replace.");
        }

        $flags = 0;

        $len = strlen($value);

        $req = "replace {$key} {$flags} {$expire} {$len}\r\n{$value}\r\n";

        $this->socket->send($req);

        $data = $this->socket->receive();

        if ($data == "STORED\r\n") {
            return true;
        }

        return false;
    }

    public function get($key = '') {
        if (!$this->socket) {
            throw new Exception("Socket not initialized at get.");
        }

        $req = "get {$key}\r\n";

        $this->socket->send($req);

        $data = $this->socket->receive();

        if ($data == "END\r\n") {
            return false;
        }

        $data = explode("\r\n", $data);

        return isset($data[1])?$data[1]:null;
    }

    public function delete($key = '') {
        if (!$this->socket) {
            throw new Exception("Socket not initialized at get.");
        }

        $req = "delete {$key}\r\n";

        $this->socket->send($req);

        $data = $this->socket->receive();

        if ($data == "DELETE\r\n") {
            return true;
        }

        return false;
    }

}





?>