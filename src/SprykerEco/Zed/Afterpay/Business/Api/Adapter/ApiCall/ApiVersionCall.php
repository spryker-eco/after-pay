<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Spryker\Shared\Log\LoggerTrait;
use SprykerEco\Shared\Afterpay\AfterpayApiRequestConfig;
use SprykerEco\Zed\Afterpay\AfterpayConfig;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface;

class ApiVersionCall implements ApiVersionCallInterface
{
    use LoggerTrait;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface
     */
    protected $client;

    /**
     * @var \SprykerEco\Zed\Afterpay\AfterpayConfig
     */
    private $config;

    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface
     */
    private $utilEncoding;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface $client
     * @param \SprykerEco\Zed\Afterpay\AfterpayConfig $config
     * @param \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface $utilEncoding
     */
    public function __construct(
        ClientInterface $client,
        AfterpayConfig $config,
        AfterpayToUtilEncodingInterface $utilEncoding
    ) {
        $this->client = $client;
        $this->config = $config;
        $this->utilEncoding = $utilEncoding;
    }

    /**
     * @return string
     */
    public function execute(): string
    {
        try {
            $jsonResponse = $this->client->sendGet(
                $this->config->getVersionApiEndpointUrl()
            );
        } catch (ApiHttpRequestException $apiHttpRequestException) {
            $this->logApiException($apiHttpRequestException);
            $jsonResponse = '[]';
        }

        return $this->parseVersion($jsonResponse);
    }

    /**
     * @param string $jsonResponse
     *
     * @return string
     */
    protected function parseVersion(string $jsonResponse): string
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        if (is_array($jsonResponseArray) && isset($jsonResponseArray[AfterpayApiRequestConfig::API_VERSION])) {
            return $jsonResponseArray[AfterpayApiRequestConfig::API_VERSION];
        }

        return '';
    }

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException $apiHttpRequestException
     *
     * @return void
     */
    protected function logApiException(ApiHttpRequestException $apiHttpRequestException): void
    {
        $this->getLogger()->error(
            $apiHttpRequestException->getMessage(),
            ['exception' => $apiHttpRequestException]
        );
    }
}
