<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsResponseTransfer;
use SprykerEco\Shared\Afterpay\AfterpayApiConstants;
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
    public function execute(AfterpayAvailablePaymentMethodsRequestTransfer $requestTransfer)
    {
        $jsonRequest = $this->buildJsonRequestFromTransferObject($requestTransfer);

        try {
            $jsonResponse = $this->client->sendPost(
                $this->config->getAvailablePaymentMethodsApiEndpointUrl(),
                $jsonRequest
            );
        } catch (ApiHttpRequestException $apiHttpRequestException) {
            // @todo do a proper error handling. Afterpay cam provide some more details about business errors
            // Make sure to get these messages, parse them into transfer objects and assign to API response transfer.
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
    protected function buildAvailablePaymentMethodsResponseTransfer($jsonResponse)
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $responseTransfer = new AfterpayAvailablePaymentMethodsResponseTransfer();

        $riskCheckResultCode = $this->extractRiskCheckCode($jsonResponseArray);
        $customerNumber = $this->extractCustomerNumber($jsonResponseArray);

        $responseTransfer
            ->setCheckoutId($jsonResponseArray[AfterpayApiConstants::TRANSACTION_CHECKOUT_ID] ?? null)
            ->setOutcome($jsonResponseArray[AfterpayApiConstants::TRANSACTION_OUTCOME] ?? null)
            ->setCustomer($jsonResponseArray[AfterpayApiConstants::CUSTOMER] ?? [])
            ->setCustomerNumber($customerNumber)
            ->setPaymentMethods($jsonResponseArray[AfterpayApiConstants::PAYMENT_METHODS] ?? [])
            ->setRiskCheckResultCode($riskCheckResultCode);

        return $responseTransfer;
    }

    /**
     * @param array $jsonResponseArray
     *
     * @return string|null
     */
    protected function extractRiskCheckCode($jsonResponseArray)
    {
        if (!isset(
            $jsonResponseArray[AfterpayApiConstants::ADDITIONAL_RESPONSE_INFO],
            $jsonResponseArray[AfterpayApiConstants::ADDITIONAL_RESPONSE_INFO][AfterpayApiConstants::RISK_CHECK_CODE]
        )
        ) {
            return null;
        }

        return $jsonResponseArray[AfterpayApiConstants::ADDITIONAL_RESPONSE_INFO][AfterpayApiConstants::RISK_CHECK_CODE];
    }

    /**
     * @param array $jsonResponseArray
     *
     * @return string|null
     */
    protected function extractCustomerNumber($jsonResponseArray)
    {
        $customerNumber = null;

        if (isset(
            $jsonResponseArray[AfterpayApiConstants::CUSTOMER],
            $jsonResponseArray[AfterpayApiConstants::CUSTOMER][AfterpayApiConstants::CUSTOMER_NUMBER]
        )
        ) {
            $customerNumber = $jsonResponseArray[AfterpayApiConstants::CUSTOMER][AfterpayApiConstants::CUSTOMER_NUMBER];
        }

        return $customerNumber;
    }
}
