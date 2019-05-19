<?php
namespace Controllers;

/*
*a simple API controller to serve our AJAX request
*/
class Api extends Base {
    public function lastHourTraffic($f3) {
        $last_hour = $f3->get('GET.lasthour');
        $traffic = new \TrafficModel($f3);
        $r = $traffic->getLastXHour($last_hour);
        echo json_encode($r);
    }
}
