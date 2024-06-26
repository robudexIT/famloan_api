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


$stmnt = $famloan->getGrandSummary();
