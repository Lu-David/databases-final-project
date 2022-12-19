<?php
require_once("putenv.php");

$dbhost = $_ENV["HOST"];
$dbuser = $_ENV["USER"];
$dbpass = $_ENV["PASSWORD"];
$dbname = $_ENV["DB"];

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s<br>", mysqli_connect_error());
    exit();
} else {
    echo "Connection successful <br>";
}

$target_dir = "uploads/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

$header_fields = array(
    "FL_DATE" => "date",
    "OP_UNIQUE_CARRIER" => "carrier_code", 
    "TAIL_NUM" => "tail_num",
    "OP_CARRIER_FL_NUM" => "flight_num",
    "ORIGIN" => "origin",    
    "DEST" => "destination",
    "DEP_TIME" => "departure_time",
    "ARR_TIME" => "arrival_time",
    "ACTUAL_ELAPSED_TIME" => "duration_of_flight",
    "DISTANCE" => "distance"    
);
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
  echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    $filename = $_FILES["fileToUpload"]["tmp_name"];
    $row = 1;

    $header_idx = array();

    if (($handle = fopen($filename, "r")) !== FALSE) {
        
        $sql = "INSERT INTO flights (".implode(",", array_values($header_fields)).") VALUES ";

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            
            $num = count($data);

            if ($row == 1) {
                for ($i=0; $i < $num; $i++) {
                    if (array_key_exists($data[$i], $header_fields)) {                        
                        array_push($header_idx, $i);
                    }
                }
                echo implode(",", $header_idx) . "<br>";
            } else {
                $values = array_intersect_key($data, array_flip($header_idx));            
                $sql = $sql. "('".implode("','", $values)."')";
                if ($row % 1000 == 0) {
                    if ($mysqli->query($sql) === TRUE) {
                        echo "New record created successfully";
                    } else {
                        echo "Error: " . $sql . "<br>" . $mysqli->error;
                    }
                    $sql = "INSERT INTO flights (".implode(",", array_values($header_fields)).") VALUES ";
                } else {
                    $sql = $sql . ", ";
                }
            }
            
            $row++;
        }
    }
}
//   if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
//     echo "The file ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " has been uploaded.";
//   } else {
//     echo "Sorry, there was an error uploading your file.";
//   }
?>