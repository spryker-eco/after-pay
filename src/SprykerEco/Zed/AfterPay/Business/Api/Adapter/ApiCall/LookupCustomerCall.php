<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer;
use Generated\Shared\Transfer\AfterPayLookupAddressTransfer;
use Generated\Shared\Transfer\AfterPayUserProfileTransfer;
use SprykerEco\Shared\AfterPay\AfterPayApiRequestConfig;
use SprykerEco\Zed\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface;

class LookupCustomerCall extends AbstractApiCall implements LookupCustomerCallInterface
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
     * @param \Generated\Shared\Transfer\AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer
     */
    public function execute(AfterPayCustomerLookupRequestTransfer $customerLookupRequestTransfer): AfterPayCustomerLookupResponseTransfer
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
     * @return \Generated\Shared\Transfer\AfterPayCustomerLookupResponseTransfer
     */
    protected function buildLookupCustomerResponseTransfer(string $jsonResponse): AfterPayCustomerLookupResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $responseTransfer = new AfterPayCustomerLookupResponseTransfer();

        if (!isset($jsonResponseArray[AfterPayApiRequestConfig::USER_PROFILES])) {
            return $responseTransfer;
        }

        foreach ($jsonResponseArray[AfterPayApiRequestConfig::USER_PROFILES] as $userProfile) {
            $responseTransfer->addUserProfile(
                $this->buildUserProfileTransfer($userProfile)
            );
        }

        return $responseTransfer;
    }

    /**
     * @param array $userProfile
     *
     * @return \Generated\Shared\Transfer\AfterPayUserProfileTransfer
     */
    protected function buildUserProfileTransfer(array $userProfile): AfterPayUserProfileTransfer
    {
        $userProfileTransfer = new AfterPayUserProfileTransfer();

        $userProfileTransfer
            ->setFirstName($userProfile[AfterPayApiRequestConfig::USER_PROFILE_FIRST_NAME])
            ->setLastName($userProfile[AfterPayApiRequestConfig::USER_PROFILE_LAST_NAME])
            ->setMobileNumber($userProfile[AfterPayApiRequestConfig::USER_PROFILE_MOBILE_NUMBER])
            ->setEmail($userProfile[AfterPayApiRequestConfig::USER_PROFILE_EMAIL])
            ->setLanguageCode($userProfile[AfterPayApiRequestConfig::USER_PROFILE_LANGUAGE_CODE]);

        if (!isset($userProfile[AfterPayApiRequestConfig::USER_PROFILE_ADDRESS_LIST])) {
            return $userProfileTransfer;
        }

        foreach ($userProfile[AfterPayApiRequestConfig::USER_PROFILE_ADDRESS_LIST] as $userAddress) {
            $userProfileTransfer->addLookupAddress(
                $this->buildLookupAddressTransfer($userAddress)
            );
        }

        return $userProfileTransfer;
    }

    /**
     * @param array $userAddress
     *
     * @return \Generated\Shared\Transfer\AfterPayLookupAddressTransfer
     */
    protected function buildLookupAddressTransfer(array $userAddress): AfterPayLookupAddressTransfer
    {
        $lookupAddressTransfer = new AfterPayLookupAddressTransfer();

        $lookupAddressTransfer
            ->setStreet($userAddress[AfterPayApiRequestConfig::USER_PROFILE_ADDRESS_STREET])
            ->setStreet2($userAddress[AfterPayApiRequestConfig::USER_PROFILE_ADDRESS_STREET2])
            ->setStreet3($userAddress[AfterPayApiRequestConfig::USER_PROFILE_ADDRESS_STREET3])
            ->setStreet4($userAddress[AfterPayApiRequestConfig::USER_PROFILE_ADDRESS_STREET4])
            ->setStreetNumber($userAddress[AfterPayApiRequestConfig::USER_PROFILE_ADDRESS_STREET_NUMBER])
            ->setFlatNo($userAddress[AfterPayApiRequestConfig::USER_PROFILE_ADDRESS_FLAT])
            ->setEntrance($userAddress[AfterPayApiRequestConfig::USER_PROFILE_ADDRESS_ENTRANCE])
            ->setCity($userAddress[AfterPayApiRequestConfig::USER_PROFILE_ADDRESS_CITY])
            ->setPostalCode($userAddress[AfterPayApiRequestConfig::USER_PROFILE_ADDRESS_POSTAL_CODE])
            ->setCountry($userAddress[AfterPayApiRequestConfig::USER_PROFILE_ADDRESS_COUNTRY])
            ->setCountryCode($userAddress[AfterPayApiRequestConfig::USER_PROFILE_ADDRESS_COUNTRY_CODE]);

        return $lookupAddressTransfer;
    }
}
