<?php
namespace PHPTikkie;

use Firebase\JWT\JWT;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use PHPTikkie\Exceptions\AccessTokenException;
use PHPTikkie\Exceptions\RequestException;

class Environment
{
    /**
     * @var float
     */
    const VERSION = 0.1;

    /**
     * @var string
     */
    const DEFAULT_HASH_ALGORITHM = 'RS256';

    /**
     * @var string
     */
    const PRODUCTION_API_URL = 'https://api.abnamro.com';

    /**
     * @var string
     */
    const PRODUCTION_TOKEN_URL = 'https://auth.abnamro.com/oauth/token';

    /**
     * @var string
     */
    const SANDBOX_API_URL = 'https://api-sandbox.abnamro.com';

    /**
     * @var string
     */
    const SANDBOX_TOKEN_URL = 'https://auth-sandbox.abnamro.com/oauth/token';

    /**
     * @var AccessToken
     */
    private $accessToken;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $hashAlgorithm;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var boolean
     */
    private $testMode;

    public function __construct(string $apiKey, bool $testMode = false)
    {
        $this->apiKey = $apiKey;
        $this->testMode = $testMode;

        $this->httpClient = new HttpClient([
            'base_uri' => $testMode ? static::SANDBOX_API_URL : static::PRODUCTION_API_URL,
            'http_errors' => false,
            'headers' => ['User-Agent' => 'PHPTikkie/'.static::VERSION]
        ]);
    }

    public function loadPrivateKey(string $path, string $hashAlgorithm = self::DEFAULT_HASH_ALGORITHM)
    {
        return $this->loadPrivateKeyFromString(file_get_contents($path), $hashAlgorithm);
    }

    public function loadPrivateKeyFromString(string $privateKey, string $hashAlgorithm = self::DEFAULT_HASH_ALGORITHM)
    {
        $this->privateKey = $privateKey;
        $this->hashAlgorithm = $hashAlgorithm;
    }

    protected function getAccessToken(): AccessToken
    {
        if ($this->accessToken && $this->accessToken->isValid()) {
            return $this->accessToken;
        }

        return $this->accessToken = $this->requestAccessToken();
    }

    protected function getJsonWebToken(): string
    {
        if ($this->privateKey === null) {
            throw new AccessTokenException("Cannot create JSON Web Token because no Private Key has been set.");
        }

        $now = time();

        return JWT::encode([
            'exp' => $now + 60, // Expires after one minute
            'nbf' => $now - 60,
            'iss' => 'PHPTikkie',
            'sub' => $this->apiKey,
            'aud' => $this->testMode ? static::SANDBOX_TOKEN_URL : static::PRODUCTION_TOKEN_URL
        ], $this->privateKey, $this->hashAlgorithm);
    }

    protected function requestAccessToken(): AccessToken
    {
        try {
            $response = $this->httpClient->request('POST', '/v1/oauth/token', [
                'headers' => [
                    'API-Key' => $this->apiKey
                ],
                'form_params' => [
                    'client_assertion' => $this->getJsonWebToken(),
                    'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                    'grant_type' => 'client_credentials',
                    'scope' => 'tikkie'
                ]
            ]);

            if ($response->getStatusCode() == 200) {
                $responseData = json_decode($response->getBody());

                return new AccessToken($responseData->access_token, (int) $responseData->expires_in);
            }

            throw new AccessTokenException($response->getBody());
        } catch (ClientException $exception) {
            throw new AccessTokenException($exception->getMessage());
        }
    }

    public function getRequest(string $endpoint, array $parameters = []): Response
    {
        try {
            $response = $this->httpClient->request('GET', $endpoint, [
                'headers' => [
                    'API-Key' => $this->apiKey,
                    'Authorization' => "Bearer {$this->getAccessToken()}"
                ],
                'query' => $parameters
            ]);

            if ($response->getStatusCode() == 200) {
                return new Response($response);
            }

            throw new RequestException($response->getBody());
        } catch (ClientException $exception) {
            throw new RequestException($exception->getMessage());
        }
    }

    public function postRequest(string $endpoint, array $data): Response
    {
        try {
            $response = $this->httpClient->request('POST', $endpoint, [
                'headers' => [
                    'API-Key' => $this->apiKey,
                    'Authorization' => "Bearer {$this->getAccessToken()}"
                ],
                'json' => $data
            ]);

            if (in_array($response->getStatusCode(), [200, 201])) {
                return new Response($response);
            }

            throw new RequestException($response->getBody());
        } catch (ClientException $exception) {
            throw new RequestException($exception->getMessage());
        }
    }
}
