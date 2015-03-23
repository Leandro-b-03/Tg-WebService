<?php 
	require '../vendor/autoload.php';
	\Slim\Slim::registerAutoloader();
	$app = new \Slim\Slim();
	header("Access-Control-Allow-Origin : * ");
	$app->response()->header('Content-Type', 'application/json;charset=utf-8');
	$app->response()->header("Access-Control-Allow-Origin : * ");
	date_default_timezone_set('America/Sao_Paulo');
	$app->post('/tag', function () use ($app){
		$con = getConn();
		$sql = "SELECT COUNT(*) AS total FROM sb_user_bracelet WHERE tag='" . $app->request->post('tag') . "' AND status = '-1'";
		$query = $con->query($sql);
		$result = $query->fetch(PDO::FETCH_ASSOC);
		if ($result['total'] == 0){
			$sql = "INSERT INTO sb_user_bracelet SET tag='" . $app->request->post('tag') . "', id_user='" . $app->request->post('id_funcionario') . "',  status='-1', created_at='". date("Y-m-d H:i:s") ."'";
			$con->query($sql);
			echo 'true';
		} else {
			echo 'false';
		}
	});

	$app->get('/', function () {
		echo json_encode(array('retorno'=> true));
	});

	$app->get('/login',function () use ($app) {
        $cURL = curl_init('smartbracelet.someideias.com/app/login?username=' . $app->request->get('login') . '&password=' . $app->request->get('senha'));
        
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);

        echo curl_exec($cURL);

        curl_close($cURL);
    });

	$app->get('/gettotal', function () use ($app){
		$con = getConn();
		$sql = 'SELECT  o.amount, b.id as id_bracelet, o.id as id_order, c.name FROM sb_customers c LEFT JOIN sb_customer_bracelet cb ON ( c.id = cb.id_customer) LEFT JOIN sb_bracelets b ON (b.id = cb.id_bracelet) LEFT JOIN sb_orders o ON (o.id_customer = c.id) WHERE c.id = cb.id_customer AND o.status = \'1\' AND (b.tag = \''.$app->request->get('param').'\' OR c.cpf = \''.$app->request->get('param').'\')';
		$result = $con->query($sql);
		$rows = $result->fetch(PDO::FETCH_ASSOC);
		if (count($rows) > 1){
			$rows['amount'] = 'R$' . number_format($rows['amount'], 2, ',', '.');
			echo json_encode($rows);
		} else {
			echo json_encode(array('status'=>'false'));
		}
		
	});

	$app->get('/getOrderDescription', function () use ($app){
		$con = getConn();
		$sql = "SELECT ob.quantity , ob.price , name , (SELECT amount FROM sb_orders WHERE id = '" . $app->request->get('id') . "') AS amount FROM  sb_order_bracelet ob LEFT JOIN sb_products  p ON (ob.id_product = p.id ) WHERE ob.id_order = '" . $app->request->get('id') . "'";
		$result = $con->query($sql);
		echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));
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