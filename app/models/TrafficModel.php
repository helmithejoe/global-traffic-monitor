<?php

use Lib\WebSocketClient;

class TrafficModel extends BaseModel {

	public function __construct($f3) {
		parent::__construct($f3);
	}
    
    /*
    * insert log to database
    */
    private function _insertLog($data) {
        $e = new DB\SQL\Mapper($this->db, 'traffic');
		$e->ip = $data['ip'];
		$e->longitude = $data['longitude'];
		$e->latitude = $data['latitude'];
        $e->user_agent = $data['user_agent'];
        $e->request_uri = $data['request_uri'];
        $e->request_method = $data['request_method'];
        $e->request_time = $data['request_time'];
		$e->timestamp_created = time();
        
		if($e->save()) return $e->id;
    }
    
    /*
    * connect to websocket
    */
    private function _connect() {
        $client = new WebsocketClient;
        $client->connect(
            $this->f3->get('websocket_host'),
            $this->f3->get('websocket_port'),
            $this->f3->get('websocket_application')
        );
        
        while($client->checkConnection() === false) {
            $this->_connect();
        }
        
        return $client;
    }
    
    /*
    * send log to websocket
    */
    private function _sendLog($data) {
        $client = $this->_connect();
        $payload = json_encode(array(
            'action' => $this->f3->get('websocket_action'),
            'data' => $data
        ));
        $client->sendData($payload);
        $client->disconnect();
    }
    
    /*
    * entry point to log traffic
    */
    public function log() {
        $ip = $this->f3->get('SERVER.REMOTE_ADDR');
        $user_agent = $this->f3->get('SERVER.HTTP_USER_AGENT');
        $uri = $this->f3->get('SERVER.REQUEST_URI');
        $method = $this->f3->get('SERVER.REQUEST_METHOD');
        $time = $this->f3->get('SERVER.REQUEST_TIME');
        
        //get location information from IP
        $geo = \Web\Geo::instance();
        $loc = $geo->location($ip);
        
        $data['latitude'] = !empty($loc['latitude'])?$loc['latitude']:'';
        $data['longitude'] = !empty($loc['longitude'])?$loc['longitude']:'';
        $data['ip'] = $ip;
        $data['user_agent'] = $user_agent;
        $data['request_uri'] = $uri;
        $data['request_method'] = $method;
        $data['request_time'] = $time;
        
        //send log to websocket and database
        $this->_sendLog(json_encode($data));
        $this->_insertLog($data);
    }
    
    /*
    * query to get last X hour traffic
    */
    public function getLastXHour($hour) {
        $sql = 'select * from traffic where request_time > '.(time() - ($hour * 3600));
        $r = $this->db->exec($sql);
        return $r;
    }
}
