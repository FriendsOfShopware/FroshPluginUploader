<?php
declare(strict_types=1);

namespace FroshPluginUploader\Components\SBP;

use function assert;
use function call_user_func_array;
use FroshPluginUploader\Components\SBP\Components\General;
use FroshPluginUploader\Components\SBP\Components\Plugin;
use FroshPluginUploader\Components\SBP\Components\Producer;
use FroshPluginUploader\Structs\Producer as ProducerStruct;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Promise\PromiseInterface;
use function is_string;
use const JSON_THROW_ON_ERROR;
use function mb_strtolower;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use RuntimeException;

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
    private GuzzleHttpClient $apiClient;
    private array $components = [];
    private ?int $userId;
    private ?ProducerStruct $producer;
    private bool $connected = false;

    public function __construct()
    {
        $this->apiClient = $this->createClient(null);
    }

    public function __call($name, $arguments)
    {
        $this->ensureConnected();

        $lowerName = mb_strtolower($name);
        assert(is_string($lowerName));

        return $this->components[$lowerName] ?? call_user_func_array([$this->apiClient, $name], $arguments);
    }

    public function login(): void
    {
        $username = $_SERVER['ACCOUNT_USER'] ?? '';
        $password = $_SERVER['ACCOUNT_PASSWORD'] ?? '';
        if (empty($username) || empty($password)) {
            throw new RuntimeException('The environment variable $ACCOUNT_USER and $ACCOUNT_PASSWORD are required');
        }

        $response = $this->apiClient->post('/accesstokens', [
            'json' => [
                'shopwareId' => $username,
                'password' => $password,
            ],
        ]);

        $data = json_decode($response->getBody()->__toString(), true, 512, JSON_THROW_ON_ERROR);

        if (isset($data['success']) && $data['success'] === false) {
            throw new RuntimeException(sprintf('Login to Account failed with code %s', $data['code']));
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
        $this->ensureConnected();

        return $this->components['plugins'];
    }

    public function Producer(): Producer
    {
        $this->ensureConnected();

        return $this->components['producer'];
    }

    public function General(): General
    {
        $this->ensureConnected();

        return $this->components['general'];
    }

    public function getUserId(): int
    {
        if (!isset($this->userId)) {
            throw new RuntimeException('No user id could be found');
        }

        return $this->userId;
    }

    public function getProducer(): ProducerStruct
    {
        if (!isset($this->producer)) {
            throw new RuntimeException('No producer could be found');
        }

        return $this->producer;
    }

    private function createClient(?string $token): GuzzleHttpClient
    {
        $options = [
            'base_uri' => $_SERVER['API_ENDPOINT'] ?? 'https://api.shopware.com',
            'timeout' => 60.0,
        ];

        if ($token) {
            $options['headers'] = [
                'X-Shopware-Token' => $token,
            ];
        }

        return new GuzzleHttpClient($options);
    }

    private function ensureConnected(): void
    {
        if ($this->connected) {
            return;
        }

        $this->connected = true;
        $this->login();
    }
}
