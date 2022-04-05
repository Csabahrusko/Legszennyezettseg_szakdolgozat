
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
    
    <script src="https://code.highcharts.com/highcharts.js"></script>

    <title>Légszennyezettség szakdolgozat</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">

  <!--CSS-->  

  <style>
    body{
      margin: 0 px;
      padding: 0 px;
      box-sizing: border-box;
      font-family: poppin, 'Times New Roman', Times, serif;
      font-size: large;
      background-image: url(hatter.jpg);
      color: white;
      background-size: cover;
      background-attachment: fixed;
    }
    h1{
      font-weight: 700;
      margin-top: 15px;
    }
    .input{
      width: 350px;
      padding: 5px;
    }
    table, th, td {
      border: 3px solid white;
      border-collapse: collapse;
      text-align: center; 
      vertical-align: middle;
      width: 700px;
    }

    table.center {
      margin-left: auto; 
      margin-right: auto;    
    }

    .btn{
      background-color: #36486b;
      border: none;
      color: white;
      padding: 15px 32px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      margin: 4px 2px;
      cursor: pointer;
    }
    
  </style>

  </head>

<?php
$weather="";
$error="";
$records="";
$lat="";
$lon="";
$pol="";
$CO="";
$O3="";
$NO="";
$PM25="";
$city="";
$COresponse="";
$O3response="";
$NOresponse="";
$PM25response="";
$sql="";

//Connection with DB

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_DATABASE', 'szakdolgozat');
$connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_DATABASE);

