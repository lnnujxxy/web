<?php
/**
 * 唯一 ID 生成器
 */
class IDWork {
	private static $self = NULL;

	public static function getInstance() {
		if (self::$self == NULL) {
			self::$self = new self();
		}
		return self::$self;
	}

	private function __construct() {

	}

	public function nextId() {
		$db = (new Mysql())->getMSConfig(true)->getPDO();
		$sql = "insert into hh_id_work (id) values('')";
		$db->exec($sql);
		$id = $db->lastInsertId();
		return $id;
	}

}