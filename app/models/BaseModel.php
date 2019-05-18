<?php

class BaseModel {
    public $db;
	protected $f3;

	public function __construct($f3) {
		$this->f3 = $f3;
		$this->db = new DB\SQL(
			$f3->get('db_str'),
			$f3->get('db_username'),
			$f3->get('db_password')
		);
	}
}