//Weather API

  if(array_key_exists('Idojaras', $_POST)){
    if(!$_POST['city']){
      $error = "Kérem adjon meg egy város nevet";
      header("Refresh:0");
      echo '<script>alert("Kérem adjon meg egy város nevet")</script>';
    }
  if($_POST['city']){
    $apiSubmit = file_get_contents("http://api.openweathermap.org/data/2.5/weather?q=".$_POST['city'].
                                    "&APPID=30c75d3c56d7565b2e6d578523ebebd5");
    $weatherArray = json_decode($apiSubmit, true);
    if ($weatherArray['cod'] == 200) {

        $tempCelsius = $weatherArray['main']['temp'] - 273;
        $weather .="<b>".$weatherArray['name'].", ".$weatherArray['sys']['country'].", ".intval($tempCelsius)."&deg;C</b><br>";
        $weather .="<b>Időjárás: </b>" .$weatherArray['weather']['0']['description']. "<br>";
        $weather .="<b>Légnyomás: </b>" .$weatherArray['main']['pressure']."hPA<br>";
        $weather .="<b>Szélerősség: </b>" .$weatherArray['wind']['speed']."meter/sec<br>";
        $weather .="<b>Felhők: </b>" .$weatherArray['clouds']['all']." %<br>";
        date_default_timezone_set('Europe/Budapest');
        $sunrise = $weatherArray['sys']['sunrise'];


        $lat = $weatherArray['coord']['lat'];
        $lon = $weatherArray['coord']['lon'];

        if($lat && $lon){
          $apiXY = file_get_contents("https://api.openweathermap.org/data/2.5/air_pollution?lat=".$lat."&lon=".$lon."&appid=30c75d3c56d7565b2e6d578523ebebd5");
          $weatherArray = json_decode($apiXY, true);
          $pol .="<b>CO : </b>" .$weatherArray['list']['0']['components']['co']."<br>";
          $pol .="<b>O<sub>3</sub> : </b>" .$weatherArray['list']['0']['components']['o3']."<br>";
          $pol .="<b>NO : </b>" .$weatherArray['list']['0']['components']['no']."<br>";
          $pol .="<b>PM<sub>2,5</sub> : </b>" .$weatherArray['list']['0']['components']['pm2_5']."<br>";
    
          $CO = $weatherArray['list']['0']['components']['co'];
          $O3 = $weatherArray['list']['0']['components']['o3'];
          $NO = $weatherArray['list']['0']['components']['no'];
          $PM25 = $weatherArray['list']['0']['components']['pm2_5'];

          $city = $_POST['city'];

          $sql = "INSERT INTO air_pollution_db_cities (Dat, CO, O3, NOO, PM25, City)
                  VALUES (now(), '$CO', '$O3', '$NO', '$PM25', '$city')";
    
          if ($connection->query($sql) === FALSE) {
            echo "Error: " . $sql . "<br>" . $connection->error;
          }
        }
    }else{
      $error = "Nem sikerült, a város nem található";
      die("Hiba: a város nem található.");}
  }/*
  if($lat && $lon){
      $apiXY = file_get_contents("https://api.openweathermap.org/data/2.5/air_pollution?lat=".$lat."&lon=".$lon."&appid=30c75d3c56d7565b2e6d578523ebebd5");
      $weatherArray = json_decode($apiXY, true);
      $pol .="<b>CO : </b>" .$weatherArray['list']['0']['components']['co']."<br>";
      $pol .="<b>O<sub>3</sub> : </b>" .$weatherArray['list']['0']['components']['o3']."<br>";
      $pol .="<b>NO : </b>" .$weatherArray['list']['0']['components']['no']."<br>";
      $pol .="<b>PM<sub>2,5</sub> : </b>" .$weatherArray['list']['0']['components']['pm2_5']."<br>";

      $CO = $weatherArray['list']['0']['components']['co'];
      $O3 = $weatherArray['list']['0']['components']['o3'];
      $NO = $weatherArray['list']['0']['components']['no'];
      $PM25 = $weatherArray['list']['0']['components']['pm2_5'];

  }else{
    $error = "Nem sikerült, a város nem található";
  }*/

  

//Results from the datas

/*			
1. NO >90
2. PM2,5 >110
3. O3 >220
4. CO >10000
*/

//error handling: there is one missing data from the API
/*
$result= $NO * 7 + $PM25 * 6 + $NO2 * 5 + $PM10 * 4 + $SO2 * 3 + $O3 * 2 + $CO;
if ($NO==0) {
  $result = $PM25 * 6 + $NO2 * 5 + $PM10 * 4 + $SO2 * 3 + $O3 * 2 + $CO;
} else if ($PM25==0){
  $result = $NO * 7 + $NO2 * 5 + $PM10 * 4 + $SO2 * 3 + $O3 * 2 + $CO;
} else if ($NO2==0){
  $result = $NO * 7 + $PM25 * 6 + $PM10 * 4 + $SO2 * 3 + $O3 * 2 + $CO;
} else if ($PM10==0){
  $result = $NO * 7 + $PM25 * 6 + $NO2 * 5 + $SO2 * 3 + $O3 * 2 + $CO;
} else if ($SO2==0){
  $result = $NO * 7 + $PM25 * 6 + $NO2 * 5 + $PM10 * 4 + $O3 *2 + $CO;
} else if ($O3==0){
  $result = $NO * 7 + $PM25 * 6 + $NO2 * 5 + $PM10 * 4 + $SO2 * 3 +  $CO;
} else if ($CO==0){
  $result = $NO * 7 + $PM25 * 6 + $NO2 * 5 + $PM10 * 4 + $SO2 * 3 + $O3 * 2;
}
ECHO $result;

switch($result){
  case 0:
    break;
  case $result <= 1000:
    echo "<br>".$_POST['city']." levegőtartalma kitűnő";
    break;
  case $result <= 1600:
    echo "<br>".$_POST['city']." levegőtartalma jó";
    break;
  case $result <= 2200:
    echo "<br>".$_POST['city']." levegőtartalma megfelelő";
    break;
  case $result <= 2800:
    echo "<br>".$_POST['city']." levegőtartalma szennyezett";
    break;
  case $result > 3400:
    echo "<br>".$_POST['city']." levegőtartalma erősen szennyezett";
    break;  
  }
  */
  
/*
    switch ($CO) {
      case 0:
        $COresponse = "CO (Szén-monoxid): Nincs adat <br>";
        echo $COresponse;
        break;  
      case $CO <= 2000:
        $COresponse = "CO (Szén-monoxid): Kiváló <br>";
        echo $COresponse;
        break;  
      case $CO <= 4000:
        $COresponse = "CO (Szén-monoxid): Jó,  " . round(100 - (($CO/5000)*100), 2) . " %-ban Kiváló, ". round(($CO/5000)*100, 2) ." %-ban viszont már Megfelelő<br>";
        echo $COresponse;
        break;
      case $CO <= 5000:
        $COresponse = "CO (Szén-monoxid): Megfelelő,  " . round(100 - (($CO/10000)*100), 2) . " %-ban Jó, ". round(($CO/10000)*100, 2) ." %-ban viszont már Szennyezett<br>";
        echo $COresponse;
        break;
      case $CO <= 10000:
        $COresponse = "CO (Szén-monoxid): Szennyezett,  " . round(100 - (($CO/10000)*100), 2) . " %-ban Megfelelő, ". round(($CO/10000)*100, 2) ." %-ban viszont már Erősen szennyezett<br>";
        echo $COresponse;
        break;          
      case $CO > 10000:
        $CO = "CO (Szén-monoxid): Erősen szennyezett <br>";
        echo $COresponse;
        break;              
    }
    switch ($O3) {
      case 0:
        $O3response = "O3 (Ózon): Nincs adat <br>";
        echo $O3response;
        break;
      case $O3 <= 48:
        $O3response = "O3 (Ózon): Kiváló <br>";
        echo $O3response;
        break;  
      case $O3 <= 96:
        $O3response = "O3 (Ózon): Jó,  " . round(100 - (($O3/120)*100), 2) . " %-ban Kiváló, ". round(($O3/120)*100, 2) ." %-ban viszont már Megfelelő<br>";
        echo $O3response;
        break;
      case $O3 <= 120:
        $O3response = "O3 (Ózon): Megfelelő, " . round(100 - (($O3/220)*100), 2) . " %-ban Jó, ". round(($O3/220)*100, 2) ." %-ban viszont már Szennyezett<br>";
        echo $O3response;
        break;
      case $O3 <= 220:
        $O3response = "O3 (Ózon): Szennyezett, " . round(100 - (($O3/220)*100), 2) . " %-ban Megfelelő, ". round(($O3/220)*100, 2) ." %-ban viszont már Erősen szennyezett<br>";
        echo $O3response;
        break;          
      case $O3 > 220:
        $O3response = "O3 (Ózon): Erősen szennyezett <br>";
        echo $O3response;
        break;              
    }
    switch ($NO) {
      case 0:
        $NOresponse = "NO (Nitrogén-monoxid): Nincs adat <br>";
        echo $NOresponse;
        break;
      case $NO <= 20:
        $NOresponse = "NO (Nitrogén-monoxid): Kiváló <br>";
        echo $NOresponse;
        break;  
      case $NO <= 40:
        $NOresponse = "NO (Nitrogén-monoxid): Jó,  " . round(100 - (($NO/50)*100), 2) . " %-ban Kiváló, ". round(($NO/50)*100, 2) ." %-ban viszont már Megfelelő<br>";
        echo $NOresponse;
        break;
      case $NO <= 50:
        $NOresponse = "NO (Nitrogén-monoxid): Megfelelő, " . round(100 - (($NO/90)*100), 2) . " %-ban Jó, ". round(($NO/90)*100, 2) ." %-ban viszont már Szennyezett<br>";
        echo $NOresponse;
        break;
      case $NO <= 90:
        $NOresponse = "NO (Nitrogén-monoxid): Szennyezett, " . round(100 - (($NO/90)*100), 2) . " %-ban Megfelelő, ". round(($NO/90)*100, 2) ." %-ban viszont már Erősen szennyezett<br>";
        echo $NOresponse;
        break;          
      case $NO > 90:
        $NOresponse = "NO (Nitrogén-monoxid): Erősen szennyezett <br>";
        echo $NOresponse;
        break;              
    }
    switch ($PM25) {
      case 0:
        $PM25response = "PM25 (Szálló por): Nincs adat <br>";
        echo $PM25response;
        break;  
      case $PM25 <= 15:
        $PM25response = "PM25 (Szálló por): Kiváló <br>";
        echo $PM25response;
        break;
      case $PM25 <= 30:
        $PM25response = "PM25 (Szálló por): Jó,  " . round(100 - (($PM25/55)*100), 2) . " %-ban Kiváló, ". round(($PM25/55)*100, 2) ." %-ban viszont már Megfelelő<br>";
        echo $PM25response;
        break;
      case $PM25 <= 55:
        $PM25response = "PM25 (Szálló por): Megfelelő, " . round(100 - (($PM25/110)*100), 2) . " %-ban Jó, ". round(($PM25/110)*100, 2) ." %-ban viszont már Szennyezett<br>";
        echo $PM25response;
        break;
      case $PM25 <= 110:
        $PM25response = "PM25 (Szálló por): Szennyezett, " . round(100 - (($PM25/110)*100), 2) . " %-ban Megfelelő, ". round(($PM25/110)*100, 2) ." %-ban viszont már Erősen szennyezett<br>";
        echo $PM25response;
        break;          
      case $PM25 > 110:
        $PM25response = "PM25 (Szálló por): Erősen szennyezett <br>";
        echo $PM25response;
        break;              
    }
*/
    

/*
      if($SO2 == "SO2 kiváló" && $NO2 == "NO2 kiváló" && $CO == "CO kiváló" && $O3 == "O3 kiváló" && 
          $NO == "NO kiváló" && $PM10 == "PM10 kiváló" && $PM25 == "PM25 kiváló"){
        echo "<br>".$_POST['city']." levegőszintje kiváló";
      }
      if($SO2 == "SO2 jó" && $NO2 == "NO2 jó" && $CO == "CO jó" && $O3 == "O3 jó" && 
          $NO == "NO jó" && $PM10 == "PM10 jó" && $PM25 == "PM25 jó"){
        echo "<br>".$_POST['city']." levegőszintje jó";
      }
      if($SO2 == "SO2 megfelelő" && $NO2 == "NO2 megfelelő" && $CO == "CO megfelelő" && $O3 == "O3 megfelelő" && 
          $NO == "NO megfelelő" && $PM10 == "PM10 megfelelő" && $PM25 == "PM25 megfelelő"){
        echo "<br>".$_POST['city']." levegőszintje megfelelő";
      }
      if($SO2 == "SO2 szennyezett" && $NO2 == "NO2 szennyezett" && $CO == "CO szennyezett" && $O3 == "O3 szennyezett" && 
          $NO == "NO szennyezett" && $PM10 == "PM10 szennyezett" && $PM25 == "PM25 szennyezett"){
        echo "<br>".$_POST['city']." levegőszintje szennyezett";
      }
      if($SO2 == "SO2 erősen szennyezett" && $NO2 == "NO2 erősen szennyezett" && $CO == "CO erősen szennyezett" && $O3 == "O3 erősen szennyezett" && 
        $NO == "NO erősen szennyezett" && $PM10 == "PM10 erősen szennyezett" && $PM25 == "PM25 erősen szennyezett"){
          echo "<br>".$_POST['city']." erősen szennyezett!";
      }
      if($SO2 == "SO2 nincs adat" && $NO2 == "NO2 nincs adat" && $CO == "CO nincs adat" && $O3 == "O3 nincs adat" && 
          $NO == "NO nincs adat" && $PM10 == "PM10 nincs adat" && $PM25 == "PM25 nincs adat"){
        echo "<br>".$_POST['city']." levegőszintje szennyezett";
      }
      if($NO == "NO erősen szennyezett" || $PM25 == "erősen szennyezett" || $NO2 == "CO erősen szennyezett" || $PM10 == "erősen szennyezett" || 
          $SO2 == "NO erősen szennyezett"){
        echo "<br>".$_POST['city']." erősen szennyezett";
      }
*/
    }/*
    if(array_key_exists('Hírek', $_POST)){
      if(!$_POST['city']){
        $error = "Kérem adjon meg egy város nevet";
      }
    if($_POST['city']){
      <a href="index.php">Index Page</a>
    }
    }*/
