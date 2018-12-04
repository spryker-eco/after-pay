<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Handler\RiskCheck;

use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsResponseTransfer;
use Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\Afterpay\Business\Payment\Mapper\QuoteToRequestTransferInterface;

class AvailablePaymentMethodsHandler implements AvailablePaymentMethodsHandlerInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface
     */
    protected $apiAdapter;

    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Mapper\QuoteToRequestTransferInterface
     */
    protected $quoteToRequestMapper;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Api\Adapter\AdapterInterface $apiAdapter
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Mapper\QuoteToRequestTransferInterface $quoteToRequestMapper
     */
    public function __construct(
        AdapterInterface $apiAdapter,
        QuoteToRequestTransferInterface $quoteToRequestMapper
    ) {
        $this->apiAdapter = $apiAdapter;
        $this->quoteToRequestMapper = $quoteToRequestMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer
     */
    public function getAvailablePaymentMethods(QuoteTransfer $quoteTransfer): AfterpayAvailablePaymentMethodsTransfer
    {
        $requestTransfer = $this->buildRequestTransferFromQuote($quoteTransfer);
        $responseTransfer = $this->sendRequest($requestTransfer);

        $availablePaymentMethodsTransfer = $this->parseResponseTransfer($responseTransfer);

        $availablePaymentMethodsTransfer->setQuoteHash(
            $quoteTransfer->getTotals()->getHash()
        );

        return $availablePaymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\QuoteTransfer $quoteTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer
     */
    protected function buildRequestTransferFromQuote(QuoteTransfer $quoteTransfer): AfterpayAvailablePaymentMethodsRequestTransfer
    {
        return $this->quoteToRequestMapper->quoteToAvailablePaymentMethods($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsResponseTransfer
     */
    protected function sendRequest(AfterpayAvailablePaymentMethodsRequestTransfer $requestTransfer): AfterpayAvailablePaymentMethodsResponseTransfer
    {
        return $this->apiAdapter->sendAvailablePaymentMethodsRequest($requestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsResponseTransfer $apiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsTransfer
     */
    protected function parseResponseTransfer(AfterpayAvailablePaymentMethodsResponseTransfer $apiResponseTransfer): AfterpayAvailablePaymentMethodsTransfer
    {
        $availablePaymentMethodsTransfer = new AfterpayAvailablePaymentMethodsTransfer();

        $availablePaymentMethodNames = $this->fetchAvailablePaymentMethodsNames($apiResponseTransfer);

        $availablePaymentMethodsTransfer
            ->setAvailablePaymentMethodNames($availablePaymentMethodNames)
            ->setRiskCheckCode($apiResponseTransfer->getRiskCheckResultCode())
            ->setCheckoutId($apiResponseTransfer->getCheckoutId())
            ->setCustomerNumber($apiResponseTransfer->getCustomerNumber());

        return $availablePaymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayAvailablePaymentMethodsResponseTransfer $apiResponseTransfer
     *
     * @return array
     */
    protected function fetchAvailablePaymentMethodsNames(AfterpayAvailablePaymentMethodsResponseTransfer $apiResponseTransfer): array
    {
        $availablePaymentMethodNames = [];
        foreach ($apiResponseTransfer->getPaymentMethods() as $paymentMethodArray) {
            if (!isset($paymentMethodArray['type'])) {
                continue;
            }
            $availablePaymentMethodNames[] = $paymentMethodArray['type'];
        }

        return array_unique($availablePaymentMethodNames);
    }
}
