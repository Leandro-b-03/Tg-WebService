<?php 
	require '../vendor/autoload.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	
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

	$app->get('/getTotal', function () use ($app){
		$con = getConn();
		$sql = 'SELECT  o.amount, b.id as id_bracelet, o.id as id_order FROM sb_customers c LEFT JOIN sb_customer_bracelet cb ON ( c.id = cb.id_customer) LEFT JOIN sb_bracelets b ON (b.id = cb.id_bracelet) LEFT JOIN sb_orders o ON (o.id_customer = c.id) WHERE c.id = cb.id_customer AND (b.tag = \''.$app->request->get('param').'\' OR c.cpf = \''.$app->request->get('param').'\')';
		$result = $con->query($sql);
		echo json_encode($result->fetch(PDO::FETCH_ASSOC));
	});

	$app->get('/getOrderDescription', function () use ($app){
		$con = getConn();
		$sql = 'SELECT * FROM sb_orders o LEFT JOIN sb_order_bracelet ob ON (o.id = ob.id_bracelet) LEFT JOIN sb_products  p ON (ob.id_product = p.id ) WHERE o.id = \'' . $app->request->get('id') . '\' AND o.status =  1';
		$result = $con->query($sql);
		echo json_encode($result->fetch(PDO::FETCH_ASSOC));
	});

	function getConn()
	{
		return new PDO('mysql:host=localhost;dbname=smart_bracelet',
		'root',
		'3540f8e4**',
		array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8")
		);
	}
	$app->run();
 ?>