<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Filter\Provider;

use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsRequestTransfer;
use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsResponseTransfer;
use Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer;
use Generated\Shared\Transfer\QuoteTransfer;
use SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface;
use SprykerEco\Zed\AfterPay\Business\Payment\Mapper\QuoteToRequestTransferInterface;

class AfterPayPaymentMethodsProvider implements AfterPayPaymentMethodsProviderInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface
     */
    protected $apiAdapter;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Mapper\QuoteToRequestTransferInterface
     */
    protected $quoteToRequestMapper;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Api\Adapter\AdapterInterface $apiAdapter
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Mapper\QuoteToRequestTransferInterface $quoteToRequestMapper
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
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer
     */
    public function getAvailablePaymentMethods(QuoteTransfer $quoteTransfer): AfterPayAvailablePaymentMethodsTransfer
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
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsRequestTransfer
     */
    protected function buildRequestTransferFromQuote(QuoteTransfer $quoteTransfer): AfterPayAvailablePaymentMethodsRequestTransfer
    {
        return $this->quoteToRequestMapper->quoteToAvailablePaymentMethods($quoteTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsRequestTransfer $requestTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsResponseTransfer
     */
    protected function sendRequest(AfterPayAvailablePaymentMethodsRequestTransfer $requestTransfer): AfterPayAvailablePaymentMethodsResponseTransfer
    {
        return $this->apiAdapter->sendAvailablePaymentMethodsRequest($requestTransfer);
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsResponseTransfer $apiResponseTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsTransfer
     */
    protected function parseResponseTransfer(AfterPayAvailablePaymentMethodsResponseTransfer $apiResponseTransfer): AfterPayAvailablePaymentMethodsTransfer
    {
        $availablePaymentMethodsTransfer = new AfterPayAvailablePaymentMethodsTransfer();

        $availablePaymentMethodNames = $this->fetchAvailablePaymentMethodsNames($apiResponseTransfer);

        $availablePaymentMethodsTransfer
            ->setAvailablePaymentMethodNames($availablePaymentMethodNames)
            ->setRiskCheckCode($apiResponseTransfer->getRiskCheckResultCode())
            ->setCheckoutId($apiResponseTransfer->getCheckoutId())
            ->setCustomerNumber($apiResponseTransfer->getCustomerNumber())
            ->setOutcome($apiResponseTransfer->getOutcome())
            ->setRiskCheckMessages($apiResponseTransfer->getRiskCheckMessages());

        return $availablePaymentMethodsTransfer;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayAvailablePaymentMethodsResponseTransfer $apiResponseTransfer
     *
     * @return array
     */
    protected function fetchAvailablePaymentMethodsNames(AfterPayAvailablePaymentMethodsResponseTransfer $apiResponseTransfer): array
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
