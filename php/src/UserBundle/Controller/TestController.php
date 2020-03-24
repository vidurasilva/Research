<?php
/**
 * Created by PhpStorm.
 * User: mdubbelman
 * Date: 9/8/16
 * Time: 10:25 AM
 */

namespace UserBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TestController extends Controller
{
	public function getFacebookTokenAction(Request $request)
	{
		/** @var \UserBundle\Service\FacebookService $fb */
		$fb = $this->container->get('user.service.facebook');
		/** @var \Facebook\Facebook $fb */
		$fb = $fb->getFacebookAdapter();

		$helper = $fb->getRedirectLoginHelper();
		$redirectUrl = $this->get('router')->generate('facebook_action_token_test');
		$redirectUrl = $request->getSchemeAndHttpHost() . $redirectUrl;

		echo 'Current App ID: '.$fb->getApp()->getId().'<br>';
		echo 'Redirect url: ' . $redirectUrl . '<br>';

		$accessToken = null;
		if ($code = $request->query->get('code')) {
			try {
				$accessToken = $fb->getOAuth2Client()->getAccessTokenFromCode($code, $redirectUrl);
				$accessToken = $fb->getOAuth2Client()->getLongLivedAccessToken($accessToken);
			} catch (\Exception $e) {
				echo '<pre style="background: white;color:black;position:relative;z-index:9999;">';
				print_r($e->getMessage());
				echo '</pre>';
			}
		}


		if ($accessToken) {
			echo 'Got accesstoken: <br>';
			echo '<pre>' . $accessToken->getValue() . '</pre>';
			echo 'Expires at: ' . $accessToken->getExpiresAt()->format('d-m-Y H:i:s') . '<br>';
		} else {
			$loginUrl = $helper->getLoginUrl(
				$redirectUrl,
				['email']
			);
			echo '<a href="'.$loginUrl.'">Login</a>';
		}

		die;
	}

	public function getGoogleTokenAction(Request $request)
	{
		$google = $this->get('user.service.google');
		$client = $google->getClient();

		$redirectUrl = $this->get('router')->generate('google_action_token_test');
		$redirectUrl = $request->getSchemeAndHttpHost() . $redirectUrl;
		$client->setRedirectUri($redirectUrl);

		$authUrl = $client->createAuthUrl(['profile', 'email']);

		echo 'Current client ID: '.$client->getClientId().'<br>';
		echo 'Redirect url: ' . $redirectUrl . '<br>';

		$accessToken = null;
		if ($code = $request->query->get('code')) {
			try {
				$accessToken = $client->fetchAccessTokenWithAuthCode($code);
				if (!empty($accessToken['error'])) {
					throw new \Exception($accessToken['error_description']);
				}
				$client->setAccessToken($accessToken);

			} catch (\Exception $e) {
				$accessToken = null;
				echo '<pre style="background: white;color:black;position:relative;z-index:9999;">';
				print_r($e->getMessage());
				echo '</pre>';
			}
		}

		if ($accessToken) {
			echo '<pre style="background: white;color:black;position:relative;z-index:9999;">';
			print_r($accessToken);
			echo "\n@".__FILE__.':'.__LINE__.'</pre>';
			die;
		} else {

			echo '<a href="'.$authUrl.'">Login</a>';
		}

		die;
	}
}