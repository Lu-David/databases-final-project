<head>
  <title>Flights</title>
</head>
<body>

<?php
// require_once ('./jpgraph.php');
// require_once ('jpgraph_line.php');
$dbhost = 'dbase.cs.jhu.edu';
$dbuser = $_ENV["USER"];
$dbpass = $_ENV["PASSWORD"];
$dbname = $_ENV["DB"];
$mysqli = new mysqli($dbhost, $dbuser, $dbpass, $dbname);
if (mysqli_connect_errno()) {
    printf("Connect failed: %s<br>", mysqli_connect_error());
    exit();
}
$StartDate = $_GET["StartDate"];
$EndDate = $_GET["EndDate"];
//$flight_count = [];
if ($mysqli->multi_query("CALL getFlightsPerDate('".$StartDate."', '".$EndDate."');")) {
    if ($result = $mysqli->store_result()) {
        $row = $result->fetch_row();
        if (count($row) == 1) {
            printf("%s", $row[0]);
        } else {
            do {
                printf("%s | %s <br>", $row[0], $row[1]);
            } while ($row = $result->fetch_row());
        }
        $result->close();
    }
} else {
    printf("<br>Error: %s\n", $mysqli->error);
}
?>
</body>