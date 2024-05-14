<?php
//required headers
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
date_default_timezone_set('Asia/Tokyo');

// // database connection will be here...

// //include database and object files
include_once '../config/database.php';
include_once '../objects/famloan.php';

$database = new Database();
$db = $database->getConnection();

$famloan = new FamLoan($db);


if(isset($_GET['payer_id'])){

	$payer_id = $_GET['payer_id'];
    $stmnt = $famloan->getPayerPaymentDetails($payer_id);
	
}else{
    echo json_encode("No Payer_id Selected");
}
