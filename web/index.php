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
    <a style="margin-left: 20px; margin-top: 20px;" class="btn btn-primary" href="settings.php" role="button">Ustawienia</a><br>
    <h4 style="margin-left: 20px; margin-top: 20px;">Ogłoszenia:</h4><br>
    <div style="margin-left: 20px; margin-right: 50px;">
      <table class="table table-sm">
      <thead>
        <tr>
          <th scope="col">#</th>
          <th scope="col">Tytuł</th>
          <th scope="col">Cena</th>
          <th scope="col">Data dodania</th>
          <th scope="col">Pokoje</th>
          <th scope="col">Słowa kluczowe<br>pozytywne</th>
          <th scope="col">Słowa kluczowe<br>negatywne</th>
        </tr>
      </thead>
        <tbody>
        <?php
           $host        = "host = localhost";
           $port        = "port = 5432";
           $dbname      = "dbname = test";
           $credentials = "user = test password=test";

           $db = pg_connect( "$host $port $dbname $credentials"  );

           $config = [];
           $sql = "select key, value from configuration where configuration_variant_id = 1";
           $ret = pg_query($db, $sql);
           while($row = pg_fetch_row($ret)) {
              $config[trim($row[0])] = $row[1];
           }

           $city_id = $config["city_id"];
           $room_number_min = $config["room_number_min"];
           $room_number_max = $config["room_number_max"];
           $price_min = $config["price_min"];
           $price_max = $config["price_max"];
           $size_min = $config["size_min"];
           $size_max = $config["size_max"];
           $sql = "select id, title, price, insert_date, rooms, url, description from advertisement where city_id = $city_id and rooms >= $room_number_min and rooms <= $room_number_max";
           $sql .= " and price >= $price_min and price <= $price_max and size >= $size_min and size <= $size_max";
           $tentant_choice = $config["tentant_choice"];
           if($tentant_choice != "All") {
              $sql .= " tentant = '$tentant_choice'";
           }
           $add_time = $config["add_date"];
           date_default_timezone_set('UTC');
           switch ($add_time) {
             case 'D':
                // Z dzisiaj
                $date_str = date('Y-m-d 00:00:00');
                $sql .= " and insert_date > '$date_str'";
                break;
             case 'W':
                // Od wczoraj
                $date_str = date("Y-m-d", time() + 86400);
                $sql .= " and insert_date > '$date_str'";
                break;
             case 'P':
                // Od przedwczoraj
                $date_str = date("Y-m-d", time() + 172800);
                $sql .= " and insert_date > '$date_str'";
                break;
             
             default:
                # Other? o_O
                break;
           }
           $sql .= " order by insert_date desc";

           $ret = pg_query($db, $sql);
           if(!$ret) {
              echo pg_last_error($db);
              exit;
           } 

           $positive_words_raw = preg_replace('/\s+/', '', str_replace(' ', '', $config["positive_words"]));
           $negative_words_raw = preg_replace('/\s+/', '', str_replace(' ', '', $config["negative_words"]));
           $positive_words = array_filter(explode(";", $positive_words_raw));
           $negative_words = array_filter(explode(";", $negative_words_raw));


           while($row = pg_fetch_row($ret)) {
            $positive_score = 0;
            $negative_score = 0;
            $description = $row[6];

            foreach ($positive_words as &$val) {
              if(strpos($description, $val) !== false) {
                $positive_score++;
              }
            }
            foreach ($negative_words as &$val) {
              if(strpos($description, $val) !== false) {
                $negative_score++;
              }
            }

            echo "<tr>";
            $id = $row[0];
            echo "<th scope=\"row\">$id</th>";
            $title = $row[1];
            $url = $row[5];
            echo "<td><a target=\"_blank\" href=\"$url\">$title</a></td>";
            $price = $row[2];
            echo "<td>$price</td>";
            $add_date = $row[3];
            echo "<td>$add_date</td>";
            $rooms = $row[4];
            echo "<td>$rooms</td>";
            echo "<td style=\"color: green;\">$positive_score</td>";
            echo "<td style=\"color: red;\">$negative_score</td>";
            echo "</tr>";
           }
           pg_close($db);
        ?>
        </tbody>
      </table>
    </div>
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js" integrity="sha384-smHYKdLADwkXOn1EmN1qk/HfnUcbVRZyYmZ4qpPea6sjB/pTJ0euyQp0Mk8ck+5T" crossorigin="anonymous"></script>
  </body>
</html>