?>
  <body>
  
    <h1>Légszennyezettségi adatok</h1>
    <table class="center" width="1000" border="5" cellpadding="5" cellspacing="5">
      <colgroup>
        <col span="10" style="background-color: #36486b">
      </colgroup> 
      <tr>
        <th>Levegő minősége</th>
        <th colspan="7">Koncentráció μg/m3 - ban mérve</th>
      </tr>
      <tr>
        <td colspan="1">Mért anyagok</td>
        <td>CO</td>
        <td>O3</td>
        <td>NO</td>
        <td>PM2,5</td>
      </tr>
      <tr>
        <td>Kiváló</td>
        <td>0-2000</td>
        <td>0-48</td>
        <td>0-20</td>
        <td>0-15</td>
      </tr>
      <tr>
        <td>Jó</td>
        <td>2000-4000</td>
        <td>48-96</td>
        <td>20-40</td>
        <td>15-30</td>
      </tr>
      <tr>
        <td>Megfelelő</td>
        <td>4000-5000</td>
        <td>96-120</td>
        <td>40-50</td>
        <td>30-55</td>
      </tr>
      <tr>
        <td>Szennyezett</td>
        <td>5000-10000</td>
        <td>120-220</td>
        <td>50-90</td>
        <td>55-110</td>
      </tr>		
      <tr>
        <td>Erősen szennyezett</td>
        <td>>10000</td>
        <td>>220</td>
        <td>>90</td>
        <td>>110</td>
      </tr>

    <form action = "" method="post">
        <p><label for="city"></label></p>
        <p><input type="text" name="city" id="city" placeholder="Város"></p>
        <button type="submit" name="Idojaras" class="btn" value="Időjárás">Időjárás</button>

        <!-- Air pollution news from a city-->
        <button type="submit" name="Hirek" class="btn" value="Hírek">Hírek</button>
        
        
        <?php
          if(array_key_exists('Hirek', $_POST)){
            if(!$_POST['city']){
              $error = "Kérem adjon meg egy város nevet";
            }
          if($_POST['city']){
            header('Location: https://www.google.com/search?q='.$_POST['city'].'+l%C3%A9gszennyez%C3%A9s&sxsrf=APq-WBsgnW2nvBEgF1Kqf2BfPmQIteyOMA:1648658049903&source=lnms&tbm=nws&sa=X&ved=2ahUKEwiDmK7roe72AhX2_rsIHRhGD6cQ_AUoAnoECAEQBA&biw=1396&bih=656&dpr=1.38');
          }
        }
        ?>

