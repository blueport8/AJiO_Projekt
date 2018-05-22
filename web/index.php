<!doctype html>
<html lang="pl">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">

    <title>Gumtree wyszukiwarka</title>
  </head>
  <body>
    <a class="btn btn-primary" href="settings.php" role="button">Ustawienia</a>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
  </body>
</html>

<?php
   $host        = "host = localhost";
   $port        = "port = 5432";
   $dbname      = "dbname = name";
   $credentials = "user = username password=secret";

   $db = pg_connect( "$host $port $dbname $credentials"  );
   // if(!$db) {
   //    echo "Error : Unable to open database\n";
   // } else {
   //    echo "Opened database successfully\n";
   // }


   $sql =<<<EOF
      select * from advertisement order by insert_date desc limit 100
EOF;

   $ret = pg_query($db, $sql);
   if(!$ret) {
      echo pg_last_error($db);
      exit;
   } 
   while($row = pg_fetch_row($ret)) {
      echo "ID = ". $row[0] . "<br>";
      echo "TITLE = ". $row[1] ."<br>";
      echo "PRICE = ". $row[2] ."<br>";
      echo "ADDED_DATE =  ".$row[4] ."<br><br>";
   }
   echo "Operation done successfully<br>";
   pg_close($db);
?>