<?php
class User
{
	private $db;
	private $data;

	public function __construct($db, $data = [])
	{
		$this->db = $db;
		$this->data = $data;
	}

	public static function getAll($db)
	{
		$stmt = $db->query("SELECT * FROM users");
		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public static function getById($db, $id)
	{
		$stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
		$stmt->execute([$id]);
		return $stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function saveNew()
	{
		$stmt = $this->db->prepare("INSERT INTO users (name, email, pass) VALUES (?, ?,?)");
		return $stmt->execute([$this->data['name'], $this->data['email'], $this->data['pass']]);
	}

	public static function validateAuthorizationAction($db, $name, $email,$pass)
	{
		$stmt = $db->prepare("SELECT * FROM users WHERE name = ?  AND email = ? AND pass= ?");
		$stmt->execute([$name, $email, $pass]);
		return $stmt->fetch(PDO::FETCH_ASSOC);

	}

	public static function update($db, $id, $name, $email,$pass)
	{
		$stmt = $db->prepare("UPDATE users SET name = ?, email = ?, pass = ? WHERE id = ?");
		return $stmt->execute([$name, $email,$pass, $id]);
	}

	public static function deleteById($db, $id)
	{
		$stmt = $db->prepare("DELETE FROM users WHERE id = ?");
		return $stmt->execute([$id]);
	}
}
?>