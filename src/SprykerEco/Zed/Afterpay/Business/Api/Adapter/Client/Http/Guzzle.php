<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use SprykerEco\Zed\Afterpay\AfterpayConfig;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException;

class Guzzle implements ClientInterface
{
    const REQUEST_METHOD_POST = 'POST';
    const REQUEST_METHOD_GET = 'GET';

    const REQUEST_HEADER_X_AUTH_KEY = 'X-Auth-Key';
    const REQUEST_HEADER_CONTENT_TYPE = 'Content-Type';

    const HEADER_CONTENT_TYPE_JSON = 'application/json';

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @var \SprykerEco\Zed\Afterpay\AfterpayConfig
     */
    private $config;

    /**
     * @param \SprykerEco\Zed\Afterpay\AfterpayConfig $config
     */
    public function __construct(AfterpayConfig $config)
    {
        $this->config = $config;
        $this->client = new Client();
    }

    /**
     * @param string $endPointUrl
     * @param string|null $jsonBody
     *
     * @return string
     */
    public function sendPost($endPointUrl, $jsonBody = null)
    {
        $postRequest = $this->buildPostRequest($endPointUrl, $jsonBody);
        $response = $this->send($postRequest);

        return $response->getBody();
    }

    /**
     * @param string $endPointUrl
     *
     * @return string
     */
    public function sendGet($endPointUrl)
    {
        $getRequest = $this->buildGetRequest($endPointUrl);
        $response = $this->send($getRequest);

        return $response->getBody()->getContents();
    }

    /**
     * @param string $endPointUrl
     *
     * @return string
     */
    public function getStatus($endPointUrl)
    {
        $getRequest = $this->buildGetRequest($endPointUrl);
        $response = $this->send($getRequest);

        return $response->getStatusCode();
    }

    /**
     * @param \GuzzleHttp\Psr7\Request $request
     * @param array $options
     *
     * @throws \SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function send($request, array $options = [])
    {
        try {
            return $this->client->send($request, $options);
        } catch (RequestException $requestException) {
            throw new ApiHttpRequestException($requestException->getMessage());
        }
    }

    /**
     * @param string $endPointUrl
     * @param string|null $jsonBody
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function buildPostRequest($endPointUrl, $jsonBody = null)
    {
        return new Request(
            static::REQUEST_METHOD_POST,
            $endPointUrl,
            [
                static::REQUEST_HEADER_CONTENT_TYPE => static::HEADER_CONTENT_TYPE_JSON,
                static::REQUEST_HEADER_X_AUTH_KEY => $this->config->getApiCredentialsAuthKey(),
            ],
            $jsonBody
        );
    }

    /**
     * @param string $endPointUrl
     *
     * @return \GuzzleHttp\Psr7\Request
     */
    protected function buildGetRequest($endPointUrl)
    {
        return new Request(
            static::REQUEST_METHOD_GET,
            $endPointUrl,
            [
                static::REQUEST_HEADER_CONTENT_TYPE => static::HEADER_CONTENT_TYPE_JSON,
                static::REQUEST_HEADER_X_AUTH_KEY => $this->config->getApiCredentialsAuthKey(),
            ]
        );
    }
}
