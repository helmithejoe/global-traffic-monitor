<?php

use Lib\WebSocketClient;

class TrafficModel extends BaseModel {

	public function __construct($f3) {
		parent::__construct($f3);
	}
    
    private function _insertLog($data) {
        $e = new DB\SQL\Mapper($this->db, 'traffic');
		$e->ip = $data['ip'];
		$e->longitude = $data['longitude'];
		$e->latitude = $data['latitude'];
		$e->timestamp_created = time();
		$e->timestamp_online_limit = time() + $this->f3->get('online_interval');
        
		if($e->save()) return $e->id;
    }
    
    private function _sendLog($data) {
        $client = new WebSocketClient(
            $this->f3->get('websocket_host'),
            $this->f3->get('websocket_port'),
            'U3RyZWFtKDIsID8p'
        );
        $client->sendData($data);
    }
    
    public function log() {
        $ip = $this->f3->get('SERVER.REMOTE_ADDR');
        $ip = '36.69.124.89';
        
        $geo = \Web\Geo::instance();
        $loc = $geo->location($ip);
        
        $data['latitude'] = $loc['latitude'];
        $data['longitude'] = $loc['longitude'];
        $data['ip'] = $ip;
        
        $this->_sendLog(json_encode($data));
        
        $this->_insertLog($data);
    }
}
