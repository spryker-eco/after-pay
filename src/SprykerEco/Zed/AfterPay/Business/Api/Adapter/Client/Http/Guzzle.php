<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\Http;

use Generated\Shared\Transfer\AfterPayApiResponseErrorTransfer;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use SprykerEco\Zed\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface;

class Guzzle implements ClientInterface
{
    public const REQUEST_METHOD_POST = 'POST';
    public const REQUEST_METHOD_GET = 'GET';

    public const REQUEST_HEADER_X_AUTH_KEY = 'X-Auth-Key';
    public const REQUEST_HEADER_CONTENT_TYPE = 'Content-Type';

    public const HEADER_CONTENT_TYPE_JSON = 'application/json';

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface
     */
    protected $encodingService;

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    protected $client;

    /**
     * @var \SprykerEco\Zed\AfterPay\AfterPayConfig
     */
    protected $config;

    /**
     * @param \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface $encodingService
     * @param \SprykerEco\Zed\AfterPay\AfterPayConfig $config
     */
    public function __construct(
        AfterPayToUtilEncodingServiceInterface $encodingService,
        AfterPayConfig $config
    ) {
        $this->encodingService = $encodingService;
        $this->config = $config;
        $this->client = new Client();
    }

    /**
     * @param string $endPointUrl
     * @param string|null $jsonBody
     *
     * @return \Psr\Http\Message\StreamInterface
     */
    public function sendPost(string $endPointUrl, ?string $jsonBody = null): StreamInterface
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
    public function sendGet(string $endPointUrl): string
    {
        $getRequest = $this->buildGetRequest($endPointUrl);
        $response = $this->send($getRequest);

        return $response->getBody()->getContents();
    }

    /**
     * @param string $endPointUrl
     *
     * @return int
     */
    public function getStatus(string $endPointUrl): int
    {
        $getRequest = $this->buildGetRequest($endPointUrl);
        $response = $this->send($getRequest);

        return $response->getStatusCode();
    }

    /**
     * @param \Psr\Http\Message\RequestInterface $request
     * @param array $options
     *
     * @throws \SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        try {
            return $this->client->send($request, $options);
        } catch (RequestException $requestException) {
            $apiHttpRequestException = new ApiHttpRequestException($requestException->getMessage());

            $response = $requestException->getResponse();
            if ($response instanceof ResponseInterface) {
                $content = $response->getBody()->getContents();
                if (!empty($content)) {
                    $errorResponseData = $this->encodingService->decodeJson($content, true);
                    if (isset($errorResponseData[0])) {
                        $errorDetails = $errorResponseData[0];

                        $apiErrorTransfer = (new AfterPayApiResponseErrorTransfer())
                            ->setActionCode($errorDetails['actionCode'])
                            ->setCode($errorDetails['code'])
                            ->setType($errorDetails['type'])
                            ->setMessage($errorDetails['message'])
                            ->setIsSuccess(false);

                        $apiHttpRequestException->setError($apiErrorTransfer);
                        $apiHttpRequestException->setDetailedMessage($content);
                    }
                }
            }

            throw $apiHttpRequestException;
        }
    }

    /**
     * @param string $endPointUrl
     * @param string|null $jsonBody
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function buildPostRequest(string $endPointUrl, ?string $jsonBody = null): RequestInterface
    {
        return new Request(
            static::REQUEST_METHOD_POST,
            $endPointUrl,
            [
                (string)static::REQUEST_HEADER_CONTENT_TYPE => (string)static::HEADER_CONTENT_TYPE_JSON,
                (string)static::REQUEST_HEADER_X_AUTH_KEY => (string)$this->config->getApiCredentialsAuthKey(),
            ],
            $jsonBody
        );
    }

    /**
     * @param string $endPointUrl
     *
     * @return \Psr\Http\Message\RequestInterface
     */
    protected function buildGetRequest(string $endPointUrl): RequestInterface
    {
        return new Request(
            static::REQUEST_METHOD_GET,
            $endPointUrl,
            [
                (string)static::REQUEST_HEADER_CONTENT_TYPE => (string)static::HEADER_CONTENT_TYPE_JSON,
                (string)static::REQUEST_HEADER_X_AUTH_KEY => (string)$this->config->getApiCredentialsAuthKey(),
            ]
        );
    }
}
