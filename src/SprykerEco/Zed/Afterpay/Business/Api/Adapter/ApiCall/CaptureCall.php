<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayApiResponseTransfer;
use Generated\Shared\Transfer\AfterpayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterpayCaptureResponseTransfer;
use SprykerEco\Shared\Afterpay\AfterpayApiRequestConfig;
use SprykerEco\Shared\Afterpay\AfterpayConfig as AfterpayConfig1;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\AfterpayConfig;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface;

class CaptureCall extends AbstractApiCall implements CaptureCallInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface
     */
    protected $client;

    /**
     * @var \SprykerEco\Zed\Afterpay\AfterpayConfig
     */
    protected $config;

    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface
     */
    private $money;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface $client
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface $transferConverter
     * @param \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface $utilEncoding
     * @param \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface $money
     * @param \SprykerEco\Zed\Afterpay\AfterpayConfig $config
     */
    public function __construct(
        ClientInterface $client,
        TransferToCamelCaseArrayConverterInterface $transferConverter,
        AfterpayToUtilEncodingInterface $utilEncoding,
        AfterpayToMoneyInterface $money,
        AfterpayConfig $config
    ) {
        $this->client = $client;
        $this->transferConverter = $transferConverter;
        $this->utilEncoding = $utilEncoding;
        $this->config = $config;
        $this->money = $money;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $requestTransfer
     *
     * @throws \SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureResponseTransfer
     */
    public function execute(AfterpayCaptureRequestTransfer $requestTransfer): AfterpayCaptureResponseTransfer
    {
        $jsonRequest = $this->buildJsonRequestFromTransferObject($requestTransfer);
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
     * @param \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer $requestTransfer
     *
     * @return string
     */
    protected function getCaptureEndpointUrl(AfterpayCaptureRequestTransfer $requestTransfer): string
    {
        return $this->config->getCaptureApiEndpointUrl(
            $requestTransfer->getOrderDetails()->getNumber()
        );
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureResponseTransfer
     */
    protected function buildResponseTransfer(string $jsonResponse): AfterpayCaptureResponseTransfer
    {
        $apiResponseTransfer = $this->buildApiResponseTransfer($jsonResponse);
        $captureResponseTransfer = $this->buildCaptureResponseTransfer($jsonResponse);

        $captureResponseTransfer->setApiResponse($apiResponseTransfer);

        return $captureResponseTransfer;
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureResponseTransfer
     */
    protected function buildCaptureResponseTransfer(string $jsonResponse): AfterpayCaptureResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $captureResponseTransfer = new AfterpayCaptureResponseTransfer();

        $captureResponseTransfer
            ->setCapturedAmount(
                $this->money->convertDecimalToInteger(
                    $jsonResponseArray[AfterpayApiRequestConfig::CAPTURE_CAPTURED_AMOUNT]
                )
            )
            ->setAuthorizedAmount(
                $this->money->convertDecimalToInteger(
                    $jsonResponseArray[AfterpayApiRequestConfig::CAPTURE_AUTHORIZED_AMOUNT]
                )
            )
            ->setRemainingAuthorizedAmount(
                $this->money->convertDecimalToInteger(
                    $jsonResponseArray[AfterpayApiRequestConfig::CAPTURE_REMAINING_AUTHORIZED_AMOUNT]
                )
            )
            ->setCaptureNumber(
                $jsonResponseArray[AfterpayApiRequestConfig::CAPTURE_CAPTURE_NUMBER]
            );

        return $captureResponseTransfer;
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterpayApiResponseTransfer
     */
    protected function buildApiResponseTransfer(string $jsonResponse): AfterpayApiResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $apiResponseTransfer = new AfterpayApiResponseTransfer();

        $outcome = $jsonResponseArray[AfterpayApiRequestConfig::CAPTURE_CAPTURE_NUMBER]
            ? AfterpayConfig1::API_TRANSACTION_OUTCOME_ACCEPTED
            : AfterpayConfig1::API_TRANSACTION_OUTCOME_REJECTED;

        $apiResponseTransfer
            ->setOutcome($outcome)
            ->setResponsePayload($jsonResponse);

        return $apiResponseTransfer;
    }
}
