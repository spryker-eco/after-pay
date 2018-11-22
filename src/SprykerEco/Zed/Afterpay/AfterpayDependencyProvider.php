<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay;

use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToCustomerBridge;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToMoneyBridge;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToRefundBridge;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToSalesBridge;
use SprykerEco\Zed\Afterpay\Dependency\Facade\AfterpayToStoreBridge;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilEncodingBridge;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilTextBridge;

class AfterpayDependencyProvider extends AbstractBundleDependencyProvider
{
    const FACADE_MONEY = 'money facade';
    const FACADE_SALES = 'sales facade';
    const FACADE_CUSTOMER = 'customer facade';
    const FACADE_REFUND = 'refund facade';
    const FACADE_STORE = 'store facade';

    const SERVICE_UTIL_ENCODING = 'util encoding service';
    const SERVICE_UTIL_TEXT = 'util text service';

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

        $container[static::FACADE_STORE] = function (Container $container) {
            return new AfterpayToStoreBridge($container->getLocator()->store()->facade());
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

        return $container;
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

        $container[static::FACADE_REFUND] = function (Container $container) {
            return new AfterpayToRefundBridge($container->getLocator()->refund()->facade());
        };

        return $container;
    }
}
