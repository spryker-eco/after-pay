<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Spryker\Shared\Log\LoggerTrait;
use SprykerEco\Zed\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException;

class ApiStatusCall implements ApiStatusCallInterface
{
    public const RESPONSE_STATUS_NOT_AVAILABLE = 503;

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
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface $client
     * @param \SprykerEco\Zed\AfterPay\AfterPayConfig $config
     */
    public function __construct(
        ClientInterface $client,
        AfterPayConfig $config
    ) {
        $this->client = $client;
        $this->config = $config;
    }

    /**
     * @return int
     */
    public function execute(): int
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
