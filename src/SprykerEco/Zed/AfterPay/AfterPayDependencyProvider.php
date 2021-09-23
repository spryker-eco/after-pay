<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToCustomerFacadeBridge;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToMoneyFacadeBridge;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToPaymentFacadeBridge;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToRefundFacadeBridge;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToSalesFacadeBridge;
use SprykerEco\Zed\AfterPay\Dependency\Facade\AfterPayToStoreFacadeBridge;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceBridge;
use SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilTextServiceBridge;

class AfterPayDependencyProvider extends AbstractBundleDependencyProvider
{
    public const FACADE_MONEY = 'FACADE_MONEY';
    public const FACADE_SALES = 'FACADE_SALES';
    public const FACADE_CUSTOMER = 'FACADE_CUSTOMER';
    public const FACADE_PAYMENT = 'FACADE_PAYMENT';
    public const FACADE_REFUND = 'FACADE_REFUND';
    public const FACADE_STORE = 'FACADE_STORE';

    public const SERVICE_UTIL_ENCODING = 'SERVICE_UTIL_ENCODING';
    public const SERVICE_UTIL_TEXT = 'SERVICE_UTIL_TEXT';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container): Container
    {
        $container = parent::provideBusinessLayerDependencies($container);
        $container = $this->addMoneyFacade($container);
        $container = $this->addStoreFacade($container);
        $container = $this->addCustomerFacade($container);
        $container = $this->addPaymentFacade($container);
        $container = $this->addUtilEncodingService($container);
        $container = $this->addUtilTextService($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);
        $container = $this->addSalesFacade($container);
        $container = $this->addRefundFacade($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addMoneyFacade(Container $container): Container
    {
        $container->set(static::FACADE_MONEY, function (Container $container) {
            return new AfterPayToMoneyFacadeBridge($container->getLocator()->money()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSalesFacade(Container $container): Container
    {
        $container->set(static::FACADE_SALES, function (Container $container) {
            return new AfterPayToSalesFacadeBridge($container->getLocator()->sales()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addStoreFacade(Container $container): Container
    {
        $container->set(static::FACADE_STORE, function (Container $container) {
            return new AfterPayToStoreFacadeBridge($container->getLocator()->store()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCustomerFacade(Container $container): Container
    {
        $container->set(static::FACADE_CUSTOMER, function (Container $container) {
            return new AfterPayToCustomerFacadeBridge($container->getLocator()->customer()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addPaymentFacade(Container $container): Container
    {
        $container->set(static::FACADE_PAYMENT, function (Container $container) {
            return new AfterPayToPaymentFacadeBridge($container->getLocator()->payment()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addRefundFacade(Container $container): Container
    {
        $container->set(static::FACADE_REFUND, function (Container $container) {
            return new AfterPayToRefundFacadeBridge($container->getLocator()->refund()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilEncodingService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_ENCODING, function (Container $container) {
            return new AfterPayToUtilEncodingServiceBridge($container->getLocator()->utilEncoding()->service());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addUtilTextService(Container $container): Container
    {
        $container->set(static::SERVICE_UTIL_TEXT, function (Container $container) {
            return new AfterPayToUtilTextServiceBridge($container->getLocator()->utilText()->service());
        });

        return $container;
    }
}
