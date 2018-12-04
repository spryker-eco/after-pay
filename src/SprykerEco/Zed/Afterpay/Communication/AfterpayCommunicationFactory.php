<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerEco\Zed\Afterpay\AfterpayDependencyProvider;
use SprykerEco\Zed\Afterpay\Communication\Converter\OrderToCallConverter;
use SprykerEco\Zed\Afterpay\Communication\Converter\OrderToCallConverterInterface;
use SprykerEco\Zed\Afterpay\Communication\Converter\QuoteToCallConverter;
use SprykerEco\Zed\Afterpay\Communication\Converter\QuoteToCallConverterInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToRefundInterface;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToSalesInterface;

/**
 * @method \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\Afterpay\AfterpayConfig getConfig()
 * @method \SprykerEco\Zed\Afterpay\Business\AfterpayFacadeInterface getFacade()
 */
class AfterpayCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToSalesInterface
     */
    public function getSalesFacade(): AfterpayToSalesInterface
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_SALES);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToRefundInterface
     */
    public function getRefundFacade(): AfterpayToRefundInterface
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::FACADE_REFUND);
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Communication\Converter\QuoteToCallConverterInterface
     */
    public function createQuoteToCallConverter(): QuoteToCallConverterInterface
    {
        return new QuoteToCallConverter();
    }

    /**
     * @return \SprykerEco\Zed\Afterpay\Communication\Converter\OrderToCallConverterInterface
     */
    public function createOrderToCallConverter(): OrderToCallConverterInterface
    {
        return new OrderToCallConverter();
    }
}
