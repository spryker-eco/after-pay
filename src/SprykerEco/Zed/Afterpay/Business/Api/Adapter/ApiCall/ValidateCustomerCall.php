<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayRequestAddressTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer;
use SprykerEco\Shared\Afterpay\AfterpayApiConstants;
use SprykerEco\Zed\Afterpay\AfterpayConfig;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilTextInterface;

class ValidateCustomerCall extends AbstractApiCall implements ValidateCustomerCallInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface
     */
    protected $client;

    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilTextInterface
     */
    protected $utilText;

    /**
     * @var \SprykerEco\Zed\Afterpay\AfterpayConfig
     */
    protected $config;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface $client
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface $transferConverter
     * @param \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface $utilEncoding
     * @param \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilTextInterface $utilText
     * @param \SprykerEco\Zed\Afterpay\AfterpayConfig $config
     */
    public function __construct(
        ClientInterface $client,
        TransferToCamelCaseArrayConverterInterface $transferConverter,
        AfterpayToUtilEncodingInterface $utilEncoding,
        AfterpayToUtilTextInterface $utilText,
        AfterpayConfig $config
    ) {
        $this->client = $client;
        $this->utilEncoding = $utilEncoding;
        $this->utilText = $utilText;
        $this->config = $config;
        $this->transferConverter = $transferConverter;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer
     */
    public function execute(AfterpayValidateCustomerRequestTransfer $validateCustomerRequestTransfer)
    {
        $jsonRequest = $this->buildJsonRequestFromTransferObject($validateCustomerRequestTransfer);

        try {
            $jsonResponse = $this->client->sendPost(
                $this->config->getValidateAddressApiEndpointUrl(),
                $jsonRequest
            );
        } catch (ApiHttpRequestException $apiHttpRequestException) {
            $this->logApiException($apiHttpRequestException);
            $jsonResponse = '[]';
        }

        return $this->buildValidateCustomerResponseTransfer($jsonResponse);
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterpayValidateCustomerResponseTransfer
     */
    protected function buildValidateCustomerResponseTransfer($jsonResponse)
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $responseTransfer = new AfterpayValidateCustomerResponseTransfer();

        $responseTransfer
            ->setIsValid($jsonResponseArray[AfterpayApiConstants::VALIDATE_ADDRESS_IS_VALID] ?? false)
            ->setCorrectedAddress(
                $this->parseCorrectedAddress($jsonResponseArray)
            )
            ->setResponsePayload($jsonResponse);

        return $responseTransfer;
    }

    /**
     * @param array $jsonResponseArray
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestAddressTransfer
     */
    protected function parseCorrectedAddress(array $jsonResponseArray)
    {
        $correctedAddressTransfer = new AfterpayRequestAddressTransfer();
        $correctedAddressArray = $this->extractAddressDataWithUnderscoreKeys($jsonResponseArray);

        $correctedAddressTransfer->fromArray($correctedAddressArray, true);

        return $correctedAddressTransfer;
    }

    /**
     * @param array $jsonResponseArray
     *
     * @return array
     */
    protected function extractAddressDataWithUnderscoreKeys(array $jsonResponseArray)
    {
        if (!isset($jsonResponseArray[AfterpayApiConstants::CORRECTED_ADDRESS])) {
            return [];
        }

        $addressWithUnderscoreKeys = [];
        foreach ($jsonResponseArray[AfterpayApiConstants::CORRECTED_ADDRESS] as $key => $value) {
            $keyWithUnderscore = $this->utilText->camelCaseToSeparator($key, '_');
            $addressWithUnderscoreKeys[$keyWithUnderscore] = $value;
        }

        return $addressWithUnderscoreKeys;
    }
}
