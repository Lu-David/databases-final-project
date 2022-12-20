<?php
require_once("putenv.php");
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_scatter.php');

$dbhost = $_ENV["HOST"];
$dbuser = $_ENV["USER"];
$dbpass = $_ENV["PASSWORD"];
$dbname = $_ENV["DB"];

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s<br>", mysqli_connect_error());
    exit();
}

$delays = array();
$wind_speeds = array();
if ($mysqli->multi_query("CALL getWindSpeedDelay(1000);")) {
    if ($result = $mysqli->store_result()) {
        $row = $result->fetch_row();
        if (count($row) == 1) {
            pass;
        } else {
            do {
                array_push($delays, intval($row[0]));
                array_push($wind_speeds, intval($row[1]));
            } while ($row = $result->fetch_row());
        }
        $result->close();
    }
} else {
    printf("<br>Error: %s\n", $mysqli->error);
}

$graph = new Graph(300,200);
$graph->clearTheme();
$graph->SetScale("linlin");

$graph->img->SetMargin(40,40,40,40);
$graph->SetShadow();

$graph->title->Set("Windspeed vs delay minutes");
$graph->yaxis->title->Set('delay (min)');
$graph->xaxis->title->Set('windspeed (mph)');

$sp1 = new ScatterPlot($delays,$wind_speeds);

$graph->Add($sp1);
$graph->Stroke();

?>
