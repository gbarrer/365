<?php
  session_start();
  require_once('oauth.php');
  $auth_code = $_GET['code'];
  $redirectUri = 'http://localhost/off365/php/authorize.php';

  $tokens = oAuthService::getTokenFromAuthCode($auth_code, $redirectUri);


    if ($tokens['access_token']) {
    $_SESSION['access_token'] = $tokens['access_token'];

    // Get the user's email from the ID token
	$user_email = oAuthService::getUserEmailFromIdToken($tokens['id_token']);
	$_SESSION['user_email'] = $user_email;

    // Redirect back to home page
    header("Location: http://localhost/off365/php/home.php");
  }
  else
  {
    echo "<p>ERROR: ".$tokens['error']."</p>";
  }
?>
