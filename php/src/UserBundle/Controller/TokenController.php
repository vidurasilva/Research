<?php

namespace UserBundle\Controller;

use \FOS\OAuthServerBundle\Controller\TokenController as BaseController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations\Route;
use Symfony\Component\HttpFoundation\Request;
use OAuth2\OAuth2;

class TokenController extends BaseController
{
    /**
     *
     * @ApiDoc(
     *  section="Auth",
     *  output="FOS\OAuthServerBundle\Model\TokenInterface",
     *  filters = {
     *      {
     *          "name" = "Method 1: Anonymous authentication (Used for public API calls like the registration node)",
     *          " " = "client_id=..., client_secret=..., grant_type=client_credentials"
     *      },
     *      {
     *          "name" = "Method 2: Username + Password (Used for private API calls)",
     *          " " = "client_id=..., client_secret=..., grant_type=password, username=..., password=..."
     *      },
     *      {
     *          "name" = "Method 3: Refresh token (Used for refreshing the access token and to stay logged-in)",
     *          " " = "client_id=..., client_secret=..., grant_type=refresh_token, refresh_token=..."
     *      }
     *  },
     *  requirements={
     *      { "name"="client_id", "dataType"="string", "description"="The client application's identifier. Combination of the fields 'id' and 'random id', separated by an _, for example 1_theRandomId"},
     *      { "name"="client_secret", "dataType"="string", "description"="The client application's secret"},
     *      { "name"="grant_type", "dataType"="string", "requirement"="refresh_token|password|client_credentials", "description"="Grant type"},
     *  },
     *  parameters={
     *      { "name"="username", "dataType"="string", "required"=false, "description"="User name (for `password` grant type)"},
     *      { "name"="password", "dataType"="string", "required"=false, "description"="User password (for `password` grant type)"},
     *      { "name"="refresh_token", "dataType"="string", "required"=false, "description"="The authorization code received by the authorization server(for `refresh_token` grant type`"},
     *  },
     *  tags={
     *     "public" = "#00cc99"
     *  }
     * )
     *
     * @param Request $request
     * @return TokenInterface
     */
    public function tokenAction(Request $request)
    {
        $result =  parent::tokenAction($request);

        return $result;
    }
}