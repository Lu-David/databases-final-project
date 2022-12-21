<?php // content="text/plain; charset=utf-8"
require_once("putenv.php");
require_once ('jpgraph/jpgraph.php');
require_once ('jpgraph/jpgraph_line.php');
require_once ('jpgraph/jpgraph_bar.php');

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

$year = $_GET["year"];
$airline = $_GET["airline"];

$flight_dates = array();
$num_flights = array();
if ($mysqli->multi_query("CALL airlineFlights('".$year."', '".$airline."');")) {
  if ($result = $mysqli->store_result()) {
      $row = $result->fetch_row();
      if (count($row) == 1) {
          pass;
      } else {
          do {
              array_push($flight_dates, $row[0]);
              array_push($num_flights, $row[1]);
          } while ($row = $result->fetch_row());
      }
      $result->close();
  }
} else {
  printf("<br>Error: %s\n", $mysqli->error);
}

clearStoredResults();
$delay_dates = array();
$num_delays = array();
if ($mysqli->multi_query("CALL airlineDelays('".$year."', '".$airline."');")) {
  if ($result = $mysqli->store_result()) {
      $row = $result->fetch_row();
      if (count($row) == 1) {
          pass;
      } else {
          do {
              array_push($delay_dates, $row[0]);
              array_push($num_delays, $row[1]);
          } while ($row = $result->fetch_row());
      }
      $result->close();
  }
} else {
  printf("<br>Error: %s\n", $mysqli->error);
}

clearStoredResults();
$cancel_dates = array();
$num_cancels = array();
if ($mysqli->multi_query("CALL airlineCancels('".$year."', '".$airline."');")) {
  if ($result = $mysqli->store_result()) {
      $row = $result->fetch_row();
      if (count($row) == 1) {
          pass;
      } else {
          do {
              array_push($cancel_dates, $row[0]);
              array_push($num_cancels, $row[1]);
          } while ($row = $result->fetch_row());
      }
      $result->close();
  }
} else {
  printf("<br>Error: %s\n", $mysqli->error);
}

clearStoredResults();
$stock_dates = array();
$open = array();
$close = array();
if ($mysqli->multi_query("CALL airlineStock('".$year."', '".$airline."');")) {
  if ($result = $mysqli->store_result()) {
      $row = $result->fetch_row();
      if (count($row) == 1) {
          pass;
      } else {
          do {
              array_push($stock_dates, $row[0]);
              array_push($open, $row[1]);
              array_push($close, $row[2]);
          } while ($row = $result->fetch_row());
      }
      $result->close();
  }
} else {
  printf("<br>Error: %s\n", $mysqli->error);
}

clearStoredResults();
$accident_dates = array();
$accidents = array();
if ($mysqli->multi_query("CALL airlineAccidents('".$year."', '".$airline."');")) {
  if ($result = $mysqli->store_result()) {
      $row = $result->fetch_row();
      if (count($row) == 1) {
          pass;
      } else {
          do {
              array_push($accident_dates, $row[0]);
              array_push($accidents, $row[1]);
          } while ($row = $result->fetch_row());
      }
      $result->close();
  }
} else {
  printf("<br>Error: %s\n", $mysqli->error);
}
$accident_index = 0;
$num_accidents = array();
for ($x=0; $x<count($flight_dates); $x++) {
  if ($flight_dates[$x] == $accident_dates[$accident_index] && $accident_index < count($accidents)) {
    array_push($num_accidents, $accidents[$accident_index]);
    $accident_index++;
  } else {
    array_push($num_accidents, 0);
  }
}

// Size of the overall graphs
$width=400;
$height=400;

if ($num_flights[0]!="") {
  $graph = new Graph($width,$height,'auto');
  $graph->SetScale("textlin");
  $theme_class=new UniversalTheme;
  $graph->SetTheme($theme_class);
  $graph->SetBox(false);
  $graph->ygrid->SetFill(false);
  $graph->xaxis->HideLabels(True);
  $graph->yaxis->HideLine(false);
  $graph->yaxis->HideTicks(false,false);
  $bplot = new BarPlot($num_flights);
  $graph->Add($bplot);
  $graph->title->Set('Number of '.$airline.' flights during '.$year.'');
  $graph->yaxis->title->Set('# of Flights');
  $graph->xaxis->title->Set('Dates');
  $img = $graph->Stroke(_IMG_HANDLER);
  ob_start();
  imagepng($img);
  $imageNumFlights = ob_get_contents();
  ob_end_clean();
} else {
  $imageNumFlights=file_get_contents("datanotavail.png");
}

