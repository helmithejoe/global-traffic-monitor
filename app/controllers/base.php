<?php
namespace Controllers;

class Base {
    public function __construct($f3) {
        $traffic = new \TrafficModel($f3);
        $traffic->log();
    }
}
