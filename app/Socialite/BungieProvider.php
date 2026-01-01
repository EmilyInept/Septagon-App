<?php

namespace App\Socialite;

use Laravel\Socialite\Two\AbstractProvider;
use Laravel\Socialite\Two\ProviderInterface;
use Laravel\Socialite\Two\User;
use Illuminate\Support\Arr;

class BungieProvider extends AbstractProvider implements ProviderInterface
{
    // Bungie-specific endpoints
    protected function getAuthUrl($state)
    {
        return $this->buildAuthUrlFromBase('https://www.bungie.net/en/OAuth/Authorize', $state);
    }

    protected function getTokenUrl()
    {
        return 'https://www.bungie.net/Platform/App/OAuth/token/';
    }

    /**
     * Bungie requires an X-API-Key header even for token requests.
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get('https://www.bungie.net/Platform/User/GetMembershipsForCurrentUser/', [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'X-API-Key' => config('services.bungie.api_key'),
            ],
        ]);

        return json_decode($response->getBody(), true)['Response'];
    }

    protected function mapUserToObject(array $user)
    {
        // Bungie returns a complex object; we map the BungieNet User specifically
        $bungieNetUser = $user['bungieNetUser'];

        return (new User)->setRaw($user)->map([
            'id'       => $bungieNetUser['membershipId'],
            'nickname' => $bungieNetUser['displayName'],
            'name'     => $bungieNetUser['displayName'],
            'email'    => null, // Bungie does not provide email via OAuth by default
            'avatar'   => 'https://www.bungie.net' . ($bungieNetUser['profilePicturePath'] ?? ''),
        ]);
    }
}
