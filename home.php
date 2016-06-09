<!--  $redirectUri = 'http://localhost/off365/php/authorize.php';-->
<?php
  session_start();
  require('oauth.php');
  require('outlook.php');

  $loggedIn = !is_null($_SESSION['access_token']);
  //$redirectUri = 'http://localhost/php-tutorial/authorize.php';
  $redirectUri = 'http://localhost/off365/php/authorize.php';
?>

<html>
    <head>
      <title>PHP Mail API Tutorial</title>
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
        $messages = OutlookService::getMessages($_SESSION['access_token'], $_SESSION['user_email']);
    ?>
      <!-- User is logged in, do something here -->
      <h2>Your messages</h2>

      <table>
        <tr>
          <th>From</th>
          <th>Subject</th>
          <th>Received</th>
        </tr>

        <?php foreach($messages['value'] as $message) { ?>
          <tr>
            <td><?php echo $message['From']['EmailAddress']['Name'] ?></td>
            <td><?php echo $message['Subject'] ?></td>
            <td><?php echo $message['ReceivedDateTime'] ?></td>
          </tr>
        <?php } ?>
      </table>
    <?php    
      }
    ?>
  </body>
</html>
