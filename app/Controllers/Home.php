<?php

namespace App\Controllers;

use Google_Client;
use Google_Service_Oauth2;

class Home extends BaseController
{
	public function index()
	{
		$this->session->destroy();
		// var_dump($this->session->access_token);
		// $google_client = new Google_Client();
		// //Set the OAuth 2.0 Client ID
		// $google_client->setClientId('261294496538-n2i66pjniqa04jo6o5hncn25n1u10vl3.apps.googleusercontent.com');
		// //Set the OAuth 2.0 Client Secret key
		// $google_client->setClientSecret('plD8Qv7lfX82lo6lwQ1YhIwv');
		// //Set the OAuth 2.0 Redirect URI
		// $google_client->setRedirectUri("http://localhost");
		// $google_client->setAccessType("offline");
		// $google_client->setApprovalPrompt('force');

		// $google_client->addScope('email');
		// $google_client->addScope('profile');
		// $google_client->setAccessToken($this->session->access_token);
		// if ($google_client->isAccessTokenExpired()) {

		// 	// save refresh token to some variable
		// 	$refreshTokenSaved = $google_client->getRefreshToken();
		// 	// update access token
		// 	$google_client->fetchAccessTokenWithRefreshToken($refreshTokenSaved);

		// 	// pass access token to some variable
		// 	$accessTokenUpdated = $google_client->getAccessToken();

		// 	// append refresh token
		// 	$accessTokenUpdated['refresh_token'] = $refreshTokenSaved;

		// 	//Set the new acces token
		// 	$accessToken = $refreshTokenSaved;
		// 	$google_client->setAccessToken($accessToken);
		// }
		// $google_service = new Google_Service_Oauth2($google_client);
		// $data = $google_service->userinfo->get();
		// var_dump($data);
		// die();
		if (($_GET['with'] ?? "") == 'facebook') {
		} else {
			$google_client = new Google_Client();
			//Set the OAuth 2.0 Client ID
			$google_client->setClientId('261294496538-n2i66pjniqa04jo6o5hncn25n1u10vl3.apps.googleusercontent.com');
			//Set the OAuth 2.0 Client Secret key
			$google_client->setClientSecret('plD8Qv7lfX82lo6lwQ1YhIwv');
			//Set the OAuth 2.0 Redirect URI
			$google_client->setRedirectUri("http://localhost");
			$google_client->setAccessType("offline");
			$google_client->setApprovalPrompt('force');

			$google_client->addScope('email');
			$google_client->addScope('profile');
			if (isset($_GET["code"])) {
				$token = $google_client->fetchAccessTokenWithAuthCode($_GET["code"]);
				if (!isset($token['error'])) {
					$google_client->setAccessToken($google_client->getAccessToken());
					$this->session->set(['access_token' => $google_client->getAccessToken()]);
					$google_service = new Google_Service_Oauth2($google_client);
					$data = $google_service->userinfo->get();
					if (!empty($data['given_name'])) {
						$this->session->set(['user_first_name' => $data['given_name']]);
					}

					if (!empty($data['family_name'])) {
						$this->session->set(['user_last_name' => $data['family_name']]);
					}

					if (!empty($data['email'])) {
						$this->session->set(['user_email_address' => $data['email']]);
					}

					if (!empty($data['gender'])) {
						$this->session->set(['user_gender' => $data['gender']]);
					}

					if (!empty($data['picture'])) {
						$this->session->set(['user_image' => $data['picture']]);
					}
					var_dump($data);
				}
			}

			if (!isset($this->session->access_token)) {

				echo '<a href="' . $google_client->createAuthUrl() . '"><img src="https://www.phpcodingstuff.com/uploads/tutorial_images/google_login_php_coding_stuff.png" /></a>';
			}
		}
		// return view('welcome_message');
	}
}
