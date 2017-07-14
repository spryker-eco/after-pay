<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayCustomerLookupRequestTransfer;
use Generated\Shared\Transfer\AfterpayCustomerLookupResponseTransfer;
use Generated\Shared\Transfer\AfterpayLookupAddressTransfer;
use Generated\Shared\Transfer\AfterpayUserProfileTransfer;
use SprykerEco\Shared\Afterpay\AfterpayApiConstants;
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
    public function execute(AfterpayCustomerLookupRequestTransfer $customerLookupRequestTransfer)
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
    protected function buildLookupCustomerResponseTransfer($jsonResponse)
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $responseTransfer = new AfterpayCustomerLookupResponseTransfer();

        if (!isset($jsonResponseArray[AfterpayApiConstants::USER_PROFILES])) {
            return $responseTransfer;
        }

        foreach ($jsonResponseArray[AfterpayApiConstants::USER_PROFILES] as $userProfile) {
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
    protected function buildUserProfileTransfer(array $userProfile)
    {
        $userProfileTransfer = new AfterpayUserProfileTransfer();

        $userProfileTransfer
            ->setFirstName($userProfile[AfterpayApiConstants::USER_PROFILE_FIRST_NAME])
            ->setLastName($userProfile[AfterpayApiConstants::USER_PROFILE_LAST_NAME])
            ->setMobileNumber($userProfile[AfterpayApiConstants::USER_PROFILE_MOBILE_NUMBER])
            ->setEmail($userProfile[AfterpayApiConstants::USER_PROFILE_EMAIL])
            ->setLanguageCode($userProfile[AfterpayApiConstants::USER_PROFILE_LANGUAGE_CODE]);

        if (!isset($userProfile[AfterpayApiConstants::USER_PROFILE_ADDRESS_LIST])) {
            return $userProfileTransfer;
        }

        foreach ($userProfile[AfterpayApiConstants::USER_PROFILE_ADDRESS_LIST] as $userAddress) {
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
    protected function buildLookupAddressTransfer(array $userAddress)
    {
        $lookupAddressTransfer = new AfterpayLookupAddressTransfer();

        $lookupAddressTransfer
            ->setStreet($userAddress[AfterpayApiConstants::USER_PROFILE_ADDRESS_STREET])
            ->setStreet2($userAddress[AfterpayApiConstants::USER_PROFILE_ADDRESS_STREET2])
            ->setStreet3($userAddress[AfterpayApiConstants::USER_PROFILE_ADDRESS_STREET3])
            ->setStreet4($userAddress[AfterpayApiConstants::USER_PROFILE_ADDRESS_STREET4])
            ->setStreetNumber($userAddress[AfterpayApiConstants::USER_PROFILE_ADDRESS_STREET_NUMBER])
            ->setFlatNo($userAddress[AfterpayApiConstants::USER_PROFILE_ADDRESS_FLAT])
            ->setEntrance($userAddress[AfterpayApiConstants::USER_PROFILE_ADDRESS_ENTRANCE])
            ->setCity($userAddress[AfterpayApiConstants::USER_PROFILE_ADDRESS_CITY])
            ->setPostalCode($userAddress[AfterpayApiConstants::USER_PROFILE_ADDRESS_POSTAL_CODE])
            ->setCountry($userAddress[AfterpayApiConstants::USER_PROFILE_ADDRESS_COUNTRY])
            ->setCountryCode($userAddress[AfterpayApiConstants::USER_PROFILE_ADDRESS_COUNTRY_CODE]);

        return $lookupAddressTransfer;
    }

}
