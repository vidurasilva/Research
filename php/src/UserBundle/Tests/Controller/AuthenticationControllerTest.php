<?php

namespace UserBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 02/09/16
 * Time: 09:18
 */
class AuthenticationControllerTest extends AbstractController
{
    public function testAuthenticationController()
    {
	    $content = $this->doRequest('POST', '/api/authorization/registration', $this->token, $this->authMockData);
        $this->assertObjectHasAttribute('status', $content);
        $this->assertEquals('success', $content->status);
    }
}