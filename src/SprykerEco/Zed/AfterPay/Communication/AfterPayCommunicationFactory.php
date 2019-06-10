<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Communication;

use Spryker\Zed\Kernel\Communication\AbstractCommunicationFactory;
use SprykerEco\Zed\AfterPay\AfterPayDependencyProvider;
use SprykerEco\Zed\AfterPay\Communication\Converter\OrderToCallConverter;
use SprykerEco\Zed\AfterPay\Communication\Converter\OrderToCallConverterInterface;
use SprykerEco\Zed\AfterPay\Communication\Converter\QuoteToCallConverter;
use SprykerEco\Zed\AfterPay\Communication\Converter\QuoteToCallConverterInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToRefundFacadeInterface;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToSalesFacadeInterface;

/**
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface getQueryContainer()
 * @method \SprykerEco\Zed\AfterPay\AfterPayConfig getConfig()
 * @method \SprykerEco\Zed\AfterPay\Business\AfterPayFacadeInterface getFacade()
 * @method \SprykerEco\Zed\AfterPay\Persistence\AfterPayEntityManagerInterface getEntityManager()
 */
class AfterPayCommunicationFactory extends AbstractCommunicationFactory
{
    /**
     * @return \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToSalesFacadeInterface
     */
    public function getSalesFacade(): AfterPayToSalesFacadeInterface
    {
        return $this->getProvidedDependency(AfterPayDependencyProvider::FACADE_SALES);
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToRefundFacadeInterface
     */
    public function getRefundFacade(): AfterPayToRefundFacadeInterface
    {
        return $this->getProvidedDependency(AfterPayDependencyProvider::FACADE_REFUND);
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Communication\Converter\QuoteToCallConverterInterface
     */
    public function createQuoteToCallConverter(): QuoteToCallConverterInterface
    {
        return new QuoteToCallConverter();
    }

    /**
     * @return \SprykerEco\Zed\AfterPay\Communication\Converter\OrderToCallConverterInterface
     */
    public function createOrderToCallConverter(): OrderToCallConverterInterface
    {
        return new OrderToCallConverter();
    }
}
