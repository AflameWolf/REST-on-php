<?php
require_once 'ApiClasses/AbstractApi.php';
require_once 'Config/Database.php';
require_once 'Classes/User.php';

class UserApi extends Api
{
	/**
	 * Метод GET
	 * Вывод списка всех записей
	 * https://domain.by/api
	 * @return string
	 */
	public function indexAction()
	{
		$db = (new Database())->getConnect();
		$people = User::getAll($db);
		if($people){
			return $this->response($people, 200);
		}
		return $this->response('Data not found', 404);
	}

	/**
	 * Метод GET
	 * Просмотр отдельной записи (по id)
	 * https://domain.by/api/1
	 * @return string
	 */
	public function viewAction()
	{

		$id = array_shift($this->requestUri);

		if($id){
			$db = (new Database())->getConnect();
			$user = User::getById($db, $id);
			if($user){
				return $this->response($user, 200);
			}
		}
		return $this->response('Data not found', 404);
	}

	/**
	 * Метод POST
	 * Создание новой записи или авторизация
	 * https://domain.by/api + параметры запроса name, email, pass, auth
	 * @return string
	 */
	public function createOrAuthorizationAction()
	{
		$name = $this->requestParams['name'] ?? '';
		$email = $this->requestParams['email'] ?? '';
		$pass = $this->requestParams['pass'] ?? '';
		$auth = $this->requestParams['auth'] ?? '';
		if ($auth=='true'){
			$db = (new Database())->getConnect();
			$res = User::validateAuthorizationAction($db, $name, $email, $pass);
			$res = $res?'Authorization successful':'User not found check the entered data';
			return $this->response($res, 200);
		}
		if($name && $email){
			$db = (new Database())->getConnect();
			$user = new User($db, [
				'name' => $name,
				'email' => $email,
				'pass' => $pass
			]);
			if($user = $user->saveNew()){
				return $this->response('Data saved.', 200);
			}
		}
		return $this->response("Saving error", 500);
	}

	/**
	 * Метод PUT
	 * Обновление отдельной записи (по ее id)
	 * https://domain.by/api/1 + параметры запроса name, email, pass
	 * @return string
	 */
	public function updateAction()
	{
		$parse_url = parse_url($this->requestUri[0]);
		$userId = $parse_url['path'] ?? null;

		$db = (new Database())->getConnect();

		if(!$userId || !User::getById($db, $userId)){
			return $this->response("User with id=$userId not found", 404);
		}

		$name = $this->requestParams['name'] ?? '';
		$email = $this->requestParams['email'] ?? '';
		$pass = $this->requestParams['pass'] ?? '';

		if($name && $email){
			if($user = User::update($db, $userId, $name, $email,$pass)){
				return $this->response('Data updated.', 200);
			}
		}
		return $this->response("Update error", 400);
	}

	/**
	 * Метод DELETE
	 * Удаление отдельной записи (по ее id)
	 * https://domain.by/api/1
	 * @return string
	 */
	public function deleteAction()
	{
		$parse_url = parse_url($this->requestUri[0]);
		$userId = $parse_url['path'] ?? null;

		$db = (new Database())->getConnect();

		if(!$userId || !User::getById($db, $userId)){
			return $this->response("User with id=$userId not found", 404);
		}
		if(User::deleteById($db, $userId)){
			return $this->response('Data deleted.', 200);
		}
		return $this->response("Delete error", 500);
	}

}