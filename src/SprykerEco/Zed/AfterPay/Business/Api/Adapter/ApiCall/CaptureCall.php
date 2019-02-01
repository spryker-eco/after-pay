<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterPayApiResponseTransfer;
use Generated\Shared\Transfer\AfterPayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterPayCaptureResponseTransfer;
use SprykerEco\Shared\AfterPay\AfterPayApiRequestConfig;
use SprykerEco\Shared\AfterPay\AfterPayConfig as SharedAfterPayConfig;
use SprykerEco\Zed\AfterPay\AfterPayConfig;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeInterface;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface;

class CaptureCall extends AbstractApiCall implements CaptureCallInterface
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
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $requestTransfer
     *
     * @throws \SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureResponseTransfer
     */
    public function execute(AfterPayCaptureRequestTransfer $requestTransfer): AfterPayCaptureResponseTransfer
    {
        $preparedRequestTransfer = $this->prepareRequestTransferToBuildJsonRequest($requestTransfer);
        $jsonRequest = $this->buildJsonRequestFromTransferObject($preparedRequestTransfer);

        try {
            $jsonResponse = $this->client->sendPost(
                $this->getCaptureEndpointUrl($requestTransfer),
                $jsonRequest
            );
        } catch (ApiHttpRequestException $apiHttpRequestException) {
            $this->logApiException($apiHttpRequestException);
            throw $apiHttpRequestException;
        }

        return $this->buildResponseTransfer($jsonResponse);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $requestTransfer
     *
     * @return string
     */
    protected function getCaptureEndpointUrl(AfterPayCaptureRequestTransfer $requestTransfer): string
    {
        return $this->config->getCaptureApiEndpointUrl(
            $requestTransfer->getOrderDetails()->getNumber()
        );
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureResponseTransfer
     */
    protected function buildResponseTransfer(string $jsonResponse): AfterPayCaptureResponseTransfer
    {
        $apiResponseTransfer = $this->buildApiResponseTransfer($jsonResponse);
        $captureResponseTransfer = $this->buildCaptureResponseTransfer($jsonResponse);

        $captureResponseTransfer->setApiResponse($apiResponseTransfer);

        return $captureResponseTransfer;
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureResponseTransfer
     */
    protected function buildCaptureResponseTransfer(string $jsonResponse): AfterPayCaptureResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $captureResponseTransfer = new AfterPayCaptureResponseTransfer();

        $captureResponseTransfer
            ->setCapturedAmount(
                $this->money->convertDecimalToInteger(
                    $jsonResponseArray[AfterPayApiRequestConfig::CAPTURE_CAPTURED_AMOUNT]
                )
            )
            ->setAuthorizedAmount(
                $this->money->convertDecimalToInteger(
                    $jsonResponseArray[AfterPayApiRequestConfig::CAPTURE_AUTHORIZED_AMOUNT]
                )
            )
            ->setRemainingAuthorizedAmount(
                $this->money->convertDecimalToInteger(
                    $jsonResponseArray[AfterPayApiRequestConfig::CAPTURE_REMAINING_AUTHORIZED_AMOUNT]
                )
            )
            ->setCaptureNumber(
                $jsonResponseArray[AfterPayApiRequestConfig::CAPTURE_CAPTURE_NUMBER]
            );

        return $captureResponseTransfer;
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterPayApiResponseTransfer
     */
    protected function buildApiResponseTransfer(string $jsonResponse): AfterPayApiResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $apiResponseTransfer = new AfterPayApiResponseTransfer();

        $outcome = $jsonResponseArray[AfterPayApiRequestConfig::CAPTURE_CAPTURE_NUMBER]
            ? SharedAfterPayConfig::API_TRANSACTION_OUTCOME_ACCEPTED
            : SharedAfterPayConfig::API_TRANSACTION_OUTCOME_REJECTED;

        $apiResponseTransfer
            ->setOutcome($outcome)
            ->setResponsePayload($jsonResponse);

        return $apiResponseTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer
     */
    protected function prepareRequestTransferToBuildJsonRequest(AfterPayCaptureRequestTransfer $requestTransfer): AfterPayCaptureRequestTransfer
    {
        return (new AfterPayCaptureRequestTransfer())
            ->setOrderDetails($requestTransfer->getOrderDetails());
    }
}
