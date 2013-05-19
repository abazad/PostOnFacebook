<?php
// Remember to copy files from the SDK's src/ directory to a
// directory in your application on the server, such as php-sdk/
require_once('facebook.php');

// Here you need to put your post's data in:
$attachment = array(
        'message' => 'Test Post Message',
        'name' => 'TestPost',
        'link' => 'http://www.google.de',
        'description' => 'Test Post Description',
);

$config = array(
  'appId' => '458758440879962',
  'secret' => 'db98c0abc9732981a0aebc3f32d8dc85',
);

// Don't make any changes for configuration
// Below this line!
// ========================================

$facebook = new Facebook($config);
$user_id = $facebook->getUser();
?>
<!doctype html>
<html xmlns:fb="http://www.facebook.com/2008/fbml">
  <head>
    <title>Post On Facebook Testpage</title>
    <style>
      body {
        font-family: 'Lucida Grande', Verdana, Arial, sans-serif;
      }
      h1 a {
        text-decoration: none;
        color: #3b5998;
      }
      h1 a:hover {
        text-decoration: underline;
      }
    </style>
  </head>
  <body>

  <?php
    if($user_id) {

      // We have a user ID, so probably a logged in user.
      // If not, we'll get an exception, which we handle below.
      try {
        //get pages or applications where you are administrator
        $accounts = $facebook->api('/me/accounts');

        //page where i want to post
        $page_id = '166719733495940';

        foreach($accounts['data'] as $account)
        {
           if($account['id'] == $page_id)
           {
              $token = $account['access_token'];
              $attachment['access_token'] = $token;
           }
        }

        try{
          $res = $facebook->api('/'.$page_id.'/feed','POST',$attachment);

        } catch (Exception $e){

            echo $e->getMessage();
        }  

        echo '<pre>Post ID: ' . $res['id'] . '</pre>';

        // Give the user a logout link 
        echo '<br /><a href="' . $facebook->getLogoutUrl() . '">logout</a>';
      } catch(FacebookApiException $e) {
        // If the user is logged out, you can have a 
        // user ID even though the access token is invalid.
        // In this case, we'll get an exception, so we'll
        // just ask the user to login again here.
        $login_url = $facebook->getLoginUrl( array(
                       'scope' => 'manage_pages,offline_access,publish_stream'
                       )); 
        echo 'Please <a href="' . $login_url . '">login.</a>';
        echo '<pre>Error-Type: ' . $e->getType() . '</pre>';
        echo '<pre>Error-Msg : ' . $e->getMessage() . '</pre>';
      }   
    } else {

      // No user, so print a link for the user to login
      // To post to a user's wall, we need publish_stream permission
      // We'll use the current URL as the redirect_uri, so we don't
      // need to specify it here.
      $login_url = $facebook->getLoginUrl( array( 'scope' => 'manage_pages,offline_access,publish_stream' ) );
      echo 'Please <a href="' . $login_url . '">login.</a>';

    } 

  ?>      

  </body> 
</html>  