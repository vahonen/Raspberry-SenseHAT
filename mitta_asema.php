<!DOCTYPE html>
<html>
<head>

<title>Mitta-asema</title>
<!-- <link rel="stylesheet" href="styles.css"> -->

<style>

body {
    background-color: powderblue;
}

h1 {
    color: black;
}

table {
margin: 8px;
border: 1px double black;
}

th {
font-family: Arial, Helvetica, sans-serif;
font-size: 1em;
background: cyan;
color: black;
padding: 5px;
border: 1px double black;

}

td {
font-family: Arial, Helvetica, sans-serif;
font-size: 1em;
padding: 5px
border: 1px double black;
}

form  { display: table;      }
p     { display: table-row;  }
label { display: table-cell; }
input { display: table-cell; }

</style>



<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.0/jquery.min.js"></script>
<script type="text/javascript" src="./gauge.js"></script>
<script type="text/javascript" src="./jquery.gauge.js"></script>

</head>

<body>

<h1>SenseHAT-mittadataa</h1>
<p><b>Viimeisin mittaus:</b><p>
<p id="currentTime"></p>

<canvas id="temperaturegauge" width="250" height="250"></canvas>
<canvas id="humiditygauge" width="200" height="200"></canvas>
<canvas id="pressuregauge" width="200" height="200"></canvas>

<form name="askDateRange" method="POST">

<p>
	<label>Mistä:</label>
    <input type="date" name="dateFrom" value="<?php echo date("Y-m-d"); ?>"/>
</p>
<p>
    <label>Mihin:</label>
    <input type="date" name="dateTo" value="<?php echo date("Y-m-d"); ?>" />
</p>
<p>
    <input type="submit" name="submit" value="Hae"/>
</p>
</form>

<?php
	
	$start_date = date('Y-m-d', strtotime($_POST['dateFrom']));
	$stop_date = date('Y-m-d', strtotime($_POST['dateTo']));
	
    $con=mysqli_connect("localhost", "testaaja", "salasana", "sense_data");
    
    if (mysqli_connect_errno())
    {
      echo "MySQL-yhteys epäonnistui: " . mysqli_connect_error();
    }
	
	$sql_query = "SELECT * FROM data_table WHERE date >= '" . $start_date . "' AND date <= '" . $stop_date . "';";
	//echo $sql_query;
	$result = mysqli_query($con, $sql_query);

	echo '<table border = "1">';
	echo "<tr><th>Pvm</th><th>Aika</th><th>Lämpötila (°C)</th><th>Kosteus (%)</th><th>Paine (mbar)</th></tr>";
	while($row = mysqli_fetch_array($result))
	{
		echo "<tr><td>" . $row['date'] . "</td><td> " . $row['time'] . "</td><td>" . $row['temperature'] . "</td><td>" . $row['humidity'] . "</td><td>" . $row['pressure'] . "</td></tr>";
	}
	echo "</table>";
	
	// luetaan taulukon viimeinen rivi:
	$result = mysqli_query($con, "SELECT * FROM data_table ORDER BY idDataTable DESC LIMIT 1");
	$row = mysqli_fetch_array($result);
	
	$temperature = $row['temperature'];
	$humidity = $row['humidity'];
	$pressure = $row['pressure'];
	$curr_time = $row['date'] . " " . $row['time'];
	
    mysqli_close($con);
?>

<script type="text/javascript">

$(document).ready(function(){
		
		temperature = parseFloat("<?php echo $temperature ;?>");
		humidity = parseInt("<?php echo $humidity ;?>");
		pressure = parseInt("<?php echo $pressure ;?>");

		document.getElementById("currentTime").innerHTML = "<b><?php echo $curr_time ;?></b><br/>";
		
        $("#temperaturegauge")
          .gauge({
             min: 0,
             max: 40,
             label: 'Lämpötila',
             bands: [{color: "#0000ff", from: 0, to: 18}, {color: "#00ff00", from: 18, to: 24}, {color: "#ff0000", from: 24, to: 40}],
			 unitsLabel: '' + String.fromCharCode(186) + 'C',
			 majorTicks: 5,
			 minorTicks: 1
           })
          .gauge('setValue', temperature);
        
		$("#humiditygauge")
          .gauge({
             min: 0,
             max: 100,
             label: 'Ilmankosteus',
             bands: [{color: "#ff0000", from: 0, to: 20}, {color: "#00ff00", from: 20, to: 40}, {color: "#0000ff", from: 40, to: 100}],
			 unitsLabel: '%',
			 majorTicks: 11,
			 minorTicks: 1
           })
          .gauge('setValue', humidity);
		  
		
		$("#pressuregauge")
          .gauge({
             min: 900,
             max: 1100,
             label: 'Ilmanpaine',
			 unitsLabel: 'm',
			 majorTicks: 5,
			 minorTicks: 4
           })
          .gauge('setValue', pressure);
		  
    });
</script>
</body>
</html>