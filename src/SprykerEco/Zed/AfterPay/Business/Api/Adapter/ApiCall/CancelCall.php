<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Generated\Shared\Transfer\AfterPayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterPayCancelResponseTransfer;
use SprykerEco\Shared\AfterPay\AfterPayApiRequestConfig;
use SprykerEco\Shared\AfterPay\AfterPayConfig as SharedAfterPayConfig;
use SprykerEco\Zed\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface;

class CancelCall extends AbstractApiCall implements CancelCallInterface
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
     * @var \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface
     */
    protected $money;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface $client
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface $transferConverter
     * @param \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface $utilEncoding
     * @param \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface $money
     * @param \SprykerEco\Zed\AfterPay\AfterPayConfig $config
     */
    public function __construct(
        ClientInterface $client,
        TransferToCamelCaseArrayConverterInterface $transferConverter,
        AfterPayToUtilEncodingServiceInterface $utilEncoding,
        AfterPayToMoneyFacadeInterface $money,
        AfterPayConfig $config
    ) {
        $this->client = $client;
        $this->transferConverter = $transferConverter;
        $this->utilEncoding = $utilEncoding;
        $this->config = $config;
        $this->money = $money;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $requestTransfer
     *
     * @throws \SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException
     *
     * @return \Generated\Shared\Transfer\AfterPayCancelResponseTransfer
     */
    public function execute(AfterPayCancelRequestTransfer $requestTransfer): AfterPayCancelResponseTransfer
    {
        $preparedRequestTransfer = $this->prepareRequestTransferToBuildJsonRequest($requestTransfer);
        $jsonRequest = $this->buildJsonRequestFromTransferObject($preparedRequestTransfer);

        try {
            $jsonResponse = $this->client->sendPost(
                $this->getCancelEndpointUrl($requestTransfer),
                $jsonRequest,
            );
        } catch (ApiHttpRequestException $apiHttpRequestException) {
            $this->logApiException($apiHttpRequestException);

            throw $apiHttpRequestException;
        }

        return $this->buildResponseTransfer($jsonResponse);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $requestTransfer
     *
     * @return string
     */
    protected function getCancelEndpointUrl(AfterPayCancelRequestTransfer $requestTransfer): string
    {
        return $this->config->getCancelApiEndpointUrl(
            $requestTransfer->getCancellationDetails()->getNumber(),
        );
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterPayCancelResponseTransfer
     */
    protected function buildResponseTransfer(string $jsonResponse): AfterPayCancelResponseTransfer
    {
        $apiResponseTransfer = $this->buildApiResponseTransfer($jsonResponse);
        $cancelResponseTransfer = $this->buildCancelResponseTransfer($jsonResponse);

        $cancelResponseTransfer->setApiResponse($apiResponseTransfer);

        return $cancelResponseTransfer;
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterPayCancelResponseTransfer
     */
    protected function buildCancelResponseTransfer(string $jsonResponse): AfterPayCancelResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        return (new AfterPayCancelResponseTransfer())
            ->setTotalCapturedAmount(
                $this->money->convertDecimalToInteger(
                    $jsonResponseArray[AfterPayApiRequestConfig::CANCEL_CAPTURED_AMOUNT],
                ),
            )
            ->setTotalAuthorizedAmount(
                $this->money->convertDecimalToInteger(
                    $jsonResponseArray[AfterPayApiRequestConfig::CANCEL_AUTHORIZED_AMOUNT],
                ),
            );
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterPayApiResponseTransfer
     */
    protected function buildApiResponseTransfer(string $jsonResponse): AfterPayApiResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $outcome = isset($jsonResponseArray[AfterPayApiRequestConfig::CANCEL_AUTHORIZED_AMOUNT])
            ? SharedAfterPayConfig::API_TRANSACTION_OUTCOME_ACCEPTED
            : SharedAfterPayConfig::API_TRANSACTION_OUTCOME_REJECTED;

        return (new AfterPayApiResponseTransfer())
            ->setOutcome($outcome)
            ->setResponsePayload($jsonResponse);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCancelRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCancelRequestTransfer
     */
    protected function prepareRequestTransferToBuildJsonRequest(AfterPayCancelRequestTransfer $requestTransfer): AfterPayCancelRequestTransfer
    {
        return (new AfterPayCancelRequestTransfer())
            ->setCancellationDetails($requestTransfer->getCancellationDetails());
    }
}
