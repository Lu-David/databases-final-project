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

$xlabels = range(-5, 10);
$delays = array();

for ($i = 0; $i < count($xlabels); $i++) {
    if ($mysqli -> multi_query('CALL getAvgCancellations('.$xlabels[$i].')')) {
        // Store first result set
        do {
            if ($result = $mysqli -> store_result()) {
            while ($row = $result -> fetch_row()) {
                array_push($delays, $row[0]);
            }
            $result -> free_result();
            }
        } while ($mysqli -> next_result());
    }   
}

// New graph with a drop shadow
$graph = new Graph(300,200,'auto');
$graph->clearTheme();
$graph->SetShadow();

// Use a "text" X-scale
$graph->SetScale("textlin");

// Specify X-labels
$graph->xaxis->SetTickLabels($xlabels);

// Set title and subtitle
$graph->title->Set("Average Num cancellations for all disaster dates in db");

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
