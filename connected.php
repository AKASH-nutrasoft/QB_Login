<?php
//please do more research on below 
//https://www.tiktok.com/@ansshasha_/video/7366607798127348999?q=%23china&t=1736889085371
//https://www.tiktok.com/@travelih/video/7250379954137910554?q=%23china&t=1736889085371
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


// CUT THIS PART OUT AND PUT IT IN A BRAND NEW PAGE
// echo "Before session token check in index</br></br>";
//set the access token using the auth object
if (isset($_SESSION['sessionAccessToken'])) {
    // echo "Entered session token in index</br></br>";
    $accessToken = $_SESSION['sessionAccessToken'];
    $accessTokenJson = array('token_type' => 'bearer',
        'access_token' => $accessToken->getAccessToken(),
        'refresh_token' => $accessToken->getRefreshToken(),
        'x_refresh_token_expires_in' => $accessToken->getRefreshTokenExpiresAt(),
        'expires_in' => $accessToken->getAccessTokenExpiresAt()
    );
    // Storing info in Database
    connect_db_customer($accessToken->getAccessToken(), $accessToken->getAccessTokenExpiresAt(), $accessToken->getRefreshToken(), $accessToken->getRefreshTokenExpiresAt());
    $dataService->updateOAuth2Token($accessToken);
    $oauthLoginHelper = $dataService -> getOAuth2LoginHelper();
}
// echo "After the session token in index</br></br>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connection Successful</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #56ab2f, #a8e063);
            color: #fff;
        }

        .success-container {
            text-align: center;
            background: #fff;
            color: #333;
            padding: 40px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 400px;
            width: 90%;
        }

        .icon-wrapper {
            margin: 0 auto 20px;
            width: 80px;
            height: 80px;
        }

        .checkmark-circle {
            width: 80px;
            height: 80px;
            background: #4CAF50;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .checkmark {
            width: 20px;
            height: 40px;
            border: solid #fff;
            border-width: 0 4px 4px 0;
            transform: rotate(45deg);
            position: absolute;
            top: 20%;
            left: 35%;
            animation: checkmark-draw 0.6s ease-in-out forwards;
        }

        h1 {
            font-size: 24px;
            margin: 10px 0;
        }

        p {
            font-size: 16px;
            margin: 10px 0 20px;
            line-height: 1.5;
        }

        .button {
            text-decoration: none;
            padding: 10px 20px;
            background: #4CAF50;
            color: #fff;
            border-radius: 5px;
            transition: background 0.3s ease;
            display: inline-block;
        }

        .button:hover {
            background: #45a049;
        }

        @keyframes checkmark-draw {
            from {
                stroke-dashoffset: 60;
            }
            to {
                stroke-dashoffset: 0;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="icon-wrapper">
            <div class="checkmark-circle">
                <div class="checkmark"></div>
            </div>
        </div>
        <h1>Connection Successful!</h1>
        <p>You have successfully completed the connection to quickbooks. You can now close this tab now.</p>
        <a href="#" onclick="showSuccessMessage()" class="btn btn-success connect-button">Show Customers</a>
        <!-- <a href="#" class="button">Go to Dashboard</a> -->
    </div>

    <script>
        // URL for OAuth2 login
        var url = '<?php echo $authUrl; ?>';

        var OAuthCode = function(url) {
            this.loginPopup = function () {
                this.showSuccessMessage();
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
            window.location.href = "https://8687-207-164-59-226.ngrok-free.app/QB_Customers/index.php?license=" + license;
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