<!--Api Answer-->


    </form>
    <!-- Creating table, showing the results -->
    <table class="center" width="800" border="5" cellpadding="5" cellspacing="5">
      <colgroup>
        <col span="10" style="background-color: #36486b">
      </colgroup>  
      <tr>
        <th>Város</th>
        <th>Dátum</th>
        <th>CO</th>
        <th>O3</th>
        <th>NO</th>
        <th>CPM2.5</th>
      </tr>
      <br>
      <br>
      <div style="padding: 20px; background-color: #36486b; width: 50%; height: 180px; display: inline-block; text-align: center; font-size: 20px">
        <?php if ($weather){echo $weather;}?>
      </div>
      <div style="padding: 50px; background-color: #36486b; width: 50%; height: 180px; display: inline-block; text-align: center; font-size: 20px">
        <?php if ($pol){echo $pol;}?>
      </div>
        <?php if ($error){echo $error;}?>
      <br><br>
      <div style="padding: 50px; background-color: #36486b; width: 100%; height: 250px; text-align: center; font-size: 26px">
        <?php 
          switch ($CO) {
            case 0:
              $COresponse = "CO (Szén-monoxid): Nincs adat <br>";
              echo $COresponse;
              break;  
            case $CO <= 2000:
              $COresponse = "CO (Szén-monoxid): Kiváló <br>";
              echo $COresponse;
              break;  
            case $CO <= 4000:
              $COresponse = "CO (Szén-monoxid): Jó,  " . round(100 - (($CO/5000)*100), 2) . " %-ban Kiváló, ". round(($CO/5000)*100, 2) ." %-ban viszont már Megfelelő<br>";
              echo $COresponse;
              break;
            case $CO <= 5000:
              $COresponse = "CO (Szén-monoxid): Megfelelő,  " . round(100 - (($CO/10000)*100), 2) . " %-ban Jó, ". round(($CO/10000)*100, 2) ." %-ban viszont már Szennyezett<br>";
              echo $COresponse;
              break;
            case $CO <= 10000:
              $COresponse = "CO (Szén-monoxid): Szennyezett,  " . round(100 - (($CO/10000)*100), 2) . " %-ban Megfelelő, ". round(($CO/10000)*100, 2) ." %-ban viszont már Erősen szennyezett<br>";
              echo $COresponse;
              break;          
            case $CO > 10000:
              $CO = "CO (Szén-monoxid): Erősen szennyezett <br>";
              echo $COresponse;
              break;              
          }
          switch ($O3) {
            case 0:
              $O3response = "O3 (Ózon): Nincs adat <br>";
              echo $O3response;
              break;
            case $O3 <= 48:
              $O3response = "O3 (Ózon): Kiváló <br>";
              echo $O3response;
              break;  
            case $O3 <= 96:
              $O3response = "O3 (Ózon): Jó,  " . round(100 - (($O3/120)*100), 2) . " %-ban Kiváló, ". round(($O3/120)*100, 2) ." %-ban viszont már Megfelelő<br>";
              echo $O3response;
              break;
            case $O3 <= 120:
              $O3response = "O3 (Ózon): Megfelelő, " . round(100 - (($O3/220)*100), 2) . " %-ban Jó, ". round(($O3/220)*100, 2) ." %-ban viszont már Szennyezett<br>";
              echo $O3response;
              break;
            case $O3 <= 220:
              $O3response = "O3 (Ózon): Szennyezett, " . round(100 - (($O3/220)*100), 2) . " %-ban Megfelelő, ". round(($O3/220)*100, 2) ." %-ban viszont már Erősen szennyezett<br>";
              echo $O3response;
              break;          
            case $O3 > 220:
              $O3response = "O3 (Ózon): Erősen szennyezett <br>";
              echo $O3response;
              break;              
          }
          switch ($NO) {
            case 0:
              $NOresponse = "NO (Nitrogén-monoxid): Nincs adat <br>";
              echo $NOresponse;
              break;
            case $NO <= 20:
              $NOresponse = "NO (Nitrogén-monoxid): Kiváló <br>";
              echo $NOresponse;
              break;  
            case $NO <= 40:
              $NOresponse = "NO (Nitrogén-monoxid): Jó,  " . round(100 - (($NO/50)*100), 2) . " %-ban Kiváló, ". round(($NO/50)*100, 2) ." %-ban viszont már Megfelelő<br>";
              echo $NOresponse;
              break;
            case $NO <= 50:
              $NOresponse = "NO (Nitrogén-monoxid): Megfelelő, " . round(100 - (($NO/90)*100), 2) . " %-ban Jó, ". round(($NO/90)*100, 2) ." %-ban viszont már Szennyezett<br>";
              echo $NOresponse;
              break;
            case $NO <= 90:
              $NOresponse = "NO (Nitrogén-monoxid): Szennyezett, " . round(100 - (($NO/90)*100), 2) . " %-ban Megfelelő, ". round(($NO/90)*100, 2) ." %-ban viszont már Erősen szennyezett<br>";
              echo $NOresponse;
              break;          
            case $NO > 90:
              $NOresponse = "NO (Nitrogén-monoxid): Erősen szennyezett <br>";
              echo $NOresponse;
              break;              
          }
          switch ($PM25) {
            case 0:
              $PM25response = "PM25 (Szálló por): Nincs adat <br>";
              echo $PM25response;
              break;  
            case $PM25 <= 15:
              $PM25response = "PM25 (Szálló por): Kiváló <br>";
              echo $PM25response;
              break;
            case $PM25 <= 30:
              $PM25response = "PM25 (Szálló por): Jó,  " . round(100 - (($PM25/55)*100), 2) . " %-ban Kiváló, ". round(($PM25/55)*100, 2) ." %-ban viszont már Megfelelő<br>";
              echo $PM25response;
              break;
            case $PM25 <= 55:
              $PM25response = "PM25 (Szálló por): Megfelelő, " . round(100 - (($PM25/110)*100), 2) . " %-ban Jó, ". round(($PM25/110)*100, 2) ." %-ban viszont már Szennyezett<br>";
              echo $PM25response;
              break;
            case $PM25 <= 110:
              $PM25response = "PM25 (Szálló por): Szennyezett, " . round(100 - (($PM25/110)*100), 2) . " %-ban Megfelelő, ". round(($PM25/110)*100, 2) ." %-ban viszont már Erősen szennyezett<br>";
              echo $PM25response;
              break;          
            case $PM25 > 110:
              $PM25response = "PM25 (Szálló por): Erősen szennyezett <br>";
              echo $PM25response;
              break;              
          }
        ?>
      </div>
      <br>
      <br>

      
        <?php
          $sql = "select * from air_pollution_db_cities where city = '$city'"; 
          $FromDB = mysqli_query($connection,$sql);
          while($data = mysqli_fetch_assoc($FromDB)){
            echo "<tr>";
            echo "<th>".$data['City']."</th>";
            echo "<th>".$data['Dat']."</th>";
            echo "<th>".$data['CO']." ug/m3</th>";
            echo "<th>".$data['O3']." ug/m3</th>";
            echo "<th>".$data['NOO']." ug/m3</th>";
            echo "<th>".$data['PM25']." ug/m3</th>";
            echo "</tr>";
          }          
        ?>

