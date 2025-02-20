<?
require_once 'ApiClasses/UserApi.php';



try {

$api = new UserApi();

echo $api->run();

} catch (Exception $e) {

echo json_encode(Array('error' => $e->getMessage()));

}