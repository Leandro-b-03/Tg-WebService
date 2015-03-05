<?php 
	require '../vendor/autoload.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	
	$app->response()->header('Access-Control-Allow-Origin', '*');
	
	$app->post('/tag', function () use ($app){
		$con = getConn();
		$sql = "SELECT COUNT(*) AS total FROM pulseira_id WHERE idTag='" . $app->request->post('tag') . "'";
		$query = $con->query($sql);
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if ($result['total'] == 0){
			$sql = "INSERT INTO pulseira_id SET idTag='" . $app->request->post('tag') . "', idFuncionario='" . $app->request->post('id_funcionario') . "'";
			$con->query($sql);
		}
	});

	$app->get('/', function () {
		echo json_encode(array('retorno'=> true));
	});

	$app->get('/login',function () use ($app) {
		$con = getConn();
		$sql = "SELECT COUNT(*) as total,idFuncionario FROM login WHERE login='". $app->request->get('login') ."' and senha='" . $app->request->get('senha') . "'";
		$result = $con->query($sql);
		echo json_encode($result->fetch(PDO::FETCH_ASSOC));
	});

	function getConn()
	{
		return new PDO('mysql:host=localhost;dbname=futuroau_smartbraselete',
		'root',
		'',
		array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
		);
	}
	$app->run();
 ?>