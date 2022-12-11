<?php // content="text/plain; charset=utf-8"
require_once ('jpgraph/src/jpgraph.php');
require_once ('jpgraph/src/jpgraph_line.php');
require_once("putenv.php");

$dbhost = $_ENV["HOST"];
$dbuser = $_ENV["USER"];
$dbpass = $_ENV["PASSWORD"];
$dbname = $_ENV["DB"];
$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s<br>", mysqli_connect_error());
    exit();
}
$date = $_GET["date"];
$location = $_GET["city"];

$times = array();
$humidity = array();
$pressure = array();
$temperature = array();
$wind_direction = array();
$wind_speed = array();
if ($mysqli->multi_query("CALL getWeather('".$date."', '".$location."');")) {
    if ($result = $mysqli->store_result()) {
        $row = $result->fetch_row();
        if (count($row) == 1) {
            pass;
        } else {
            do {
                array_push($times, $row[0]);
                array_push($humidity, $row[1]);
                array_push($pressure, $row[2]);
                array_push($temperature, $row[3]);
                array_push($wind_direction, $row[4]);
                array_push($wind_speed, $row[5]);
            } while ($row = $result->fetch_row());
        }
        $result->close();
    }
} else {
    printf("<br>Error: %s\n", $mysqli->error);
}


// Size of the overall graph
$width=400;
$height=400;
 
// Create the graph and set a scale.
// These two calls are always required
$graph = new Graph($width,$height);
$graph->SetScale('intlin');
 
// Create the linear plot
$lineplot=new LinePlot($temperature);
 
// Add the plot to the graph
$graph->Add($lineplot);
 
// Display the graph
$graph->Stroke();

?>