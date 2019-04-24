<?php
namespace PHPTikkie;

use Firebase\JWT\JWT;
use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use PHPTikkie\Exceptions\AccessTokenException;
use PHPTikkie\Exceptions\RequestException;
use PHPTikkie\Requests\AbstractRequest;

class Environment
{
    const VERSION = '0.2.3';
    const DEFAULT_HASH_ALGORITHM = 'RS256';
    const PRODUCTION_API_URL = 'https://api.abnamro.com';
    const PRODUCTION_TOKEN_URL = 'https://auth.abnamro.com/oauth/token';
    const SANDBOX_API_URL = 'https://api-sandbox.abnamro.com';
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

    public function __construct(string $apiKey, bool $testMode = false, array $requestOptions = [])
    {
        $this->apiKey = $apiKey;
        $this->testMode = $testMode;

        $this->httpClient = new HttpClient(array_merge([
            'base_uri' => $testMode ? static::SANDBOX_API_URL : static::PRODUCTION_API_URL,
            'http_errors' => false,
            'headers' => ['User-Agent' => 'PHPTikkie/'.static::VERSION]
        ], $requestOptions));
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
        if (empty($this->privateKey)) {
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

    /**
     * @throws AccessTokenException
     */
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

            if ($response->getStatusCode() == 200 && is_object($responseData = json_decode($response->getBody()))) {
                return new AccessToken($responseData->access_token, (int) $responseData->expires_in);
            }

            throw new AccessTokenException($response->getBody());
        } catch (GuzzleException $exception) {
            throw new AccessTokenException($exception->getMessage());
        }
    }

    protected function getRequestOptions(AbstractRequest $request): array
    {
        $options = [
            RequestOptions::HEADERS => [
                'API-Key' => $this->apiKey,
                'Authorization' => "Bearer {$this->getAccessToken()}",
                'Accept' => 'application/json'
            ]
        ];

        if ($parameters = $request->getParameters()) {
            $options[RequestOptions::QUERY] = $parameters;
        }

        if ($payload = $request->getPayload()) {
            $options[RequestOptions::JSON] = $payload;
        }

        return array_merge_recursive($options, $request->getRequestOptions());
    }

    /**
     * @throws RequestException
     */
    public function send(AbstractRequest $request): Response
    {
        try {
            $response = $this->httpClient->request(
                $request->getMethod(),
                $request->getUri(),
                $this->getRequestOptions($request)
            );
            
            if (in_array($response->getStatusCode(), [200, 201])) {
                return new Response($response);
            }

            throw new RequestException($response->getBody());
        } catch (GuzzleException $exception) {
            throw new RequestException($exception->getMessage());
        }
    }
}
