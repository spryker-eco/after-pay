<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer;
use SprykerEco\Shared\AfterPay\AfterPayApiRequestConfig;
use SprykerEco\Zed\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface;

class ValidateBankAccountCall extends AbstractApiCall implements ValidateBankAccountCallInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface
     */
    protected $client;

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilTextServiceInterface
     */
    protected $utilText;

    /**
     * @var \SprykerEco\Zed\AfterPay\AfterPayConfig
     */
    protected $config;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface $client
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface $transferConverter
     * @param \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface $utilEncoding
     * @param \SprykerEco\Zed\AfterPay\AfterPayConfig $config
     */
    public function __construct(
        ClientInterface $client,
        TransferToCamelCaseArrayConverterInterface $transferConverter,
        AfterPayToUtilEncodingServiceInterface $utilEncoding,
        AfterPayConfig $config
    ) {
        $this->client = $client;
        $this->utilEncoding = $utilEncoding;
        $this->config = $config;
        $this->transferConverter = $transferConverter;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer
     */
    public function execute(AfterPayValidateBankAccountRequestTransfer $validateBankAccountRequestTransfer): AfterPayValidateBankAccountResponseTransfer
    {
        $jsonRequest = $this->buildJsonRequestFromTransferObject($validateBankAccountRequestTransfer);

        try {
            $jsonResponse = $this->client->sendPost(
                $this->config->getValidateBankAccountApiEndpointUrl(),
                $jsonRequest
            );
        } catch (ApiHttpRequestException $apiHttpRequestException) {
            $this->logApiException($apiHttpRequestException);
            $jsonResponse = '[]';
        }

        return $this->buildValidateBankAccountResponseTransfer($jsonResponse);
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateBankAccountResponseTransfer
     */
    protected function buildValidateBankAccountResponseTransfer(string $jsonResponse): AfterPayValidateBankAccountResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        return (new AfterPayValidateBankAccountResponseTransfer())
            ->setIsValid($jsonResponseArray[AfterPayApiRequestConfig::VALIDATE_BANK_ACCOUNT_IS_VALID] ?? false);
    }
}
