<?php

namespace UserBundle\Tests\Controller;

use Symfony\Component\HttpFoundation\Response;

/**
 * Created by PhpStorm.
 * User: ngeleedst
 * Date: 02/09/16
 * Time: 09:18
 */
class TokenControllerTest extends AbstractController
{
    public function testTokenGenerateClientCredentials()
    {
	    $content = $this->doRequest('POST', '/api/oauth/v2/token', null, $this->tokenMockData);
        $this->assertObjectHasAttribute('access_token', $content);
    }

    public function testTokenGeneratePassword()
    {
        $parameters = array_merge($this->tokenMockData, ['username' => 'ngeleedst@e-sites.nl', 'password' => 'test']);
	    $content = $this->doRequest('POST', '/api/oauth/v2/token', null, $parameters);
        $this->assertObjectHasAttribute('access_token', $content);
    }
}