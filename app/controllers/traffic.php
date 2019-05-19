<?php
namespace Controllers;

class Traffic extends Base {
    
	public function display($f3) {
        echo \Template::instance()->render('app/views/traffic/display.php');
    }
    
    /**
    * an example of URL to be accessed and monitored.
    **/
    public function test($f3) {
        $traffic = new \TrafficModel($f3);
        $traffic->log();
    }
}