if ($num_delays[0]!="") {
  $graph = new Graph($width,$height,'auto');
  $graph->SetScale("textlin");
  $theme_class=new UniversalTheme;
  $graph->SetTheme($theme_class);
  $graph->SetBox(false);
  $graph->ygrid->SetFill(false);
  $graph->xaxis->HideLabels(True);
  $graph->yaxis->HideLine(false);
  $graph->yaxis->HideTicks(false,false);
  $bplot = new BarPlot($num_delays);
  $graph->Add($bplot);
  $graph->title->Set('Number of '.$airline.' delays during '.$year.'');
  $graph->yaxis->title->Set('# of Delays');
  $graph->xaxis->title->Set('Dates');
  $img = $graph->Stroke(_IMG_HANDLER);
  ob_start();
  imagepng($img);
  $imageNumDelays = ob_get_contents();
  ob_end_clean();
} else {
  $imageNumDelays=file_get_contents("datanotavail.png");
}

if ($num_cancels[0]!="") {
  $graph = new Graph($width,$height,'auto');
  $graph->SetScale("textlin");
  $theme_class=new UniversalTheme;
  $graph->SetTheme($theme_class);
  $graph->SetBox(false);
  $graph->ygrid->SetFill(false);
  $graph->xaxis->HideLabels(True);
  $graph->yaxis->HideLine(false);
  $graph->yaxis->HideTicks(true,true);
  $graph->yaxis->HideLabels(true);
  $bplot = new BarPlot($num_cancels);
  $graph->Add($bplot);
  $graph->title->Set('Number of '.$airline.' cancels during '.$year.'');
  $graph->yaxis->title->Set('# of Cancels');
  $graph->xaxis->title->Set('Dates');
  $img = $graph->Stroke(_IMG_HANDLER);
  ob_start();
  imagepng($img);
  $imageNumCancels = ob_get_contents();
  ob_end_clean();
} else {
  $imageNumCancels=file_get_contents("datanotavail.png");
}

if ($open[0]!="") {
  $graph = new Graph($width,$height);
  $graph->SetScale("textlin");
  $graph->title->Set(''.$airline.' Stock Close and Open during '.$year.'');
  $graph->yaxis->title->Set('Price');
  $graph->xaxis->title->Set('Dates');
  $graph->xaxis->HideLabels(True);
  $openplot=new LinePlot($open);
  $graph->Add($openplot);
  $openplot->SetColor("#6495ED");
  $openplot->SetLegend('open');
  $closeplot=new LinePlot($close);
  $graph->Add($closeplot);
  $closeplot->SetColor("#B22222");
  $closeplot->SetLegend('close');
  $img = $graph->Stroke(_IMG_HANDLER);
  ob_start();
  imagepng($img);
  $imageStocks = ob_get_contents();
  ob_end_clean();
} else {
  $imageStocks=file_get_contents("datanotavail.png");
}

if ($accidents[0]!="") {
  $graph = new Graph($width,$height,'auto');
  $graph->SetScale("textlin");
  $theme_class=new UniversalTheme;
  $graph->SetTheme($theme_class);
  $graph->SetBox(false);
  $graph->ygrid->SetFill(false);
  $graph->xaxis->HideLabels(True);
  $graph->yaxis->HideLine(false);
  $graph->yaxis->HideTicks(true,true);
  $graph->yaxis->HideLabels(true);
  $bplot = new BarPlot($num_accidents);
  $graph->Add($bplot);
  $graph->title->Set('Number of '.$airline.' accidents during '.$year.'');
  $graph->yaxis->title->Set('# of Accidents');
  $graph->xaxis->title->Set('Dates');
  $img = $graph->Stroke(_IMG_HANDLER);
  ob_start();
  imagepng($img);
  $imageAccidents = ob_get_contents();
  ob_end_clean();
} else {
  $imageAccidents=file_get_contents("datanotavail.png");
}

?>
<img src="data:image/png;base64,<?php echo(base64_encode($imageNumFlights)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageNumDelays)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageNumCancels)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageStocks)); ?>" />
<img src="data:image/png;base64,<?php echo(base64_encode($imageAccidents)); ?>" />
