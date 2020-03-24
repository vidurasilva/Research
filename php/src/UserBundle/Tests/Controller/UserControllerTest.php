<?php

namespace UserBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 02/09/16
 * Time: 09:18
 */
class UserControllerTest extends AbstractController
{
    public function testUserMe()
    {
	    $content = $this->doRequest('GET', '/api/user/me', $this->getLoggedInToken());
	    $this->assertObjectHasAttribute('id', $content->data);
    }

	public function testUserMustBeLoggedIn()
	{
		$this->doRequest('GET', '/api/user/me', $this->token, [], [], false);
		$this->assertEquals(
			Response::HTTP_FORBIDDEN,
			$this->client->getResponse()->getStatusCode(),
			'User should not be able to log in anonymously'
		);
	}
}