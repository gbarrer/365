<?php
  class OutlookService {
    public static function makeApiCall($access_token, $user_email, $method, $url, $payload = NULL) {
      // Generate the list of headers to always send.
      $headers = array(
        "User-Agent: php-tutorial/1.0",         // Sending a User-Agent header is a best practice.
        "Authorization: Bearer ".$access_token, // Always need our auth token!
        "Accept: application/json",             // Always accept JSON response.
        "client-request-id: ".self::makeGuid(), // Stamp each new request with a new GUID.
        "return-client-request-id: true",       // Tell the server to include our request-id GUID in the response.
        "X-AnchorMailbox: ".$user_email         // Provider user's email to optimize routing of API call
      );

      $curl = curl_init($url);
//asi lo esta mandando por error
//"https://outlook.office.com/api/v2.0/Me/Events/?%24select=Subject%2cStart%2cEnd&startdatetime=2016-05-31T00%3a00%3a00Z&enddatetime=2016-06-03T00%3a00%3a00Z&%24orderby=Start%2fDateTime+DESC&%24top=8&%24skip=8"}
echo "<br>GBH".$url;
/*
$urlok = str_replace("%3A", ":", $url);
$url=$urlok;
echo "<br>GBH2".$url;
*/

      switch(strtoupper($method)) {
        case "GET":
          // Nothing to do, GET is the default and needs no
          // extra headers.
          error_log("Doing GET");
          break;
        case "POST":
          error_log("Doing POST");
          // Add a Content-Type header (IMPORTANT!)
          $headers[] = "Content-Type: application/json";
          curl_setopt($curl, CURLOPT_POST, true);
          curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
          break;
        case "PATCH":
          error_log("Doing PATCH");
          // Add a Content-Type header (IMPORTANT!)
          $headers[] = "Content-Type: application/json";
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PATCH");
          curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
          break;
        case "DELETE":
          error_log("Doing DELETE");
          curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
          break;
        default:
          error_log("INVALID METHOD: ".$method);
          exit;
      }

      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
      $response = curl_exec($curl);
      error_log("curl_exec done.");

      $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
      error_log("Request returned status ".$httpCode);
      if ($httpCode >= 400) {
        return array('errorNumber' => $httpCode,
                     'error' => 'Request returned HTTP error '.$httpCode);
      }

      $curl_errno = curl_errno($curl);
      $curl_err = curl_error($curl);
      if ($curl_errno) {
        $msg = $curl_errno.": ".$curl_err;
        error_log("CURL returned an error: ".$msg);
        curl_close($curl);
        return array('errorNumber' => $curl_errno,
                     'error' => $msg);
      }
      else {
        error_log("Response: ".$response);
        curl_close($curl);
        return json_decode($response, true);
      }
    }

    // This function generates a random GUID.
    public static function makeGuid(){
      if (function_exists('com_create_guid')) {
        error_log("Using 'com_create_guid'.");
        return strtolower(trim(com_create_guid(), '{}'));
      }
      else {
        error_log("Using custom GUID code.");
        $charid = strtolower(md5(uniqid(rand(), true)));
        $hyphen = chr(45);
        $uuid = substr($charid, 0, 8).$hyphen
                .substr($charid, 8, 4).$hyphen
                .substr($charid, 12, 4).$hyphen
                .substr($charid, 16, 4).$hyphen
                .substr($charid, 20, 12);

        return $uuid;
      }
    }



private static $outlookApiUrl = "https://outlook.office.com/api/v2.0";
    
public static function getMessages($access_token, $user_email) {
  $getMessagesParameters = array (
    // Only return Subject, ReceivedDateTime, and From fields
    "\$select" => "Subject,ReceivedDateTime,From",
    // Sort by ReceivedDateTime, newest first
    "\$orderby" => "ReceivedDateTime DESC",
    // Return at most 10 results
    "\$top" => "10"
  );

  $getMessagesUrl = self::$outlookApiUrl."/Me/Messages?".http_build_query($getMessagesParameters);
                        
  return self::makeApiCall($access_token, $user_email, "GET", $getMessagesUrl);
}


public static function getEvents($access_token, $user_email, $start, $end) {
//$a=urlencode("2015-10-01T01:00:00Z");
//$b=urlencode("2015-10-02T01:00:00Z");

  $getEventsParameters = array (
    // Only return Subject, Start, and End fields
    "\$select" => "Subject,Start,End",

//        "\$select" => "Subject,Start,End,Location,Attendees,Organizer",
    // Only return betweet range
    //"\$startDateTime" => "2015-10-01T01:00:00",
    //"\$startdatetime" => "2015-10-01T01:00:00",
    //https://oauthplay.azurewebsites.net
    //?startdatetime=2016-05-06T00:00:00Z&enddatetime=2016-05-13T00:00:00Z
    //"\$startdatetime" => "2015-10-01T01:00:00Z",
    //"\$enddatetime"   => "2015-10-02T01:00:00Z",

    ///Me/Events?$filter=Start ge 2014-08-28T21:00:00Z
    //"\$filter" => "Start ge 2015-10-01T01:00:00Z",

//    "\$startdatetime" => $a,
//    "\$enddatetime"   => $b,

//funciona OK
//    "startdatetime" => "2016-05-31T00:00:00Z",
//    "enddatetime" => "2016-06-03T00:00:00Z",

    "startdatetime" => $start,
    "enddatetime" => $end,


//    "\$startdatetime" => "2016-05-31T00:00:00Z",
//    "\$enddatetime" => "2016-06-03T00:00:00Z",

//    "\$startdatetime" => "2016%2D05%2D31T00%2D00%2D00Z",
//    "\$enddatetime" =>   "2016%2D06%2D03T00%2D00%2D00Z",


    // Sort by Start, oldest first
    "\$orderby" => "Start/DateTime DESC",
    // Return at most 10 results
    "\$top" => "30"
  );

  //este metodo no me funciona bien     
  //$getEventsUrl = self::$outlookApiUrl."/Me/Events?".http_build_query($getEventsParameters);
  $getEventsUrl = self::$outlookApiUrl."/Me/calendarview?".http_build_query($getEventsParameters);

                      
  //return self::makeApiCall($access_token, $user_email, "GET", "\"".$getEventsUrl."\"");
  //return self::makeApiCall($access_token, $user_email, "GET", urlencode($getEventsUrl));
  //echo self::makeApiCall($access_token, $user_email, "GET", $getEventsUrl);
  return self::makeApiCall($access_token, $user_email, "GET", $getEventsUrl); 

}


public static function getEventsFile($access_token, $user_email/*, $fromd, $tod*/) {
//$results = exec('sh /var/www/off365/php/curlCalendar6.sh $access_token', $output);
/*if ($results){
echo "yay!";
    var_dump($output);
    echo $results;
} else {
    var_dump($output);
    echo "screw you";   
}*/
  //arroja salida a archivo
 $file = file_get_contents('./salida.txt', FILE_USE_INCLUDE_PATH);
  //echo $access_token;
//  $file = file_get_contents('/tmp/salida.txt', FILE_USE_INCLUDE_PATH);

  // echo $file;
  //tal como lo hace la fucnion make api call
  return json_decode($file, true);

}


  }
?>
