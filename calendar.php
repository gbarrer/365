<head>
<meta http-equiv="content-type" content="text/plain; charset=utf-8">
</head>

<?php
  session_start();
  require('oauth.php');
  require('outlook.php');

  require 'class.iCalReader.php';
  $ical   = new ICal('MyCal.ics');
  
  $loggedIn = !is_null($_SESSION['access_token']);
  $redirectUri = 'http://localhost/off365/php/authorize.php';

function convertNumberToDay($number) {
//    $days = array(/*'Sunday','Monday',*/ '', 'Martes','Miércoles','Martes','Jueves','','');
  $days = array(/*'Sunday','Monday',*/ 'Lunes', 'Martes','Miércoles','Jueves','Viernes','Sabado','Domingo');

    return $days[$number-1]; 
}

function dateTimeToTimestamp ($dato,$ical){
             //echo "procesando..".$dato;
             $dato2limpio=str_replace("-","",$dato);
             $dato2limpio=str_replace(":","",$dato2limpio);      
             //$dato2limpio=str_replace("T","",$dato2limpio);  
             $dato2limpio=str_replace(".0000000","",$dato2limpio);
             //echo $dato2limpio;
             $timestampf = $ical->iCalDateToUnixTimestamp($dato2limpio);
             return $timestampf;

}

?>
<html>
  <head>
    <title>PHP Calendar API Tutorial</title>
  </head>
  <body>
    <?php 
      if (!$loggedIn) {
    ?>
      <!-- User not logged in, prompt for login -->
      <p>Please <a href="<?php echo oAuthService::getLoginUrl($redirectUri)?>">sign in</a> with your Office 365 or Outlook.com account.</p>
    <?php
      }
      else {
//        echo $_SESSION['access_token'];
        $events = OutlookService::getEvents($_SESSION['access_token'], $_SESSION['user_email'],$_GET['start'],$_GET['end']);
        //$events = OutlookService::getEventsFile($_SESSION['access_token'], $_SESSION['user_email']);
    ?>
      <!-- User is logged in, do something here -->
      <h2>Your events</h2>

<?php

    $sumtot=0;
    $sumday=0;
    $lastday="";

?>      
      <table>
        <tr>
          <th>Subject</th>
          <th>Start</th>
<!--         <th>End</th>-->
          <th>Duration</th>
          <th>Day</th>
        </tr>
        
        <?php foreach($events['value'] as $event) { 
             //Start":{"DateTime":"2007-06-04T19:00:00.0000000","TimeZone":"UTC"},"End":{"DateTime":"2007-06-04T21:00:00.0000000","TimeZone":"UTC"}}
             //echo $event;
$dato1=$event['Start'];
$dato2=$event['End'];
             $timestampf = dateTimeToTimestamp($dato2['DateTime'],$ical);
             $timestampi = dateTimeToTimestamp($dato1['DateTime'],$ical);
             $weekday = date('N', $timestampi); // 1-7
             $duration = ($timestampf - $timestampi)/3600;

	     if ( !($lastday == $weekday) ){
        	 //evaluating a different day
	         echo "<tr><td><strong>Total Horas "./*$lastday.*/" ".$sumday."</strong></td></tr><tr><td><hr></td></tr>";
       		 $sumday=0;
	         $lastday=$weekday;
          }

          ?>
          <tr>
            <td><?php echo $event['Subject'] ?></td>
            <td><?php echo $dato1['DateTime'] ?></td>
<!--            <td><?php echo $dato2['DateTime'] ?></td>-->
<td>
<?php
             echo ($timestampf - $timestampi)/3600," hrs";
?>
</td>
<td>
<?php
             echo  convertNumberToDay($weekday);

?>

</td>
          </tr>
        <?php  
	   $sum+=$duration;
	   $sumday+=$duration;
          } ?>
      </table>
    <?php    
      }
    ?>
  </body>
</html>
