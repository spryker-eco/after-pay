<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Dependency\Injector;

use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Kernel\Dependency\Injector\AbstractDependencyInjector;
use Spryker\Zed\Payment\Dependency\Plugin\Checkout\CheckoutPluginCollection;
use Spryker\Zed\Payment\PaymentDependencyProvider;
use SprykerEco\Shared\Afterpay\AfterpayConstants;
use SprykerEco\Zed\Afterpay\Communication\Plugin\Checkout\AfterpayPostCheckPlugin;
use SprykerEco\Zed\Afterpay\Communication\Plugin\Checkout\AfterpaySaveOrderPlugin;

class PaymentDependencyInjector extends AbstractDependencyInjector
{

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function injectBusinessLayerDependencies(Container $container)
    {
        $container = $this->injectPaymentPlugin($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function injectPaymentPlugin(Container $container)
    {
        $container->extend(PaymentDependencyProvider::CHECKOUT_PLUGINS, function (CheckoutPluginCollection $pluginCollection) {
            $pluginCollection->add(new AfterpaySaveOrderPlugin(), AfterpayConstants::PROVIDER_NAME, PaymentDependencyProvider::CHECKOUT_ORDER_SAVER_PLUGINS);
            $pluginCollection->add(new AfterpayPostCheckPlugin(), AfterpayConstants::PROVIDER_NAME, PaymentDependencyProvider::CHECKOUT_POST_SAVE_PLUGINS);

            return $pluginCollection;
        });

        return $container;
    }

}
