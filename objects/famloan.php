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
             
             $result = $this->validate_and_total($loan_array);
             if(!$result) {
              echo json_encode(array("message" => "The array contains non-numeric values."));
             }elseif (is_numeric($result)){
              echo json_encode(array("resut" => "$result"));
             }
            }else{
            echo json_encode(array("message" => "No Records Found"));
         }

    }
  public function getBreakdownSummary(){
       //build query

       $query = "SELECT amount FROM ".$this->loan_table." WHERE name NOT IN ('Geda','Carloan')";

       //prepare the query

       $stmnt = $this->conn->prepare($query);

        
        

        $stmnt->execute();

        $num = $stmnt->rowCount();
        $loan_array = array();
        if($num != 0){
               while($row = $stmnt->fetch(PDO::FETCH_ASSOC)){

                 array_push($loan_array,$row['amount']);
               }
            
            $result = $this->validate_and_total($loan_array);
            if(!$result) {
             echo json_encode(array("message" => "The array contains non-numeric values."));
            }elseif (is_numeric($result)){
             //divide by 8 family members
             $pershareammount = $result / 8;
             $payermembers = $this->get_payermembers($pershareammount);
             echo json_encode($payermembers);
            }
           }else{
           echo json_encode(array("message" => "No Records Found"));
        }

  }  

  private function get_payermembers($pershareammount){
       //build query
       $query = "SELECT * FROM ".$this->payer_table." WHERE name NOT IN ('Gloria Bulaclac')";

       //prepare the query

       $stmnt = $this->conn->prepare($query);

        $stmnt->execute();

        $num = $stmnt->rowCount();
        $payermember_array = array();
        if($num != 0){
               while($row = $stmnt->fetch(PDO::FETCH_ASSOC)){

                $member = array(
		            	"id" => $row['id'],
		            	"name" => $row['name'],
		            	"alias" =>$row['alias'],
		            	"pershare_amount" => $pershareammount

		            );
            	array_push($payermember_array, $member);
               }
            
          return $payermember_array;
        }
      else {
        return false;
      }  

  }

  private function validate_and_total($array){
    foreach($array as $val){

      // check if the value is not a numeric value 
      if(!is_numeric($val)) {
        return false ;
      }
    }
    return array_sum($array);
  }  
}  