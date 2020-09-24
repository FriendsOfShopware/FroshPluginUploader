<?php declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP;

use FroshPluginUploader\Components\SBP\Components\General;
use FroshPluginUploader\Components\SBP\Components\Plugin;
use FroshPluginUploader\Components\SBP\Components\Producer;
use FroshPluginUploader\Structs\Producer as ProducerStruct;
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
 * @method PromiseInterface  getAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface  headAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface  putAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface  postAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface  patchAsync(string|UriInterface $uri, array $options = [])
 * @method PromiseInterface  deleteAsync(string|UriInterface $uri, array $options = [])
 */
class Client
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $apiClient;

    /**
     * @var array
     */
    private $components;

    /**
     * @var int
     */
    private $userId;

    /**
     * @var ProducerStruct
     */
    private $producer;

    public function __construct()
    {
        $this->apiClient = $this->createClient(null);

        $user = $_SERVER['ACCOUNT_USER'] ?? '';
        $password = $_SERVER['ACCOUNT_PASSWORD'] ?? '';
        if (empty($user) || empty($password)) {
            throw new \RuntimeException('The enviroment variable $ACCOUNT_USER and $ACCOUNT_PASSWORD are required');
        }

        $this->login($user, $password);
    }

    public function __call($name, $arguments)
    {
        return call_user_func_array([$this->apiClient, $name], $arguments);
    }

    public function login(string $username, string $password): void
    {
        $response = $this->apiClient->post('/accesstokens', [
            'json' => [
                'shopwareId' => $username,
                'password' => $password,
            ],
        ]);

        $data = json_decode($response->getBody()->__toString(), true);

        if (isset($data['success']) && $data['success'] === false) {
            throw new \RuntimeException(sprintf('Login to Account failed with code %s', $data['code']));
        }

        $this->apiClient = $this->createClient($data['token']);
        $this->userId = (int) $data['userId'];

        $this->components['plugins'] = new Plugin($this);
        $this->components['producer'] = new Producer($this);
        $this->components['general'] = new General($this);

        $this->producer = $this->Producer()->getProducer();
    }

    public function Plugins(): Plugin
    {
        return $this->components['plugins'];
    }

    public function Producer(): Producer
    {
        return $this->components['producer'];
    }

    public function General(): General
    {
        return $this->components['general'];
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getProducer(): ProducerStruct
    {
        return $this->producer;
    }

    private function createClient(?string $token): \GuzzleHttp\Client
    {
        $options = [
            'base_uri' => $_SERVER['API_ENDPOINT'] ?? 'https://api.shopware.com',
            'timeout' => 30.0,
        ];

        if ($token) {
            $options['headers'] = [
                'X-Shopware-Token' => $token,
            ];
        }

        return new \GuzzleHttp\Client($options);
    }
}
