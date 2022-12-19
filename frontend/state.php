<?php // content="text/plain; charset=utf-8"
require_once("putenv.php");
require_once ('jpgraph/src/jpgraph.php');
require_once ('jpgraph/src/jpgraph_line.php');
require_once ('jpgraph/src/jpgraph_bar.php');

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
$location = $_GET["state"];

// Get weather data
$times = array();
$humidity = array();
$pressure = array();
$temperature = array();
$wind_direction = array();
$wind_speed = array();
if ($mysqli->multi_query("CALL getStateWeather('".$date."', '".$location."');")) {
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

// Get flight data for airports in the given location at the specified date
clearStoredResults();
$flight_out = array();
$flight_in = array();
if ($mysqli->multi_query("CALL outgoingStateFlights('".$date."', '".$location."');")) {
    if ($result = $mysqli->store_result()) {
        $row = $result->fetch_row();
        array_push($flight_out, $row[0]);
        $result->close();
    }
} else {
    printf("<br>Error: %s\n", $mysqli->error);
}

clearStoredResults();
if ($mysqli->multi_query("CALL incomingStateFlights('".$date."', '".$location."');")) {
    if ($result = $mysqli->store_result()) {
        $row = $result->fetch_row();
        array_push($flight_in, $row[0]);
        $result->close();
    }
} else {
    printf("<br>Error: %s\n", $mysqli->error);
}

// Get delay data
clearStoredResults();
$weather_delay = array();
$NAS_delay = array();
$security_delay = array();
$late_aircraft_delay = array();
if ($mysqli->multi_query("CALL locationStateDelays('".$date."', '".$location."');")) {
    if ($result = $mysqli->store_result()) {
        $row = $result->fetch_row();
        if (count($row) == 1) {
            pass;
        } else {
            do {
                array_push($weather_delay, $row[0]);
                array_push($NAS_delay, $row[1]);
                array_push($security_delay, $row[2]);
                array_push($late_aircraft_delay, $row[3]);
            } while ($row = $result->fetch_row());
        }
        $result->close();
    }
} else {
    printf("<br>Error: %s\n", $mysqli->error);
}

clearStoredResults();
$cancel = array();
if ($mysqli->multi_query("CALL locationStateCancels('".$date."', '".$location."');")) {
    if ($result = $mysqli->store_result()) {
        $row = $result->fetch_row();
        array_push($cancel, $row[0]);
        $result->close();
    }
} else {
    printf("<br>Error: %s\n", $mysqli->error);
}


// Size of the overall graphs
$width=400;
$height=400;

$graph = new Graph($width,$height);
$graph->SetScale('intlin');
$graph->title->Set('Temperature on '.$date.' in '.$location.'');
$graph->yaxis->title->Set('Temperature (K)');
$graph->xaxis->title->Set('Time');
$graph->xaxis->SetTickLabels($times);
$lineplot=new LinePlot($temperature);
$graph->Add($lineplot);
$img = $graph->Stroke(_IMG_HANDLER);
ob_start();
imagepng($img);
$imageDataTemp = ob_get_contents();
ob_end_clean();

$graph = new Graph($width,$height);
$graph->SetScale('intlin');
$graph->title->Set('Humidity on '.$date.' in '.$location.'');
$graph->yaxis->title->Set('Humidity');
$graph->xaxis->title->Set('Time');
$lineplot=new LinePlot($humidity);
$graph->Add($lineplot);
$img = $graph->Stroke(_IMG_HANDLER);
ob_start();
imagepng($img);
$imageDataHum = ob_get_contents();
ob_end_clean();

$graph = new Graph($width,$height);
$graph->SetScale('intlin');
$graph->title->Set('Pressure on '.$date.' in '.$location.'');
$graph->yaxis->title->Set('Pressure');
$graph->xaxis->title->Set('Time');
$lineplot=new LinePlot($pressure);
$graph->Add($lineplot);
$img = $graph->Stroke(_IMG_HANDLER);
ob_start();
imagepng($img);
$imageDataPres = ob_get_contents();
ob_end_clean();

$graph= new Graph($width,$height);
$graph->SetScale('intlin');
$graph->title->Set('Wind Speed on '.$date.' in '.$location.'');
$graph->yaxis->title->Set('Wind Speed');
$graph->xaxis->title->Set('Time');
$lineplot=new LinePlot($wind_speed);
$graph->Add($lineplot);
$img = $graph->Stroke(_IMG_HANDLER);
ob_start();
imagepng($img);
$imageDataWindSpeed = ob_get_contents();
ob_end_clean();

$graph = new Graph($width,$height,'auto');
$graph->SetScale("textlin");
$theme_class=new UniversalTheme;
$graph->SetTheme($theme_class);
$graph->SetBox(false);
$graph->ygrid->SetFill(false);
$graph->xaxis->SetTickLabels($airports_out);
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);
$bplot = new BarPlot($flight_out);
$graph->Add($bplot);
$graph->title->Set('Flights out of '.$location.' on '.$date.'');
$graph->yaxis->title->Set('# of Flights');
$graph->xaxis->title->Set(''.$location.' Airports');
$img = $graph->Stroke(_IMG_HANDLER);
ob_start();
imagepng($img);
$imageFlightOut = ob_get_contents();
ob_end_clean();

