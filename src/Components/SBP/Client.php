<?php

namespace FroshPluginUploader\Components\SBP;

use FroshPluginUploader\Components\Util;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

/**
 * @method ResponseInterface get(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface head(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface put(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface post(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface patch(string|UriInterface $uri, array $options = [])
 * @method ResponseInterface delete(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface getAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface headAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface putAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface postAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface patchAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface deleteAsync(string|UriInterface $uri, array $options = [])
 */
class Client
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $apiClient;

    public function __construct()
    {
        $this->apiClient = $this->createClient(null);

        $user = Util::getEnv('ACCOUNT_USER');
        $password = Util::getEnv('ACCOUNT_PASSWORD');
        if (empty($user) || empty($password)) {
            throw new \RuntimeException('The enviroment variable $ACCOUNT_USER and $ACCOUNT_PASSWORD are required');
        }

        $this->login($user, $password);
    }

    public function login(string $username, string $password): void
    {
        $response = $this->apiClient->post('/accesstokens', [
            'json' => [
                'shopwareId' => $username,
                'password' => $password
            ]
        ]);

        $data = json_decode($response->getBody()->__toString(), true);

        if (isset($data['success']) && $data['success'] === false) {
            throw new \RuntimeException(sprintf('Login to Account failed with code %s', $data['code']));
        }

        $this->apiClient = $this->createClient($data['token']);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->apiClient, $name], $arguments);
    }

    private function createClient(?string $token): \GuzzleHttp\Client
    {
        $options = [
            'base_uri' => 'https://api.shopware.com',
            'timeout' => 5.0
        ];

        if ($token) {
            $options['headers'] = [
                'X-Shopware-Token' => $token
            ];
        }

        return new \GuzzleHttp\Client($options);
    }
}