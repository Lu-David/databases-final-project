<?php // content="text/plain; charset=utf-8"
require_once ('jpgraph/src/jpgraph.php');
require_once ('jpgraph/src/jpgraph_line.php');
require_once("putenv.php");

$dbhost = $_ENV["HOST"];
$dbuser = $_ENV["USER"];
$dbpass = $_ENV["PASSWORD"];
$dbname = $_ENV["DB"];
$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

// function to allow for more queries with procedures using mysqli
function clearStoredResults(){
    global $mysqli;
    do {
         if ($res = $mysqli->store_result()) {
           $res->free();
         }
    } while ($mysqli->more_results() && $mysqli->next_result());        
}

if (mysqli_connect_errno()) {
    printf("Connect failed: %s<br>", mysqli_connect_error());
    exit();
}

$date = $_GET["date"];
$location = $_GET["city"];

// Get weather data
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

clearStoredResults();

// Get flight data for airports in the given location at the specified date
$flight_out = array();
$flight_in = array();
$airports = array();
if ($mysqli->multi_query("CALL outgoingFlights('".$date."', '".$location."');")) {
    if ($result = $mysqli->store_result()) {
        $row = $result->fetch_row();
        if (count($row) == 1) {
            pass;
        } else {
            do {
                array_push($airports, $row[0]);
                array_push($flight_out, $row[1]);
            } while ($row = $result->fetch_row());
        }
        $result->close();
    }
} else {
    printf("<br>Error: %s\n", $mysqli->error);
}

// Size of the overall graphs
$width=400;
$height=400;

$graphTemp = new Graph($width,$height);
$graphTemp->SetScale('intlin');
$graphTemp->title->Set('Temperature on '.$date.' in '.$location.'');
$graphTemp->yaxis->title->Set('Temperature (K)');
$graphTemp->xaxis->title->Set('Time');
$graphTemp->xaxis->SetTickLabels($times);
$lineplot=new LinePlot($temperature);
$graphTemp->Add($lineplot);
$img = $graphTemp->Stroke(_IMG_HANDLER);
ob_start();
imagepng($img);
$imageDataTemp = ob_get_contents();
ob_end_clean();

$graphHum = new Graph($width,$height);
$graphHum->SetScale('intlin');
$graphHum->title->Set('Humidity on '.$date.' in '.$location.'');
$graphHum->yaxis->title->Set('Humidity');
$graphHum->xaxis->title->Set('Time');
$lineplot=new LinePlot($humidity);
$graphHum->Add($lineplot);
$img = $graphHum->Stroke(_IMG_HANDLER);
ob_start();
imagepng($img);
$imageDataHum = ob_get_contents();
ob_end_clean();

$graphPres = new Graph($width,$height);
$graphPres->SetScale('intlin');
$graphPres->title->Set('Pressure on '.$date.' in '.$location.'');
$graphPres->yaxis->title->Set('Pressure');
$graphPres->xaxis->title->Set('Time');
$lineplot=new LinePlot($pressure);
$graphPres->Add($lineplot);
$img = $graphPres->Stroke(_IMG_HANDLER);
ob_start();
imagepng($img);
$imageDataPres = ob_get_contents();
ob_end_clean();

$graphWindSpeed= new Graph($width,$height);
$graphWindSpeed->SetScale('intlin');
$graphWindSpeed->title->Set('Wind Speed on '.$date.' in '.$location.'');
$graphWindSpeed->yaxis->title->Set('Wind Speed');
$graphWindSpeed->xaxis->title->Set('Time');
$lineplot=new LinePlot($wind_speed);
$graphWindSpeed->Add($lineplot);
$img = $graphWindSpeed->Stroke(_IMG_HANDLER);
ob_start();
imagepng($img);
$imageDataWindSpeed = ob_get_contents();
ob_end_clean();
?>
<img src="data:image/png;base64,<?php echo(base64_encode($imageDataTemp)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageDataHum)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageDataPres)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageDataWindSpeed)); ?>" />