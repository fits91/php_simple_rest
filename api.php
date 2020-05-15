<?php

header('content-type: application/json');

      $request=$_SERVER['REQUEST_METHOD'];
      $source = $_GET['source'];

   switch ( $request) {
   	case 'GET':
        $date = validateDate($_GET['date'])? $_GET['date'] : date('Y-m-d H:i:s');
        //file_put_contents("errors.log", $date, FILE_APPEND);
        getData($date);
   	break;
   	case 'POST':
        $data=file_get_contents('php://input');
        saveData($data, $source);
   	break;
   	default:
   		echo '{"name": "data not found"}';
   		break;
   }

function getData($date){
  include "db.php";

  $sql = "SELECT * FROM user_data where date = '$date'";
  $result = mysqli_query($conn, $sql);

  if (mysqli_num_rows($result) > 0) {
       $rows=array();
       while ($r = mysqli_fetch_assoc($result)) {

          $rows["result"][] = $r;
       }
      
       echo json_encode($rows);
  }  else{
      echo '{"result": "no data found"}';
    }
}

function saveData($data, $source){
   include "db.php";

   $sqlCount= "SELECT COUNT(*) FROM source WHERE id=$source";
   $result = mysqli_query($conn, $sqlCount);

   if (mysqli_num_rows($result) > 0) {
        $date = date('Y-m-d H:i:s');
        $strData = json_encode($data);
        $sql= "INSERT INTO user_data(source_id, date, json_data) VALUES ($source , '$date', $strData)";
        if(mysqli_query($conn, $sql)) {
            echo '{"result": "data inserted"}';
        }
   } else {
        echo '{"result": "source not found"}';
   }

}

function validateDate($date, $format = 'Y-m-d') {
    $d = DateTime::createFromFormat($format, $date);
    return $d && $d->format($format) === $date;
}
?>