<!--Charts-->

<!--LINE-->

		<style type="text/css">
      .highcharts-figure,
      .highcharts-data-table table {
          min-width: 360px;
          max-width: 700px;
          margin: 1em auto;
      }

      .highcharts-data-table table {
          font-family: Verdana, sans-serif;
          border-collapse: collapse;
          border: 1px solid #ebebeb;
          margin: 10px auto;
          text-align: center;
          width: 100%;
          max-width: 300px;
      }

      .highcharts-data-table caption {
          padding: 1em 0;
          font-size: 1.2em;
          color: #555;
      }

      .highcharts-data-table th {
          font-weight: 600;
          padding: 0.5em;
      }

      .highcharts-data-table td,
      .highcharts-data-table th,
      .highcharts-data-table caption {
          padding: 0.5em;
      }

      .highcharts-data-table thead tr,
      .highcharts-data-table tr:nth-child(even) {
          background: #f8f8f8;
      }

      .highcharts-data-table tr:hover {
          background: #f1f7ff;
      }

		</style>

    <script src="code/highcharts.js"></script>
    <script src="code/modules/series-label.js"></script>
    <script src="code/modules/exporting.js"></script>
    <script src="code/modules/export-data.js"></script>
    <script src="code/modules/accessibility.js"></script>

    <figure class="highcharts-figure">
        <div id="line"></div>
    </figure>

    <?php 

    $sql = "select * from air_pollution_db_cities where city = '$city'"; 
    $result = mysqli_query($connection, $sql);
    $O3Chart = '';
    $COChart = '';
    $NOOChart = '';
    $PM25Chart = '';

    while($row = mysqli_fetch_array($result))
    {
    $O3Chart .= "".$row["O3"].", ";
    $COChart .= "".$row["CO"].", ";
    $NOOChart .= "".$row["NOO"].", ";
    $PM25Chart .= "".$row["PM25"].", ";
    }

    $O3Chart = substr($O3Chart, 0, -2);
    $COChart = substr($COChart, 0, -2);
    $NOOChart = substr($NOOChart, 0, -2);
    $PM25Chart = substr($PM25Chart, 0, -2);
    ?>

        <script type="text/javascript">
          Highcharts.chart('line', {

              title: {
                  text: '<?php echo ucfirst($city)?> káros anyagainak mennyisége'
              },

              subtitle: {
                  text: 'Vonal diagram'
              },

              yAxis: {
                  title: {
                      text: 'Mért mennyiség mikrogrammban (ug/m3)'
                  }
              },

              xAxis: {
                  accessibility: {
                      rangeDescription: 'Lekérdezések száma'
                  }
              },

              legend: {
                  layout: 'vertical',
                  align: 'right',
                  verticalAlign: 'middle'
              },

              plotOptions: {
                  series: {
                      label: {
                          connectorAllowed: false
                      },
                      pointStart: 0
                  }
              },

              series: [{
                  name: 'O3',
                  data: [<?PHP ECHO $O3Chart?>]
              }, {
                  name: 'NO',
                  data: [<?PHP ECHO $NOOChart?>]
              }, {
                  name: 'CO',
                  data: [<?PHP ECHO $COChart?>]
              }, {
                  name: 'PM25',
                  data: [<?PHP ECHO $PM25Chart?>]
              }],

              responsive: {
                  rules: [{
                      condition: {
                          maxWidth: 300
                      },
                      chartOptions: {
                          legend: {
                              layout: 'horizontal',
                              align: 'left',
                              verticalAlign: 'bottom'
                          }
                      }
                  }]
              }

          });
        </script>

