<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Dependency\Injector;

use Spryker\Zed\Kernel\Container;
use Spryker\Zed\Kernel\Dependency\Injector\AbstractDependencyInjector;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Command\CommandCollectionInterface;
use Spryker\Zed\Oms\Communication\Plugin\Oms\Condition\ConditionCollectionInterface;
use Spryker\Zed\Oms\OmsDependencyProvider;
use SprykerEco\Zed\Afterpay\Communication\Plugin\Checkout\Oms\Command\AuthorizePlugin;
use SprykerEco\Zed\Afterpay\Communication\Plugin\Checkout\Oms\Command\CancelPlugin;
use SprykerEco\Zed\Afterpay\Communication\Plugin\Checkout\Oms\Command\CapturePlugin;
use SprykerEco\Zed\Afterpay\Communication\Plugin\Checkout\Oms\Condition\IsAuthorizationCompletedPlugin;
use SprykerEco\Zed\Afterpay\Communication\Plugin\Checkout\Oms\Condition\IsCancellationCompletedPlugin;
use SprykerEco\Zed\Afterpay\Communication\Plugin\Checkout\Oms\Condition\IsCaptureCompletedPlugin;

class OmsDependencyInjector extends AbstractDependencyInjector
{

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function injectBusinessLayerDependencies(Container $container)
    {
        $container = $this->injectCommands($container);
        $container = $this->injectConditions($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function injectCommands(Container $container)
    {
        $container->extend(OmsDependencyProvider::COMMAND_PLUGINS, function (CommandCollectionInterface $commandCollection) {
            $commandCollection
                ->add(new AuthorizePlugin(), 'Afterpay/Authorize')
                ->add(new CapturePlugin(), 'Afterpay/Capture')
                ->add(new CancelPlugin(), 'Afterpay/Cancel');

            return $commandCollection;
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function injectConditions(Container $container)
    {
        $container->extend(OmsDependencyProvider::CONDITION_PLUGINS, function (ConditionCollectionInterface $conditionCollection) {
            $conditionCollection
                ->add(new IsAuthorizationCompletedPlugin(), 'Afterpay/IsAuthorizationCompleted')
                ->add(new IsCaptureCompletedPlugin(), 'Afterpay/IsCaptureCompleted')
                ->add(new IsCancellationCompletedPlugin(), 'Afterpay/IsCancellationCompleted');

            return $conditionCollection;
        });

        return $container;
    }

}
