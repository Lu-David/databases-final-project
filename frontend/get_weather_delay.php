<?php
require_once("putenv.php");
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_bar.php');

$dbhost = $_ENV["HOST"];
$dbuser = $_ENV["USER"];
$dbpass = $_ENV["PASSWORD"];
$dbname = $_ENV["DB"];

$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s<br>", mysqli_connect_error());
    exit();
}

$xlabels = ["clear sky", "fog", "storm"];
$delays = array();

if ($mysqli -> multi_query('CALL getWeatherDelay("%clear%"); CALL getWeatherDelay("%fog%"); CALL getWeatherDelay("%storm%");')) {
  do {
    // Store first result set
    if ($result = $mysqli -> store_result()) {
      while ($row = $result -> fetch_row()) {
        array_push($delays, $row[0]);
      }
     $result -> free_result();
    }
  } while ($mysqli -> next_result());
}

// New graph with a drop shadow
$graph = new Graph(300,200,'auto');
$graph->clearTheme();
$graph->SetShadow();

// Use a "text" X-scale
$graph->SetScale("textlin");

// Specify X-labels
$graph->xaxis->SetTickLabels($xlabels);
$graph->yaxis->title->Set('average delay (min)');

// Set title and subtitle
$graph->title->Set("Average Delay Minutes for each Weather");

// Use built in font
$graph->title->SetFont(FF_FONT1,FS_BOLD);

// Create the bar plot
$b1 = new BarPlot($delays);
$b1->SetWidth(0.4);

// The order the plots are added determines who's ontop
$graph->Add($b1);

// Finally output the  image
$img = $graph->Stroke();
?>
