<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use ArrayObject;
use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsResponseTransfer;
use Generated\Shared\Transfer\AfterPayRiskCheckMessageTransfer;
use SprykerEco\Shared\AfterPay\AfterPayApiRequestConfig;
use SprykerEco\Zed\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface;

class AvailablePaymentMethodsCall extends AbstractApiCall implements AvailablePaymentMethodsCallInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface
     */
    protected $client;

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
        $this->transferConverter = $transferConverter;
        $this->utilEncoding = $utilEncoding;
        $this->config = $config;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsResponseTransfer
     */
    public function execute(AfterPayAvailablePaymentMethodsRequestTransfer $requestTransfer): AfterPayAvailablePaymentMethodsResponseTransfer
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
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsResponseTransfer
     */
    protected function buildAvailablePaymentMethodsResponseTransfer(string $jsonResponse): AfterPayAvailablePaymentMethodsResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);
        $riskCheckResultCode = $this->extractRiskCheckCode($jsonResponseArray);
        $customerNumber = $this->extractCustomerNumber($jsonResponseArray);

        return (new AfterPayAvailablePaymentMethodsResponseTransfer())
            ->setCheckoutId($jsonResponseArray[AfterPayApiRequestConfig::TRANSACTION_CHECKOUT_ID] ?? null)
            ->setOutcome($jsonResponseArray[AfterPayApiRequestConfig::TRANSACTION_OUTCOME] ?? null)
            ->setCustomer($jsonResponseArray[AfterPayApiRequestConfig::CUSTOMER] ?? [])
            ->setCustomerNumber($customerNumber)
            ->setPaymentMethods($jsonResponseArray[AfterPayApiRequestConfig::PAYMENT_METHODS] ?? [])
            ->setRiskCheckResultCode($riskCheckResultCode)
            ->setRiskCheckMessages($this->extractRiskCheckMessages($jsonResponseArray));
    }

    /**
     * @param array $jsonResponseArray
     *
     * @return string|null
     */
    protected function extractRiskCheckCode(array $jsonResponseArray): ?string
    {
        if (!isset($jsonResponseArray[AfterPayApiRequestConfig::ADDITIONAL_RESPONSE_INFO][AfterPayApiRequestConfig::RISK_CHECK_CODE])) {
            return null;
        }

        return $jsonResponseArray[AfterPayApiRequestConfig::ADDITIONAL_RESPONSE_INFO][AfterPayApiRequestConfig::RISK_CHECK_CODE];
    }

    /**
     * @param array $jsonResponseArray
     *
     * @return string|null
     */
    protected function extractCustomerNumber(array $jsonResponseArray): ?string
    {
        if (!isset($jsonResponseArray[AfterPayApiRequestConfig::CUSTOMER][AfterPayApiRequestConfig::CUSTOMER_NUMBER])) {
            return null;
        }

        return $jsonResponseArray[AfterPayApiRequestConfig::CUSTOMER][AfterPayApiRequestConfig::CUSTOMER_NUMBER];
    }

    /**
     * @param array $jsonResponseArray
     *
     * @return \ArrayObject|\Generated\Shared\Transfer\AfterPayRiskCheckMessageTransfer[]
     */
    protected function extractRiskCheckMessages(array $jsonResponseArray): ArrayObject
    {
        $riskCheckMessages = new ArrayObject();

        if (!isset($jsonResponseArray[AfterPayApiRequestConfig::RISK_CHECK_MESSAGES])) {
            return $riskCheckMessages;
        }

        foreach ($jsonResponseArray[AfterPayApiRequestConfig::RISK_CHECK_MESSAGES] as $riskMessage) {
            $riskCheckMessages[] = (new AfterPayRiskCheckMessageTransfer())
                ->setMessage($riskMessage[AfterPayApiRequestConfig::RISK_CHECK_MESSAGE_MESSAGE])
                ->setCustomerFacingMessage($riskMessage[AfterPayApiRequestConfig::RISK_CHECK_MESSAGE_CUSTOMER_FACING_MESSAGE])
                ->setType($riskMessage[AfterPayApiRequestConfig::RISK_CHECK_MESSAGE_TYPE])
                ->setCode($riskMessage[AfterPayApiRequestConfig::RISK_CHECK_MESSAGE_CODE]);
        }

        return $riskCheckMessages;
    }
}
