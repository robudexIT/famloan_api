<?php

class Famloan {

	//FamLoan class properties
	private $loan_table = "loan";
	private $payer_table = "payer";
    private $breakdown_table = "payment_breakdonw";
 



  // end

  private $json_addr = "/var/www/html/sbtph_csd_dev/json/";
  
   //create database connection  when this class instantiated
  public function __construct($db){
    	$this->conn = $db;
    }

  
  public function getTotalLoan() {
        //build query
        $query = "SELECT amount FROM ".$this->loan_table." ";

        //prepare the query

        $stmnt = $this->conn->prepare($query);

         
         

         $stmnt->execute();

         $num = $stmnt->rowCount();
         $loan_array = array();
         if($num != 0){
                while($row = $stmnt->fetch(PDO::FETCH_ASSOC)){

                  array_push($loan_array,$row['amount']);
                }
             
             echo json_encode($loan_array);
            }else{
            echo json_encode(array("message" => "No Records Found"));
         }

    }

}  