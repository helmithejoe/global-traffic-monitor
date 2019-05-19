<?php

namespace WebSocket\Application;

/**
 * provides live traffic messages.
 */
class TrafficApplication extends Application {
    private $_clients = array();
    //a collection of traffic viewer connection
    private $_admin_clients = array();
    //valid token as a simple auth to identify traffic admin
    private $_token = '49e50181c489af9a17e2bcc53dc906e0';

	public function onConnect($client) {
		$id = $client->getClientId();
        $this->_clients[$id] = $client;
    }

    public function onDisconnect($client) {
        $id = $client->getClientId();
		unset($this->_clients[$id]);
    }
    
    public function onData($data, $client) {		
        $decodedData = $this->_decodeData($data);
		if($decodedData === false) {
			//invalid request
		}
		
        //call a function based on client request
		$actionName = '_action' . ucfirst($decodedData['action']);
		if(method_exists($this, $actionName)) {
			call_user_func(array($this, $actionName), $decodedData['data'], $client);
		}
    }
	
    //usually this echo request would be traffic information.
    //when an echo request is called
    //then broadcast the information to all connected admin clients (traffic viewers)
	private function _actionEcho($text, $client) {		
		foreach($this->_admin_clients as $sendto) {
            $sendto->send($text);
        }
	}
    
    //a special action to identify an admin
    //if the connection has valid token, then collect as admin client
    private function _actionLogin($text, $client) {
		if($text == $this->_token) {
            $id = $client->getClientId();
            $this->_admin_clients[$id] = $client;
        } else return false;
	}
    
}