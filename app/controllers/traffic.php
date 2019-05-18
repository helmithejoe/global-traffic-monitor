<?php
namespace Controllers;

class Traffic extends Base {

	public function display($f3) {
        echo \Template::instance()->render('app/views/traffic/display.php');
    }
}
