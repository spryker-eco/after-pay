<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerEco\Zed\Afterpay\AfterpayDependencyProvider;
use SprykerEco\Zed\Afterpay\Communication\Converter\OrderToCallConverter;
use SprykerEco\Zed\Afterpay\Communication\Converter\QuoteToCallConverter;

/**
 * @method \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\Afterpay\AfterpayConfig getConfig()
 */
class AfterpayCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToSalesInterface
     */
    public function getSalesFacade()
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_SALES);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToRefundInterface
     */
    public function getRefundFacade()
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_REFUND);
    }
    /**
     * @return \SprykerEco\Zed\Afterpay\Communication\Converter\QuoteToCallConverter
     */
    public function createQuoteToCallConverter()
    {
        return new QuoteToCallConverter();
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Communication\Converter\OrderToCallConverter
     */
    public function createOrderToCallConverter()
    {
        return new OrderToCallConverter();
    }
}
