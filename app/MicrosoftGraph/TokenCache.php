<?php
// Copyright (c) Microsoft Corporation.
// Licensed under the MIT License.

namespace App\MicrosoftGraph;

class TokenCache {
  public function storeTokens($accessToken, $user, $profilePhoto) {
    session([
      'accessToken' => $accessToken->getToken(),
      'refreshToken' => $accessToken->getRefreshToken(),
      'tokenExpires' => $accessToken->getExpires(),
      'userName' => $user->getDisplayName(),
      'userEmail' => null !== $user->getMail() ? $user->getMail() : $user->getUserPrincipalName(),
      'userPrincipalName' => $user->getUserPrincipalName(),
      //'userTimeZone' => $user->getMailboxSettings()->getTimeZone(),
      'userTimeZone' => "Pacific Standard Time",
    ]);
    if ($profilePhoto) {
      session()->put('profilePhoto', base64_encode($profilePhoto) );
    }

    
  }

  public function clearTokens() {
    session()->forget('accessToken');
    session()->forget('refreshToken');
    session()->forget('tokenExpires');
    session()->forget('userName');
    session()->forget('userEmail');
    session()->forget('userTimeZone');
    session()->forget('profilePhoto');
  }

  // <GetAccessTokenSnippet>
  public function getAccessToken() {
    // Check if tokens exist
    if (empty(session('accessToken')) ||
        empty(session('refreshToken')) ||
        empty(session('tokenExpires'))) {
      return '';
    }

    // Check if token is expired
    //Get current time + 5 minutes (to allow for time differences)
    $now = time() + 300;
    if (session('tokenExpires') <= $now) {
      // Token is expired (or very close to it)
      // so let's refresh

      // Initialize the OAuth client
      $oauthClient = new \League\OAuth2\Client\Provider\GenericProvider([
        'clientId'                => env('OAUTH_APP_ID'),
        'clientSecret'            => env('OAUTH_APP_PASSWORD'),
        'redirectUri'             => env('OAUTH_REDIRECT_URI'),
        'urlAuthorize'            => env('OAUTH_AUTHORITY').env('OAUTH_AUTHORIZE_ENDPOINT'),
        'urlAccessToken'          => env('OAUTH_AUTHORITY').env('OAUTH_TOKEN_ENDPOINT'),
        'urlResourceOwnerDetails' => '',
        'scopes'                  => env('OAUTH_SCOPES')
      ]);

      try {
        $newToken = $oauthClient->getAccessToken('refresh_token', [
          'refresh_token' => session('refreshToken')
        ]);

        // Store the new values
        $this->updateTokens($newToken);

        return $newToken->getToken();
      }
      catch (League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
        return '';
      }
    }

    // Token is still valid, just return it
    return session('accessToken');
  }
  // </GetAccessTokenSnippet>

  // <UpdateTokensSnippet>
  public function updateTokens($accessToken) {
    session([
      'accessToken' => $accessToken->getToken(),
      'refreshToken' => $accessToken->getRefreshToken(),
      'tokenExpires' => $accessToken->getExpires()
    ]);
  }
  // </UpdateTokensSnippet>

  public function getAccessTokenForApplication()
  {
    $now = time();

      // Check if tokens expired
      $now = time();
      if (session('accessToken') and (session('tokenExpires') > $now) ) {
        return session('accessToken');
      }

      $endpoint = "https://login.microsoftonline.com/".env('OAUTH_TENANT').env('OAUTH_TOKEN_ENDPOINT');
      $body = "grant_type=".'client_credentials'
              ."&client_id=".env('OAUTH_APP_ID')
              ."&scope=".'https://graph.microsoft.com/.default'
              ."&client_secret=".env('OAUTH_APP_PASSWORD');

      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $endpoint);
      curl_setopt($ch, CURLOPT_POST, 1);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
      curl_setopt($ch, CURLOPT_FAILONERROR, 0);
      // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // turns off SSL check,
      // curl_setopt($ch, CURLOPT_PROXY, "127.0.0.1:8888"); // need for fiddler + auth
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('application/json; charset=utf-8', 'Content-Length: ' . strlen($body)));

      $response = curl_exec ($ch);
      curl_close($ch);

      if(!$response) {
        return '';
      } else 
      {
          $token_array = json_decode($response, true);
          session([
            'accessToken' => $token_array['access_token'],
            'tokenExpires' => $now + $token_array['expires_in'],
          ]);
      }

      return $token_array['access_token'];
  }


}
