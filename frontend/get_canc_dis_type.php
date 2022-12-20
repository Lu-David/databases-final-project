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

$xlabels = array();
$cnts = array();


if ($mysqli -> multi_query('CALL getCancDisType')) {
    do {
        if ($result = $mysqli -> store_result()) {
        while ($row = $result -> fetch_row()) {
            array_push($xlabels, $row[0]);
            array_push($cnts, $row[1]);
        }
        $result -> free_result();
        }
    } while ($mysqli -> next_result());
}   


// New graph with a drop shadow
$graph = new Graph(500,500,'auto');
$graph->clearTheme();
$graph->SetShadow();

// Use a "text" X-scale
$graph->SetScale("textlin");

// Specify X-labels
$graph->xaxis->SetTickLabels($xlabels);
$graph->xaxis->SetLabelAngle(90);



// Set title and subtitle
// $graph->title->Set("Average Num cancellations for all disaster dates in db");

// Use built in font
$graph->title->SetFont(FF_FONT1,FS_BOLD);

// Create the bar plot
$b1 = new BarPlot($cnts);
$b1->SetWidth(0.4);

// The order the plots are added determines who's ontop
$graph->Add($b1);
$graph->SetMargin(50,50,50,160);
// Finally output the  image
$img = $graph->Stroke();
?>
