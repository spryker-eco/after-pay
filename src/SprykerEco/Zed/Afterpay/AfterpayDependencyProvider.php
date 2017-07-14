<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay;

use Spryker\Shared\Kernel\Store;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToCustomerBridge;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyBridge;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToPaymentBridge;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToSalesBridge;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingBridge;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilTextBridge;

class AfterpayDependencyProvider extends AbstractBundleDependencyProvider
{

    const FACADE_MONEY = 'money facade';
    const FACADE_SALES = 'sales facade';
    const FACADE_CUSTOMER = 'customer facade';
    const FACADE_PAYMENT = 'payment';

    const SERVICE_UTIL_ENCODING = 'util encoding service';
    const SERVICE_UTIL_TEXT = 'util text service';

    const CURRENT_STORE = 'current store';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideBusinessLayerDependencies(Container $container)
    {
        $container[static::FACADE_MONEY] = function (Container $container) {
            return new AfterpayToMoneyBridge($container->getLocator()->money()->facade());
        };

        $container[static::FACADE_SALES] = function (Container $container) {
            return new AfterpayToSalesBridge($container->getLocator()->sales()->facade());
        };

        $container[static::FACADE_CUSTOMER] = function (Container $container) {
            return new AfterpayToCustomerBridge($container->getLocator()->customer()->facade());
        };

        $container[static::SERVICE_UTIL_ENCODING] = function (Container $container) {
            return new AfterpayToUtilEncodingBridge($container->getLocator()->utilEncoding()->service());
        };

        $container[static::SERVICE_UTIL_TEXT] = function (Container $container) {
            return new AfterpayToUtilTextBridge($container->getLocator()->utilText()->service());
        };

        $container[static::FACADE_PAYMENT] = function (Container $container) {
            return new AfterpayToPaymentBridge($container->getLocator()->payment()->facade());
        };

        $container[static::CURRENT_STORE] = function (Container $container) {
            return $this->getStore();
        };

        return $container;
    }

    /**
     * @return \Spryker\Shared\Kernel\Store
     */
    protected function getStore()
    {
        return Store::getInstance();
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container)
    {
        $container[static::FACADE_SALES] = function (Container $container) {
            return new AfterpayToSalesBridge($container->getLocator()->sales()->facade());
        };

        return $container;
    }

}
