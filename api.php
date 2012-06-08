<?php

require_once("lib/flyCrawl.php");
require_once("lib/functions.php");

//setup
$server = "http://stage.masterofcode.com:10101/";

//type
$t = (!empty($_REQUEST['t'])) ? strtolower($_REQUEST['t'])  :  'get';
$t = trim($t);

//method 
$m = $_REQUEST['m'];

if($_SERVER['REQUEST_METHOD']=="GET"){
	$aParams = $_GET;
}
if($_SERVER['REQUEST_METHOD']=="POST"){
	$aParams = $_POST;
}

unset($aParams['m']);
unset($aParams['t']);

/*
$crawl = new flyCrawler();
echo "Server returned: ";
echo $crawl->post( $server . "users.json" , array(
												"user[email]" => "jesus@heaven.com",
												"user[password]" => "123456"
											) );
 */
//Server returned: {"auth_token":"qoq1sVjAQzKKwpaxPbJs"}


header("Content-type: application/json"); 

$crawl = new flyCrawler();
switch($t){
	case "get":
		// var_dump($aParams);
		// die();
		$res = $crawl->get( $server . $m , $aParams );
		echo fix_bogdan($res);
		break;
	case "post":
		// var_dump($server);
		// var_dump($m);
		// var_dump($aParams);
		// die();
		$res = $crawl->post( $server.$m , $aParams );
		echo fix_bogdan($res);
		break;
	case "put":
		echo $crawl->get( $server . $m , $aParams );
		break;
	case "delete":
		echo $crawl->get( $server . $m , $aParams );
		break;
	default:
		echo json_encode( array("error" => "Ooops") );
}