<!--Column-->


		<style type="text/css">
      .highcharts-figure,
      .highcharts-data-table table {
          min-width: 310px;
          max-width: 700px;
          margin: 1em auto;
      }

      #container {
          height: 400px;
      }

      .highcharts-data-table table {
          font-family: Verdana, sans-serif;
          border-collapse: collapse;
          border: 1px solid #ebebeb;
          margin: 10px auto;
          text-align: center;
          width: 100%;
          max-width: 500px;
      }

      .highcharts-data-table caption {
          padding: 1em 0;
          font-size: 1.2em;
          color: #555;
      }

      .highcharts-data-table th {
          font-weight: 600;
          padding: 0.5em;
      }

      .highcharts-data-table td,
      .highcharts-data-table th,
      .highcharts-data-table caption {
          padding: 0.5em;
      }

      .highcharts-data-table thead tr,
      .highcharts-data-table tr:nth-child(even) {
          background: #f8f8f8;
      }

      .highcharts-data-table tr:hover {
          background: #f1f7ff;
      }

		</style>

  <figure class="highcharts-figure">
      <div id="column"></div>
  </figure>



      <script type="text/javascript">
  Highcharts.chart('column', {
      chart: {
          type: 'column'
      },
      title: {
          text: '<?php echo ucfirst($city)?> káros anyagainak mennyisége'
      },
      subtitle: {
          text: 'Oszlop diagram'
      },
      xAxis: {
                  accessibility: {
                      rangeDescription: 'Lekérdezések száma'
                  }
      },
      yAxis: {
          min: 0,
          title: {
              text: 'Mért mennyiség mikrogrammban (ug/m3)'
          }
      },
      tooltip: {
          headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
          pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
              '<td style="padding:0"><b>{point.y:.1f} mm</b></td></tr>',
          footerFormat: '</table>',
          shared: true,
          useHTML: true
      },
      plotOptions: {
          column: {
              pointPadding: 0.2,
              borderWidth: 0
          }
      },
      series: [{
                  name: 'O3',
                  data: [<?PHP ECHO $O3Chart?>]
              }, {
                  name: 'NO',
                  data: [<?PHP ECHO $NOOChart?>]
              }, {
                  name: 'CO',
                  data: [<?PHP ECHO $COChart?>]
              }, {
                  name: 'PM25',
                  data: [<?PHP ECHO $PM25Chart?>]
              }],
  });
      </script>
  </body>
</html>