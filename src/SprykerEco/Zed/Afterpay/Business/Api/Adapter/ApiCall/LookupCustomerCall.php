<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer;
use Generated\Shared\Transfer\AfterpayLookupAddressTransfer;
use Generated\Shared\Transfer\AfterpayUserProfileTransfer;
use SprykerEco\Shared\Afterpay\AfterpayApiRequestConfig;
use SprykerEco\Zed\Afterpay\AfterpayConfig;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface;

class LookupCustomerCall extends AbstractApiCall implements LookupCustomerCallInterface
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
     * @param \SprykerEco\Zed\Afterpay\AfterpayConfig $config
     */
    public function __construct(
        ClientInterface $client,
        TransferToCamelCaseArrayConverterInterface $transferConverter,
        AfterpayToUtilEncodingInterface $utilEncoding,
        AfterpayConfig $config
    ) {
        $this->client = $client;
        $this->utilEncoding = $utilEncoding;
        $this->config = $config;
        $this->transferConverter = $transferConverter;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer
     */
    public function execute(AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterpayCustomerLookupResponseTransfer
    {
        $jsonRequest = $this->buildJsonRequestFromTransferObject($customerLookupRequestTransfer);

        try {
            $jsonResponse = $this->client->sendPost(
                $this->config->getLookupCustomerApiEndpointUrl(),
                $jsonRequest
            );
        } catch (ApiHttpRequestException $apiHttpRequestException) {
            $this->logApiException($apiHttpRequestException);
            $jsonResponse = '[]';
        }

        return $this->buildLookupCustomerResponseTransfer($jsonResponse);
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer
     */
    protected function buildLookupCustomerResponseTransfer(string $jsonResponse): AfterpayCustomerLookupResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $responseTransfer = new AfterpayCustomerLookupResponseTransfer();

        if (!isset($jsonResponseArray[AfterpayApiRequestConfig::USER_PROFILES])) {
            return $responseTransfer;
        }

        foreach ($jsonResponseArray[AfterpayApiRequestConfig::USER_PROFILES] as $userProfile) {
            $responseTransfer->addUserProfile(
                $this->buildUserProfileTransfer($userProfile)
            );
        }

        return $responseTransfer;
    }

    /**
     * @param array $userProfile
     *
     * @return \Generated\Shared\Transfer\AfterpayUserProfileTransfer
     */
    protected function buildUserProfileTransfer(array $userProfile): AfterpayUserProfileTransfer
    {
        $userProfileTransfer = new AfterpayUserProfileTransfer();

        $userProfileTransfer
            ->setFirstName($userProfile[AfterpayApiRequestConfig::USER_PROFILE_FIRST_NAME])
            ->setLastName($userProfile[AfterpayApiRequestConfig::USER_PROFILE_LAST_NAME])
            ->setMobileNumber($userProfile[AfterpayApiRequestConfig::USER_PROFILE_MOBILE_NUMBER])
            ->setEmail($userProfile[AfterpayApiRequestConfig::USER_PROFILE_EMAIL])
            ->setLanguageCode($userProfile[AfterpayApiRequestConfig::USER_PROFILE_LANGUAGE_CODE]);

        if (!isset($userProfile[AfterpayApiRequestConfig::USER_PROFILE_ADDRESS_LIST])) {
            return $userProfileTransfer;
        }

        foreach ($userProfile[AfterpayApiRequestConfig::USER_PROFILE_ADDRESS_LIST] as $userAddress) {
            $userProfileTransfer->addLookupAddress(
                $this->buildLookupAddressTransfer($userAddress)
            );
        }

        return $userProfileTransfer;
    }

    /**
     * @param array $userAddress
     *
     * @return \Generated\Shared\Transfer\AfterpayLookupAddressTransfer
     */
    protected function buildLookupAddressTransfer(array $userAddress): AfterpayLookupAddressTransfer
    {
        $lookupAddressTransfer = new AfterpayLookupAddressTransfer();

        $lookupAddressTransfer
            ->setStreet($userAddress[AfterpayApiRequestConfig::USER_PROFILE_ADDRESS_STREET])
            ->setStreet2($userAddress[AfterpayApiRequestConfig::USER_PROFILE_ADDRESS_STREET2])
            ->setStreet3($userAddress[AfterpayApiRequestConfig::USER_PROFILE_ADDRESS_STREET3])
            ->setStreet4($userAddress[AfterpayApiRequestConfig::USER_PROFILE_ADDRESS_STREET4])
            ->setStreetNumber($userAddress[AfterpayApiRequestConfig::USER_PROFILE_ADDRESS_STREET_NUMBER])
            ->setFlatNo($userAddress[AfterpayApiRequestConfig::USER_PROFILE_ADDRESS_FLAT])
            ->setEntrance($userAddress[AfterpayApiRequestConfig::USER_PROFILE_ADDRESS_ENTRANCE])
            ->setCity($userAddress[AfterpayApiRequestConfig::USER_PROFILE_ADDRESS_CITY])
            ->setPostalCode($userAddress[AfterpayApiRequestConfig::USER_PROFILE_ADDRESS_POSTAL_CODE])
            ->setCountry($userAddress[AfterpayApiRequestConfig::USER_PROFILE_ADDRESS_COUNTRY])
            ->setCountryCode($userAddress[AfterpayApiRequestConfig::USER_PROFILE_ADDRESS_COUNTRY_CODE]);

        return $lookupAddressTransfer;
    }
}
