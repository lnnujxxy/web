<?php
/**
 * 基础model
 * @author lnnujxxy@gmail.com
 * @version 1.0
 */
class Dao_BaseModel {
	public $db;
	public $table;

	public function __construct() {
	}

	public function setTable($table) {
		$this->table = $table;
		return $this;
	}

	public function getTable() {
		return $this->table;
	}

	public function add($data) {
		$sql = "INSERT INTO " . $this->getTable() . " SET ";
		$values = [];
		foreach ($data as $key => $value) {
			$sql .= " `$key` = ?,";
			array_push($values, $value);
		}
		$sql = substr($sql, 0, strrpos($sql, ','));
		$db = \Mysql::getDB($sql);
		$sth = $db->prepare($sql);
		$sth->execute($values);

		return $db->errorCode() == '00000';
	}

	public function insertId($data) {
		$sql = "INSERT INTO " . $this->getTable() . " SET ";
		$values = [];
		foreach ($data as $key => $value) {
			$sql .= " `$key` = ?,";
			array_push($values, $value);
		}
		$sql = substr($sql, 0, strrpos($sql, ','));
		$db = \Mysql::getDB($sql);
		$sth = $db->prepare($sql);
		$sth->execute($values);
		return $db->lastInsertId();
	}

	public function replace($data) {
		$sql = "REPLACE INTO " . $this->getTable() . " SET ";
		$values = [];
		foreach ($data as $key => $value) {
			$sql .= " `$key` = ?,";
			array_push($values, $value);
		}
		$sql = substr($sql, 0, strrpos($sql, ','));
		$db = \Mysql::getDB($sql);
		$sth = $db->prepare($sql);
		$sth->execute($values);
		return $db->errorCode() == '00000';
	}

	/**
	 * 更新字段信息
	 * @param  Int $uid   用户uid
	 * @param  String $key  数据表字段名
	 * @param  String $value 字段数据
	 * @return Boolean
	 */
	public function updateField($where, $key, $value) {
		$sql = "UPDATE " . $this->getTable() . " SET `$key` = ? WHERE $where";
		// Utiltiy::debug('sql = ' . $sql);
		$db = \Mysql::getDB($sql);
		$sth = $db->prepare($sql);
		$sth->execute(array($value));
		return $db->errorCode() == '00000';
	}

	public function updateFields($where, $data) {
		$sql = "UPDATE " . $this->getTable() . " SET ";
		$values = [];
		foreach ($data as $key => $value) {
			$sql .= " `$key` = ?, ";
			array_push($values, $value);
		}
		$sql = substr($sql, 0, strrpos($sql, ','));
		$sql .= " WHERE $where ";

		$db = \Mysql::getDB($sql);
		$sth = $db->prepare($sql);
		$sth->execute($values);
		return $db->errorCode() == '00000';
	}

	public function selectFields($where, $fields) {
		$sql = "SELECT ";
		foreach ($fields as $field) {
			$sql .= " $field,";
		}
		$sql = substr($sql, 0, strrpos($sql, ','));
		$sql .= " FROM " . $this->getTable() . " WHERE $where ";
		$db = \Mysql::getDB($sql);
		$sth = $db->prepare($sql);
		$sth->execute();
		return $sth->fetch(PDO::FETCH_ASSOC);
	}

	public function debug($sql, $params = array()) {
		if ($_SERVER['env'] == 'product') {
			return;
		}

		$sql = preg_replace_callback(
			'/[?]/',
			function ($k) use ($params) {
				static $i = 0;
				return sprintf("'%s'", $params[$i++]);
			},
			$sql
		);

		if ($_GET['echo']) {
			echo $sql . "\n";
		}

		\Utility::debug('sql = ' . $sql);
	}

	public function __clone() {

	}

}