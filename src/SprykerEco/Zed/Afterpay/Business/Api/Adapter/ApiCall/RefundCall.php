<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayApiResponseTransfer;
use Generated\Shared\Transfer\AfterpayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterpayRefundResponseTransfer;
use SprykerEco\Shared\Afterpay\AfterpayApiConstants;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\AfterpayConfig;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface;

class RefundCall extends AbstractApiCall implements RefundCallInterface
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
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $requestTransfer
     *
     * @throws \SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException
     *
     * @return \Generated\Shared\Transfer\AfterpayRefundResponseTransfer
     */
    public function execute(AfterpayRefundRequestTransfer $requestTransfer)
    {
        $jsonRequest = $this->buildJsonRequestFromTransferObject($requestTransfer);
        try {
            $jsonResponse = $this->client->sendPost(
                $this->getRefundEndpointUrl($requestTransfer),
                $jsonRequest
            );
        } catch (ApiHttpRequestException $apiHttpRequestException) {
            $this->logApiException($apiHttpRequestException);
            throw $apiHttpRequestException;
        }

        return $this->buildResponseTransfer($jsonResponse);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayRefundRequestTransfer $requestTransfer
     *
     * @return string
     */
    protected function getRefundEndpointUrl(AfterpayRefundRequestTransfer $requestTransfer)
    {
        return $this->config->getRefundApiEndpointUrl(
            $requestTransfer->getOrderNumber()
        );
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterpayRefundResponseTransfer
     */
    protected function buildResponseTransfer($jsonResponse)
    {
        $apiResponseTransfer = $this->buildApiResponseTransfer($jsonResponse);
        $refundResponseTransfer = $this->buildRefundResponseTransfer($jsonResponse);

        $refundResponseTransfer->setApiResponse($apiResponseTransfer);

        return $refundResponseTransfer;
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterpayRefundResponseTransfer
     */
    protected function buildRefundResponseTransfer($jsonResponse)
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $refundResponseTransfer = new AfterpayRefundResponseTransfer();

        $refundResponseTransfer
            ->setTotalCapturedAmount(
                $this->money->convertDecimalToInteger(
                    $jsonResponseArray[AfterpayApiConstants::REFUND_TOTAL_CAPTURED_AMOUNT]
                )
            )
            ->setTotalAuthorizedAmount(
                $this->money->convertDecimalToInteger(
                    $jsonResponseArray[AfterpayApiConstants::REFUND_TOTAL_AUTHORIZE_AMOUNT]
                )
            );

        return $refundResponseTransfer;
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterpayApiResponseTransfer
     */
    protected function buildApiResponseTransfer($jsonResponse)
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $apiResponseTransfer = new AfterpayApiResponseTransfer();

        $outcome = $jsonResponseArray[AfterpayApiConstants::REFUND_TOTAL_CAPTURED_AMOUNT]
            ? AfterpayConstants::API_TRANSACTION_OUTCOME_ACCEPTED
            : AfterpayConstants::API_TRANSACTION_OUTCOME_REJECTED;

        $apiResponseTransfer
            ->setOutcome($outcome)
            ->setResponsePayload($jsonResponse);

        return $apiResponseTransfer;
    }
}
