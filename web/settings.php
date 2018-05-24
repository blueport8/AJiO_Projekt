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
  	<div style="width: 850px; margin-left: 20px; margin-top: 20px;">
	<?php
		$host        = "host = localhost";
		$port        = "port = 5432";
		$dbname      = "dbname = test";
		$credentials = "user = test password=test";
		$db = pg_connect("$host $port $dbname $credentials");

		if(isset($_POST['city'])) {
			$city_id_to_save = $_POST['city'];
			$sql = "update configuration set value = '$city_id_to_save' where key = 'city_id' and configuration_variant_id = 1";
			pg_query($db, $sql);
		}
		if(isset($_POST['room_number_min'])) {
			$room_number_min_to_save = $_POST['room_number_min'];
			$sql = "update configuration set value = '$room_number_min_to_save' where key = 'room_number_min' and configuration_variant_id = 1";
			pg_query($db, $sql);
		}
		if(isset($_POST['room_number_max'])) {
			$room_number_max_to_save = $_POST['room_number_max'];
			$sql = "update configuration set value = '$room_number_max_to_save' where key = 'room_number_max' and configuration_variant_id = 1";
			pg_query($db, $sql);
		}
		if(isset($_POST['price_min'])) {
			$price_min_to_save = $_POST['price_min'];
			$sql = "update configuration set value = '$price_min_to_save' where key = 'price_min' and configuration_variant_id = 1";
			pg_query($db, $sql);
		}
		if(isset($_POST['price_max'])) {
			$price_max_to_save = $_POST['price_max'];
			$sql = "update configuration set value = '$price_max_to_save' where key = 'price_max' and configuration_variant_id = 1";
			pg_query($db, $sql);
		}
		if(isset($_POST['size_min'])) {
			$size_min_to_save = $_POST['size_min'];
			$sql = "update configuration set value = '$size_min_to_save' where key = 'size_min' and configuration_variant_id = 1";
			pg_query($db, $sql);
		}
		if(isset($_POST['size_max'])) {
			$size_max_to_save = $_POST['size_max'];
			$sql = "update configuration set value = '$size_max_to_save' where key = 'size_max' and configuration_variant_id = 1";
			pg_query($db, $sql);
		}
		if(isset($_POST['tentant'])) {
			$tentant_to_save = $_POST['tentant'];
			$sql = "update configuration set value = '$tentant_to_save' where key = 'tentant_choice' and configuration_variant_id = 1";
			pg_query($db, $sql);
		}
		if(isset($_POST['add_date'])) {
			$add_date_to_save = $_POST['add_date'];
			$sql = "update configuration set value = '$add_date_to_save' where key = 'add_date' and configuration_variant_id = 1";
			pg_query($db, $sql);
		}
		if(isset($_POST['positive_words'])) {
			$positive_words_to_save = $_POST['positive_words'];
			$sql = "update configuration set value = '$positive_words_to_save' where key = 'positive_words' and configuration_variant_id = 1";
			pg_query($db, $sql);
		}
		if(isset($_POST['negative_words'])) {
			$negative_words_to_save = $_POST['negative_words'];
			$sql = "update configuration set value = '$negative_words_to_save' where key = 'negative_words' and configuration_variant_id = 1";
			pg_query($db, $sql);
		}

		$sql = "select name from configuration_variant where id = 1";
		$ret = pg_query($db, $sql);
		$row = pg_fetch_row($ret);
		$configuration_name = $row[0];
		//print_r($_POST);

	?>
    <h3>Ustawienia: <?php echo $configuration_name ?></h3>
    <br>
    <form method="POST" action="settings.php">
    <div style="width: 400px; float: left;">
    	<h4>Miasto:</h4>
	    <select name="city" class="custom-select">
		<?php 
			$sql = "select c.id, c.name from City c where c.id in (select cast(value as INTEGER) from configuration where key = 'city_id' and configuration_variant_id = 1) union all select c.id, c.name from City c where c.id not in (select cast(value as INTEGER) from configuration where key = 'city_id' and configuration_variant_id = 1)";
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
	    		echo "</option>";
	   		}
		?>
		</select>

		<br><br>
		<h4>Liczba pokoi:</h4>
		<div class="input-group mb-3">
			<div class="input-group-prepend">
				<span class="input-group-text" id="inputGroup-sizing-default">Min</span>
			</div>
			<input name="room_number_min" type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" value=
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
			<input name="room_number_max" type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" value=
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
		  	<input name="price_min" type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" value=
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
		  	<input name="price_max" type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" value=
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
		<h4>Wielkość (m2):</h4>
		<div class="input-group mb-3">
		  	<div class="input-group-prepend">
		    	<span class="input-group-text" id="inputGroup-sizing-default">Min</span>
		  	</div>
		  	<input name="size_min" type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" value=
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
		  	<input name="size_max" type="text" class="form-control" aria-label="Default" aria-describedby="inputGroup-sizing-default" value=
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
		?>
		<br>
		<h4>Najemca:</h4>
		<div class="custom-control custom-radio custom-control-inline">
		  <input type="radio" id="customRadioInline1" name="tentant" value="W" class="custom-control-input" 
		  	<?php if($tentant_option[0] == 'W') echo "checked"; ?>>
		  <label class="custom-control-label" for="customRadioInline1">Właściciel</label>
		</div>
		<div class="custom-control custom-radio custom-control-inline">
		  <input type="radio" id="customRadioInline2" name="tentant" value="A" class="custom-control-input"
		  <?php if($tentant_option[0] == 'A') echo "checked"; ?>>
		  <label class="custom-control-label" for="customRadioInline2">Agencja</label>
		</div>
		<div class="custom-control custom-radio custom-control-inline">
		  <input type="radio" id="customRadioInline3" name="tentant" value="All" class="custom-control-input"
		  <?php if($tentant_option[0] == 'All') echo "checked"; ?>>
		  <label class="custom-control-label" for="customRadioInline3">Wszystkie</label>
		</div>


		<?php
			$sql = "select value from configuration where configuration_variant_id = 1 and key = 'add_date' limit 1";
			$ret = pg_query($db, $sql);
			if(!$ret) {
				echo pg_last_error($db);
				exit;
			}
			$tentant_option = pg_fetch_row($ret);
		?>
		<br><br>
		<h4>Wyświetl ogłoszenia:</h4>
		<div class="custom-control custom-radio custom-control-inline">
		  <input type="radio" id="customRadioInline4" name="add_date" value="D" class="custom-control-input" 
		  	<?php if($tentant_option[0] == 'D') echo "checked"; ?>>
		  <label class="custom-control-label" for="customRadioInline4">Dodane dzisiaj</label>
		</div>
		<div class="custom-control custom-radio custom-control-inline">
		  <input type="radio" id="customRadioInline5" name="add_date" value="W" class="custom-control-input"
		  <?php if($tentant_option[0] == 'W') echo "checked"; ?>>
		  <label class="custom-control-label" for="customRadioInline5">Dodane od wczoraj</label>
		</div>
		<div class="custom-control custom-radio custom-control-inline">
		  <input type="radio" id="customRadioInline6" name="add_date" value="P" class="custom-control-input"
		  <?php if($tentant_option[0] == 'P') echo "checked"; ?>>
		  <label class="custom-control-label" for="customRadioInline6">Dodane od przedwczoraj</label>
		</div>

		<br><br>
		<button type="submit" class="btn btn-primary">Zapisz</button><a class="btn btn-primary" href="index.php" role="button" style="margin-left: 20px;">Wyniki</a>
	</div>
	<div style="width: 400px; float: left; margin-left: 20px;">
		<?php
			$sql = "select value from configuration where configuration_variant_id = 1 and key = 'positive_words' limit 1";
			$ret = pg_query($db, $sql);
			if(!$ret) {
				echo pg_last_error($db);
				exit;
			}
			$posititve_option_row = pg_fetch_row($ret);

			$sql = "select value from configuration where configuration_variant_id = 1 and key = 'negative_words' limit 1";
			$ret = pg_query($db, $sql);
			if(!$ret) {
				echo pg_last_error($db);
				exit;
			}
			$negative_option_row = pg_fetch_row($ret);
		?>
		<h4 style="margin-bottom: 1px;">Słowa kluczowe:</h4>
		<p style="margin-top: 1px; margin-bottom: 5px;">Oddzielone średnikami. Spacje i entery będą pominięte.</p>
		<div class="input-group" style="margin-bottom: 20px;">
		  <div class="input-group-prepend">
		    <span class="input-group-text">Pozytywne</span>
		  </div>
		  <textarea name="positive_words" class="form-control" style="height: 150px;" aria-label="With textarea"><?php echo $posititve_option_row[0]; ?></textarea>
		</div>

		<div class="input-group">
		  <div class="input-group-prepend">
		    <span class="input-group-text">Negatywne</span>
		  </div>
		  <textarea name="negative_words" class="form-control" style="height: 150px;" aria-label="With textarea"><?php echo $negative_option_row[0]; ?></textarea>
		</div>
	</div>
	</form>
</div>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
  </body>
</html>