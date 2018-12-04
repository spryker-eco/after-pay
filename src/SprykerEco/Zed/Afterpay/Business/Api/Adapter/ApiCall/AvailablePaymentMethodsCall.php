<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsResponseTransfer;
use SprykerEco\Shared\Afterpay\AfterpayApiRequestConfig;
use SprykerEco\Zed\Afterpay\AfterpayConfig;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface;

class AvailablePaymentMethodsCall extends AbstractApiCall implements AvailablePaymentMethodsCallInterface
{
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
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface $transferConverter
     * @param \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface $utilEncoding
     * @param \SprykerEco\Zed\Afterpay\AfterpayConfig $config
     */
    public function __construct(
        ClientInterface $client,
        TransferToCamelCaseArrayConverterInterface $transferConverter,
        AfterpayToUtilEncodingInterface $utilEncoding,
        AfterpayConfig $config
    ) {
        $this->client = $client;
        $this->transferConverter = $transferConverter;
        $this->utilEncoding = $utilEncoding;
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsResponseTransfer
     */
    public function execute(AfterpayAvailablePaymentMethodsRequestTransfer $requestTransfer): AfterpayAvailablePaymentMethodsResponseTransfer
    {
        $jsonRequest = $this->buildJsonRequestFromTransferObject($requestTransfer);

        try {
            $jsonResponse = $this->client->sendPost(
                $this->config->getAvailablePaymentMethodsApiEndpointUrl(),
                $jsonRequest
            );
        } catch (ApiHttpRequestException $apiHttpRequestException) {
            $this->logApiException($apiHttpRequestException);
            $jsonResponse = '[]';
        }

        return $this->buildAvailablePaymentMethodsResponseTransfer($jsonResponse);
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsResponseTransfer
     */
    protected function buildAvailablePaymentMethodsResponseTransfer(string $jsonResponse): AfterpayAvailablePaymentMethodsResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $responseTransfer = new AfterpayAvailablePaymentMethodsResponseTransfer();

        $riskCheckResultCode = $this->extractRiskCheckCode($jsonResponseArray);
        $customerNumber = $this->extractCustomerNumber($jsonResponseArray);

        $responseTransfer
            ->setCheckoutId($jsonResponseArray[AfterpayApiRequestConfig::TRANSACTION_CHECKOUT_ID] ?? null)
            ->setOutcome($jsonResponseArray[AfterpayApiRequestConfig::TRANSACTION_OUTCOME] ?? null)
            ->setCustomer($jsonResponseArray[AfterpayApiRequestConfig::CUSTOMER] ?? [])
            ->setCustomerNumber($customerNumber)
            ->setPaymentMethods($jsonResponseArray[AfterpayApiRequestConfig::PAYMENT_METHODS] ?? [])
            ->setRiskCheckResultCode($riskCheckResultCode);

        return $responseTransfer;
    }

    /**
     * @param array $jsonResponseArray
     *
     * @return string|null
     */
    protected function extractRiskCheckCode(array $jsonResponseArray): ?string
    {
        if (!isset($jsonResponseArray[AfterpayApiRequestConfig::ADDITIONAL_RESPONSE_INFO][AfterpayApiRequestConfig::RISK_CHECK_CODE])) {
            return null;
        }

        return $jsonResponseArray[AfterpayApiRequestConfig::ADDITIONAL_RESPONSE_INFO][AfterpayApiRequestConfig::RISK_CHECK_CODE];
    }

    /**
     * @param array $jsonResponseArray
     *
     * @return string|null
     */
    protected function extractCustomerNumber(array $jsonResponseArray): ?string
    {
        if (!isset($jsonResponseArray[AfterpayApiRequestConfig::CUSTOMER][AfterpayApiRequestConfig::CUSTOMER_NUMBER])) {
            return null;
        }

        return $jsonResponseArray[AfterpayApiRequestConfig::CUSTOMER][AfterpayApiRequestConfig::CUSTOMER_NUMBER];
    }
}
