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

  public function getGrandSummary(){
  
  
    $summary = array(
       "total_loan" => $this->getTotalLoan()
      //  "total_paid" => $this->getTotalPaid(),
      //  "remainbalance" =>  $this->getTotalLoan() -  $this->getTotalPaid()

    );
    echo json_encode($summary); 
    
  }

  private function getTotalLoan() {
        //build query
        $query = "SELECT SUM(amount) AS totalLoan FROM ".$this->loan_table." ";

        //prepare the query

        $stmnt = $this->conn->prepare($query);

         
         

         $stmnt->execute();

         $num = $stmnt->rowCount();
         $loan_array = array();
         if($num != 0){
            //     while($row = $stmnt->fetch(PDO::FETCH_ASSOC)){

            //       array_push($loan_array,$row['amount']);
            //     }
             
            //  $result = $this->validate_and_total($loan_array);
            //  if(!$result) {
            //   echo json_encode(array("message" => "The array contains non-numeric values."));
            //  }elseif (is_numeric($result)){
            //   echo json_encode(array("resut" => "$result"));
            //  }
            // }else{
            // echo json_encode(array("message" => "No Records Found"));

            $row = $stmnt->fetch(PDO::FETCH_ASSOC);

            return $row['totalLoan'];

         }else {
          return 0;
         }

    }
   
  private function getTotalPaid(){
         //build query
         $query = "SELECT SUM(amount) AS totalPaid FROM ".$this->$breakdown_table." ";

         //prepare the query
 
         $stmnt = $this->conn->prepare($query);
 
          
          
 
          $stmnt->execute();
 
          $num = $stmnt->rowCount();
          $loan_array = array();
          if($num != 0){
            //      while($row = $stmnt->fetch(PDO::FETCH_ASSOC)){
 
            //        array_push($loan_array,$row['totalPaid']);
            //      }
              
            //   $result = $this->validate_and_total($loan_array);
            //   if(!$result) {
            //    echo json_encode(array("message" => "The array contains non-numeric values."));
            //   }elseif (is_numeric($result)){
            //    echo json_encode(array("resut" => "$result"));
            //   }
            //  }else{
            //  echo json_encode(array("message" => "No Records Found"));
            $row = $stmnt->fetch(PDO::FETCH_ASSOC);
            return $row['totalPaid'];
          }else {
            return 0;
          }
  }  

  public function getLoanDetails(){
      //build query
      $query = "SELECT amount FROM ".$this->loan_table." ";

      //prepare the query

      $stmnt = $this->conn->prepare($query);

     

      $stmnt->execute();

      $num = $stmnt->rowCount();
      $loan_array = array();
      if($num != 0){
            while($row = $stmnt->fetch(PDO::FETCH_ASSOC)){

              $loan = array(
                "id" => $row['id'],
                "name" => $row['name'],
                "amount" =>$row['amount']

              );
              array_push($loan_array, $loan);
            }
            echo json_encode($loan_array);
        
      }
    else {
      echo json_encode("No loans details");
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

  public function getPayerPaymentDetails($payer_id){
          $query = "SELECT 
                      payer.id AS payer_id, 
                      payer.name AS payer_name, payer.alias AS payer_alias, 
                      payment_breakdown.amount AS paid_amount,
                      payment_breakdown.date AS paid_date
                  FROM 
                    payer 
                  INNER JOIN 
                    payment_breakdown 
                ON 
                    payer.id = payment_breakdown.payer_id 
                WHERE 
                    payer.id = $payer_id";
          //prepare the query

          $stmnt = $this->conn->prepare($query);

          $stmnt->execute();

          $num = $stmnt->rowCount();
          $payermember_array = array();
          if($num != 0){
                while($row = $stmnt->fetch(PDO::FETCH_ASSOC)){

                  $member = array(
                    "id" => $row['payer_id'],
                    "name" => $row['payer_name'],
                    "alias" =>$row['payer_alias'],
                    "paid_amount" => $row['paid_amount'],
                    "paid_date" => $row['paid_date']

                  );
                  array_push($payermember_array, $member);
                }
                echo json_encode($payermember_array);
            
          }
        else {
          echo json_encode("No payment details");
        }  


  }

  public function getMamaBreakdownSummary(){
    //build query

    $query = "SELECT amount FROM ".$this->loan_table." WHERE name='Geda'";

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
         
          $payermama = $this->get_payermama($result);
          echo json_encode($payermama);
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
		            	"shared_debts" => $pershareammount,
                  "total_paid" => $this->getTotalPayerPaid($row['id']),
                  "remaining_balance" => $pershareammount - $this->getTotalPayerPaid($row['id'])

		            );
            	array_push($payermember_array, $member);
               }
            
          return $payermember_array;
        }
      else {
        return false;
      }  

  }

  private function get_payermama($pershareammount){
    //build query
    $query = "SELECT * FROM ".$this->payer_table." WHERE name= 'Gloria Bulaclac'";




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
               "shared_debts" => $pershareammount,
               "total_paid" => $this->getTotalPayerPaid($row['id']),
               "remaining_balance" => $pershareammount - $this->getTotalPayerPaid($row['id'])

             );
           array_push($payermember_array, $member);
            }
         
       return $payermember_array;
     }
   else {
     return false;
   }  

}


  private function getTotalPayerPaid($id){
        $query = "SELECT SUM(payment_breakdown.amount) as total_paid
               FROM 
                payer
              LEFT JOIN 
                payment_breakdown
              ON 
              payer.id = payment_breakdown.payer_id
             WHERE 
               payer.id = $id;";

        $stmnt = $this->conn->prepare($query);

        $stmnt->execute();
        
        $row = $stmnt->fetch(PDO::FETCH_ASSOC);

        if($row['total_paid'] == null) {
          return 0;
        };
        return $row['total_paid'];

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