<?php

require('vendor/autoload.php');
require_once "functions.php";

use QuickBooksOnline\API\DataService\DataService;

$config = include('config.php');

session_start();

$dataService = DataService::Configure(array(
    'auth_mode' => 'oauth2',
    'ClientID' => $config['client_id'],
    'ClientSecret' =>  $config['client_secret'],
    'RedirectURI' => $config['oauth_redirect_uri'],
    'scope' => $config['oauth_scope'],
    'baseUrl' => "development"
));

$OAuth2LoginHelper = $dataService->getOAuth2LoginHelper();
$authUrl = $OAuth2LoginHelper->getAuthorizationCodeURL();


// Store the url in PHP Session Object;
$_SESSION['authUrl'] = $authUrl;


// // CUT THIS PART OUT AND PUT IT IN A BRAND NEW PAGE
// echo "Before session token check in index</br></br>";
// //set the access token using the auth object
// if (isset($_SESSION['sessionAccessToken'])) {
//     echo "Entered session token in index</br></br>";
//     $accessToken = $_SESSION['sessionAccessToken'];
//     $accessTokenJson = array('token_type' => 'bearer',
//         'access_token' => $accessToken->getAccessToken(),
//         'refresh_token' => $accessToken->getRefreshToken(),
//         'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
//         'expires_in' => $accessToken->getAccessTokenExpiresAt()
//     );
//     // Storing info in Database
//     connect_db_customer($accessToken->getAccessToken(), $accessToken->getAccessTokenExpiresAt(), $accessToken->getRefreshToken(), $accessToken->getRefreshTokenExpiresAt());
//     $dataService->updateOAuth2Token($accessToken);
//     $oauthLoginHelper = $dataService -> getOAuth2LoginHelper();
// }
// echo "After the session token in index</br></br>";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect to QuickBooks</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }

        .connect-container {
            text-align: center;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .connect-button {
            margin-top: 20px;
        }

        .connect-logo {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .card {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 20px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            transition: 0.3s;
            padding: 50px 100px 100px 100px;
            background-color: #fff;
        }

        .card:hover {
            box-shadow: 0 8px 16px 0 rgba(0, 0, 0, 0.2);
        }

        .text-success {
            color: #28a745; /* Bootstrap's success green */
        }

        .message {
            font-size: 22px; 
            font-weight: 800;
        }

        .text {
            font-size: 15px; 
            font-weight: 550;
        }

        .btn-primary {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="connect-container">
        <img src="https://plugin.intuitcdn.net/sbg-web-shell-ui/6.3.0/shell/harmony/images/QBOlogo.png" alt="QuickBooks Logo" class="connect-logo">
        <h2>Connect to QuickBooks</h2>
        <a href="#" onclick="oauth.loginPopup()" class="btn btn-success connect-button">Connect to QuickBooks</a>
    </div>

    <script>
        // URL for OAuth2 login
        var url = '<?php echo $authUrl; ?>';

        var OAuthCode = function(url) {
            this.loginPopup = function () {
                this.loginPopupUri();
            };

            this.loginPopupUri = function () {
                // Open popup for OAuth login
                var parameters = "location=1,width=800,height=650";
                parameters += ",left=" + (screen.width - 800) / 2 + ",top=" + (screen.height - 650) / 2;

                var win = window.open(url, 'connectPopup', parameters);
                var pollOAuth = window.setInterval(function () {
                    try {
                        // Check if the OAuth2 code is in the URL
                        if (win.document.URL.indexOf("code") != -1) {
                            window.clearInterval(pollOAuth);
                            win.close();
                            showSuccessMessage(); // Show success message
                        }

                    } catch (e) {
                        console.log(e);
                    }
                }, 100);
            };
        };


        // Function to display success message
        function showSuccessMessage() {
            //const container = document.querySelector('.connect-container');
            var license = '<?php echo isset($_GET['license']) ? ($_GET['license']) : ''; ?>';
            window.location.href = "https://8687-207-164-59-226.ngrok-free.app/QB_Connector/connected.php?license=" + license;
            // container.innerHTML = `
            //     <div class="card">
            //         <p class="text-success"><i class="fa fa-check-circle fa-5x"></i></p>
            //         <p class="message">Connected Successfully!</p>
            //         <p class="text">You are now connected to QuickBooks.</p>
            //         <a href="/" class="btn btn-primary">Back to Dashboard</a>
            //     </div>
            // `;
        }

        // Initialize OAuthCode object
        var oauth = new OAuthCode(url);
    </script>
</body>
</html>
