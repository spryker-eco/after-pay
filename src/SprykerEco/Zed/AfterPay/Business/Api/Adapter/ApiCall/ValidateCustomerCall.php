<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterPayRequestAddressTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer;
use Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer;
use SprykerEco\Shared\AfterPay\AfterPayApiRequestConfig;
use SprykerEco\Zed\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilTextServiceInterface;

class ValidateCustomerCall extends AbstractApiCall implements ValidateCustomerCallInterface
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
     * @param \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilTextServiceInterface $utilText
     * @param \SprykerEco\Zed\AfterPay\AfterPayConfig $config
     */
    public function __construct(
        ClientInterface $client,
        TransferToCamelCaseArrayConverterInterface $transferConverter,
        AfterPayToUtilEncodingServiceInterface $utilEncoding,
        AfterPayToUtilTextServiceInterface $utilText,
        AfterPayConfig $config
    ) {
        $this->client = $client;
        $this->utilEncoding = $utilEncoding;
        $this->utilText = $utilText;
        $this->config = $config;
        $this->transferConverter = $transferConverter;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer
     */
    public function execute(AfterPayValidateCustomerRequestTransfer $validateCustomerRequestTransfer): AfterPayValidateCustomerResponseTransfer
    {
        $jsonRequest = $this->buildJsonRequestFromTransferObject($validateCustomerRequestTransfer);

        try {
            $jsonResponse = $this->client->sendPost(
                $this->config->getValidateAddressApiEndpointUrl(),
                $jsonRequest,
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
     * @return \Generated\Shared\Transfer\AfterPayValidateCustomerResponseTransfer
     */
    protected function buildValidateCustomerResponseTransfer(string $jsonResponse): AfterPayValidateCustomerResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        return (new AfterPayValidateCustomerResponseTransfer())
            ->setIsValid($jsonResponseArray[AfterPayApiRequestConfig::VALIDATE_ADDRESS_IS_VALID] ?? false)
            ->setCorrectedAddress(
                $this->parseCorrectedAddress($jsonResponseArray),
            )
            ->setResponsePayload($jsonResponse);
    }

    /**
     * @param array $jsonResponseArray
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestAddressTransfer
     */
    protected function parseCorrectedAddress(array $jsonResponseArray): AfterPayRequestAddressTransfer
    {
        $correctedAddressArray = $this->extractAddressDataWithUnderscoreKeys($jsonResponseArray);

        return (new AfterPayRequestAddressTransfer())
            ->fromArray($correctedAddressArray, true);
    }

    /**
     * @param array $jsonResponseArray
     *
     * @return array
     */
    protected function extractAddressDataWithUnderscoreKeys(array $jsonResponseArray): array
    {
        if (!isset($jsonResponseArray[AfterPayApiRequestConfig::CORRECTED_ADDRESS])) {
            return [];
        }

        $addressWithUnderscoreKeys = [];
        foreach ($jsonResponseArray[AfterPayApiRequestConfig::CORRECTED_ADDRESS] as $key => $value) {
            $keyWithUnderscore = $this->utilText->camelCaseToSeparator($key, '_');
            $addressWithUnderscoreKeys[$keyWithUnderscore] = $value;
        }

        return $addressWithUnderscoreKeys;
    }
}
