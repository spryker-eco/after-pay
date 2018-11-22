<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Spryker\Shared\Log\LoggerTrait;
use SprykerEco\Zed\Afterpay\AfterpayConfig;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException;

class ApiStatusCall implements ApiStatusCallInterface
{
    const RESPONSE_STATUS_NOT_AVAILABLE = 503;

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
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface $client
     * @param \SprykerEco\Zed\Afterpay\AfterpayConfig $config
     */
    public function __construct(
        ClientInterface $client,
        AfterpayConfig $config
    ) {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @return int
     */
    public function execute()
    {
        try {
            $jsonResponse = $this->client->getStatus(
                $this->config->getStatusApiEndpointUrl()
            );
        } catch (ApiHttpRequestException $apiHttpRequestException) {
            $this->logApiException($apiHttpRequestException);
            $jsonResponse = static::RESPONSE_STATUS_NOT_AVAILABLE;
        }

        return $jsonResponse;
    }

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException $apiHttpRequestException
     *
     * @return void
     */
    protected function logApiException(ApiHttpRequestException $apiHttpRequestException)
    {
        $this->getLogger()->error(
            $apiHttpRequestException->getMessage(),
            ['exception' => $apiHttpRequestException]
        );
    }
}
