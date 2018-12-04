<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\ApiCall;

use Generated\Shared\Transfer\AfterpayApiResponseTransfer;
use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterpayCancelResponseTransfer;
use SprykerEco\Shared\Afterpay\AfterpayApiRequestConfig;
use SprykerEco\Shared\Afterpay\AfterpayConfig as AfterpayConfig1;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\AfterpayConfig;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Client\ClientInterface;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface;
use SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyInterface;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingInterface;

class CancelCall extends AbstractApiCall implements CancelCallInterface
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
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $requestTransfer
     *
     * @throws \SprykerEco\Zed\Afterpay\Business\Exception\ApiHttpRequestException
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelResponseTransfer
     */
    public function execute(AfterpayCancelRequestTransfer $requestTransfer): AfterpayCancelResponseTransfer
    {
        $jsonRequest = $this->buildJsonRequestFromTransferObject($requestTransfer);
        try {
            $jsonResponse = $this->client->sendPost(
                $this->getCancelEndpointUrl($requestTransfer),
                $jsonRequest
            );
        } catch (ApiHttpRequestException $apiHttpRequestException) {
            $this->logApiException($apiHttpRequestException);
            throw $apiHttpRequestException;
        }

        return $this->buildResponseTransfer($jsonResponse);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCancelRequestTransfer $requestTransfer
     *
     * @return string
     */
    protected function getCancelEndpointUrl(AfterpayCancelRequestTransfer $requestTransfer): string
    {
        return $this->config->getCancelApiEndpointUrl(
            $requestTransfer->getCancellationDetails()->getNumber()
        );
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelResponseTransfer
     */
    protected function buildResponseTransfer(string $jsonResponse): AfterpayCancelResponseTransfer
    {
        $apiResponseTransfer = $this->buildApiResponseTransfer($jsonResponse);
        $cancelResponseTransfer = $this->buildCancelResponseTransfer($jsonResponse);

        $cancelResponseTransfer->setApiResponse($apiResponseTransfer);

        return $cancelResponseTransfer;
    }

    /**
     * @param string $jsonResponse
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelResponseTransfer
     */
    protected function buildCancelResponseTransfer(string $jsonResponse): AfterpayCancelResponseTransfer
    {
        $jsonResponseArray = $this->utilEncoding->decodeJson($jsonResponse, true);

        $cancelResponseTransfer = new AfterpayCancelResponseTransfer();

        $cancelResponseTransfer
            ->setTotalCapturedAmount(
                $this->money->convertDecimalToInteger(
                    $jsonResponseArray[AfterpayApiRequestConfig::CANCEL_CAPTURED_AMOUNT]
                )
            )
            ->setTotalAuthorizedAmount(
                $this->money->convertDecimalToInteger(
                    $jsonResponseArray[AfterpayApiRequestConfig::CANCEL_AUTHORIZED_AMOUNT]
                )
            );

        return $cancelResponseTransfer;
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

        $outcome = isset($jsonResponseArray[AfterpayApiRequestConfig::CANCEL_AUTHORIZED_AMOUNT])
            ? AfterpayConfig1::API_TRANSACTION_OUTCOME_ACCEPTED
            : AfterpayConfig1::API_TRANSACTION_OUTCOME_REJECTED;

        $apiResponseTransfer
            ->setOutcome($outcome)
            ->setResponsePayload($jsonResponse);

        return $apiResponseTransfer;
    }
}
