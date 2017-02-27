<?php
/**
 *  Copyright(c) 2017 rryqszq4 ngxphp@gmail.com
 */

namespace ngxphp\memcached;

use ngx_socket_tcp;

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

        return isset($data[1]) ? $data[1] : null;
    }

    public function delete($key = '') {
        if (!$this->socket) {
            throw new Exception("Socket not initialized at delete.");
        }

        $req = "delete {$key}\r\n";

        $this->socket->send($req);

        $data = $this->socket->receive();

        if ($data == "DELETE\r\n") {
            return true;
        }

        return false;
    }

    public function incr($key = '', $value = 0) {
        if (!$this->socket) {
            throw new Exception("Socket not initialized at incr.");
        }

        $req = "incr {$key} {$value}\r\n";

        $this->socket->send($req);

        $data = $this->socket->receive();

        if ($data == "NOT_FOUND\r\n") {
            return 0;
        }

        $data = explode("\r\n", $data);

        return isset($data[0]) ? intval($data[0]) : 0;
    }

    public function decr($key = '', $value = 0) {
        if (!$this->socket) {
            throw new Exception("Socket not initialized at decr.");
        }

        $req = "decr {$key} {$value}\r\n";

        $this->socket->send($req);

        $data = $this->socket->receive();

        if ($data == "NOT_FOUND\r\n") {
            return 0;
        }

        $data = explode("\r\n", $data);

        return isset($data[0]) ? intval($data[0]) : 0;
    }

    public function flush_all($time = 0) {
        if (!$this->socket) {
            throw new Exception("Socket not initialized at flush_all.");
        }

        if ($time) {
            $req = "flush_all {$time}\r\n";
        }else {
            $req = "flush_all\r\n";
        }

        $this->socket->send($req);

        $data = $this->socket->receive();

        if ($data != "OK\r\n") {
            return 0;
        }

        return 1;
    }

    public function version() {
        if (!$this->socket) {
            throw new Exception("Socket not initialized at version.");
        }

        $req = "version\r\n";

        $this->socket->send($req);

        $data = $this->socket->receive();

        if (preg_match("/^VERSION (.+)\r\n$/", $data, $match) > 0) {
            return isset($match[1]) ? $match[1] : null;
        }

        return null;
    }

    public function stats($arg = '') {
        if (!$this->socket) {
            throw new Exception("Socket not initialized at stats.");
        }

        if ($arg) {
            $req = "stats {$arg}\r\n";
        }else {
            $req = "stats\r\n";
        }

        $this->socket->send($req);

        $data = $this->socket->receive();

        if ($data == "ERROR\r\n") {
            return null;
        }

        $data = explode("\r\n",$data);

        $new_data = array();
        foreach ($data as $key => $value) {
            $value = explode(" ", $value);
            if (isset($value[1]) && isset($value[2])) {
                $new_data[$value[1]] = $value[2];
            }
        }

        return $new_data;
    }

}

?>