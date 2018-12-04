<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Spryker\Shared\Log\LoggerTrait;
use SprykerEco\Shared\AfterPay\AfterPayApiRequestConfig;
use SprykerEco\Zed\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface;

class ApiVersionCall implements ApiVersionCallInterface
{
    use LoggerTrait;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface
     */
    protected $client;

    /**
     * @var \SprykerEco\Zed\AfterPay\AfterPayConfig
     */
    private $config;

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface
     */
    private $utilEncoding;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface $client
     * @param \SprykerEco\Zed\AfterPay\AfterPayConfig $config
     * @param \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface $utilEncoding
     */
    public function __construct(
        ClientInterface $client,
        AfterPayConfig $config,
        AfterPayToUtilEncodingServiceInterface $utilEncoding
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

        if (is_array($jsonResponseArray) && isset($jsonResponseArray[AfterPayApiRequestConfig::API_VERSION])) {
            return $jsonResponseArray[AfterPayApiRequestConfig::API_VERSION];
        }

        return '';
    }

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException $apiHttpRequestException
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
