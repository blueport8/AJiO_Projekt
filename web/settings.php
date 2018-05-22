<!doctype html>
<html lang="pl">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">

    <title>Gumtree wyszukiwarka - ustawienia</title>
  </head>
  <body>
	<?php
		$host        = "host = localhost";
   		$port        = "port = 5432";
   		$dbname      = "dbname = name";
   		$credentials = "user = username password=secret";
		$db = pg_connect("$host $port $dbname $credentials");
	?>
    <h3>Ustawienia</h3>
    <br>
    <h4>Miasto:</h4>
    <select id="city" class="custom-select">
	<?php 
		$sql = "select * from city";
		$ret = pg_query($db, $sql);
		if(!$ret) {
			echo pg_last_error($db);
			exit;
		} 
    	while($row = pg_fetch_row($ret)) {
    		echo "<option value=\"";
    		echo $row[0];
    		echo "\">";
    		echo $row[1];
    		echo "<option>";
   		}
	?>
	</select>

	<br><br>
	<h4>Liczba pokoi:</h4>
	<div class="input-group mb-3">
		<div class="input-group-prepend">
			<span class="input-group-text" id="inputGroup-sizing-default">Min</span>
		</div>
		<input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" value=
		<?php
			$sql = "select value from configuration where configuration_variant_id = 1 and key = 'room_number_min' limit 1";
			$ret = pg_query($db, $sql);
			if(!$ret) {
				echo pg_last_error($db);
				exit;
			} 
	    	$row = pg_fetch_row($ret);
	    	echo "\"" . $row[0] . "\"/>";
		?>
	</div>
	<div class="input-group mb-3">
	  	<div class="input-group-prepend">
	    	<span class="input-group-text" id="inputGroup-sizing-default">Max</span>
	  	</div>
		<input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" value=
	  	<?php
			$sql = "select value from configuration where configuration_variant_id = 1 and key = 'room_number_max' limit 1";
			$ret = pg_query($db, $sql);
			if(!$ret) {
				echo pg_last_error($db);
				exit;
			} 
	    	$row = pg_fetch_row($ret);
	    	echo "\"" . $row[0] . "\"/>";
		?>
	</div>


	<br>
	<h4>Cena:</h4>
	<div class="input-group mb-3">
	  	<div class="input-group-prepend">
	    	<span class="input-group-text" id="inputGroup-sizing-default">Min</span>
	  	</div>
	  	<input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" value=
	  	<?php
			$sql = "select value from configuration where configuration_variant_id = 1 and key = 'price_min' limit 1";
			$ret = pg_query($db, $sql);
			if(!$ret) {
				echo pg_last_error($db);
				exit;
			} 
	    	$row = pg_fetch_row($ret);
	    	echo "\"" . $row[0] . "\"/>";
		?>
	</div>
	<div class="input-group mb-3">
	  	<div class="input-group-prepend">
	    	<span class="input-group-text" id="inputGroup-sizing-default">Max</span>
	  	</div>
	  	<input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" value=
	  	<?php
			$sql = "select value from configuration where configuration_variant_id = 1 and key = 'price_max' limit 1";
			$ret = pg_query($db, $sql);
			if(!$ret) {
				echo pg_last_error($db);
				exit;
			} 
	    	$row = pg_fetch_row($ret);
	    	echo "\"" . $row[0] . "\"/>";
		?>
	</div>

	<br>
	<h4>Rozmiar:</h4>
	<div class="input-group mb-3">
	  	<div class="input-group-prepend">
	    	<span class="input-group-text" id="inputGroup-sizing-default">Min</span>
	  	</div>
	  	<input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" value=
	  	<?php
			$sql = "select value from configuration where configuration_variant_id = 1 and key = 'size_min' limit 1";
			$ret = pg_query($db, $sql);
			if(!$ret) {
				echo pg_last_error($db);
				exit;
			} 
	    	$row = pg_fetch_row($ret);
	    	echo "\"" . $row[0] . "\"/>";
		?>
	</div>
	<div class="input-group mb-3">
	  	<div class="input-group-prepend">
	    	<span class="input-group-text" id="inputGroup-sizing-default">Max</span>
	  	</div>
	  	<input type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" value=
	  	<?php
			$sql = "select value from configuration where configuration_variant_id = 1 and key = 'size_max' limit 1";
			$ret = pg_query($db, $sql);
			if(!$ret) {
				echo pg_last_error($db);
				exit;
			} 
	    	$row = pg_fetch_row($ret);
	    	echo "\"" . $row[0] . "\"/>";
		?>
	</div>


	<?php
		$sql = "select value from configuration where configuration_variant_id = 1 and key = 'tentant_choice' limit 1";
		$ret = pg_query($db, $sql);
		if(!$ret) {
			echo pg_last_error($db);
			exit;
		}
		$tentant_option = pg_fetch_row($ret);
		echo $tentant_option;
	?>
	<br>
	<h4>Najemca:</h4>
	<div class="custom-control custom-radio custom-control-inline">
	  <input type="radio" id="customRadioInline1" name="customRadioInline1" class="custom-control-input" 
	  	<?php if($tentant_option[0] == 'W') echo "checked"; ?>>
	  <label class="custom-control-label" for="customRadioInline1">Właściciel</label>
	</div>
	<div class="custom-control custom-radio custom-control-inline">
	  <input type="radio" id="customRadioInline2" name="customRadioInline1" class="custom-control-input"
	  <?php if($tentant_option[0] == 'A') echo "checked"; ?>>
	  <label class="custom-control-label" for="customRadioInline2">Agencja</label>
	</div>
	<div class="custom-control custom-radio custom-control-inline">
	  <input type="radio" id="customRadioInline3" name="customRadioInline1" class="custom-control-input"
	  <?php if($tentant_option[0] == 'All') echo "checked"; ?>>
	  <label class="custom-control-label" for="customRadioInline3">Wszystkie</label>
	</div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
  </body>
</html>