<?php

namespace App\Libraries;

class GoogleOAuth2
{
    protected $client;

    public function init()
    {
        // load Google API Client Vendors
        $this->client = new \Google_Client();
        
        // Google OAuth2 Credential
        $this->client->setClientId(config('App')->OAuth2Config['clientId']);
        $this->client->setClientSecret(config('App')->OAuth2Config['clientSecret']);
        $this->client->setRedirectUri(base_url(config('App')->OAuth2Config['redirectUri']));
        $this->client->addScope("email");
        $this->client->addScope("profile");

        return $this->client;
    }
}