$graph = new Graph($width,$height,'auto');
$graph->SetScale("textlin");
$theme_class=new UniversalTheme;
$graph->SetTheme($theme_class);
$graph->SetBox(false);
$graph->ygrid->SetFill(false);
$graph->xaxis->SetTickLabels($airports_in);
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);
$bplot = new BarPlot($flight_in);
$graph->Add($bplot);
$graph->title->Set('Flights into '.$location.' on '.$date.'');
$graph->yaxis->title->Set('# of Flights');
$graph->xaxis->title->Set(''.$location.' Airports');
$img = $graph->Stroke(_IMG_HANDLER);
ob_start();
imagepng($img);
$imageFlightIn = ob_get_contents();
ob_end_clean();

$graph = new Graph($width,$height,'auto');
$graph->SetScale("textlin");
$theme_class=new UniversalTheme;
$graph->SetTheme($theme_class);
$graph->SetBox(false);
$graph->ygrid->SetFill(false);
$graph->xaxis->SetTickLabels($airports_in);
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);
$b1plot = new BarPlot($weather_delay);
$b2plot = new BarPlot($NAS_delay);
$b3plot = new BarPlot($security_delay);
$b4plot = new BarPlot($late_aircraft_delay);
$gbplot = new GroupBarPlot(array($b1plot,$b2plot,$b3plot,$b4plot));
$graph->Add($gbplot);
$b1plot->SetColor("white");
$b1plot->SetFillColor("#cc1111");
$b1plot->SetLegend("Weather Delay");
$b2plot->SetColor("white");
$b2plot->SetFillColor("#11cccc");
$b2plot->SetLegend("NAS Delay");
$b3plot->SetColor("white");
$b3plot->SetFillColor("#1111cc");
$b3plot->SetLegend("Security Delay");
$b4plot->SetColor("white");
$b4plot->SetFillColor("#cc6211");
$b4plot->SetLegend("Late Aircraft Delay");
$graph->title->Set('Average Delays in Flights at '.$location.' on '.$date.'');
$graph->yaxis->title->Set('Minutes');
$graph->xaxis->title->Set(''.$location.' Airports');
$img = $graph->Stroke(_IMG_HANDLER);
ob_start();
imagepng($img);
$imageDelays = ob_get_contents();
ob_end_clean();

$graph = new Graph($width,$height,'auto');
$graph->SetScale("textlin");
$theme_class=new UniversalTheme;
$graph->SetTheme($theme_class);
$graph->SetBox(false);
$graph->ygrid->SetFill(false);
$graph->xaxis->SetTickLabels($airport_cancel);
$graph->yaxis->HideLine(false);
$graph->yaxis->HideTicks(false,false);
$bplot = new BarPlot($cancel);
$graph->Add($bplot);
$graph->title->Set('Flight Cancellations at '.$location.' on '.$date.'');
$graph->yaxis->title->Set('# of Flights');
$graph->xaxis->title->Set(''.$location.' Airports');
$img = $graph->Stroke(_IMG_HANDLER);
ob_start();
imagepng($img);
$imageCancels = ob_get_contents();
ob_end_clean();
?>
<img src="data:image/png;base64,<?php echo(base64_encode($imageDataTemp)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageDataHum)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageDataPres)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageDataWindSpeed)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageFlightOut)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageFlightIn)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageDelays)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageCancels)); ?>" />
