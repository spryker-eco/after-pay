<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Client\Afterpay;

use Spryker\Client\Kernel\AbstractFactory;
use Spryker\Client\Session\SessionClientFactoryTrait;
use Spryker\Client\ZedRequest\ZedRequestClientFactoryTrait;
use SprykerEco\Client\Afterpay\Zed\AfterpayStub;

class AfterpayFactory extends AbstractFactory
{
    use SessionClientFactoryTrait;
    use ZedRequestClientFactoryTrait;

    /**
     * @return \SprykerEco\Client\Afterpay\Dependency\Client\AfterpayToLocaleInterface
     */
    public function getLocaleClient()
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::CLIENT_LOCALE);
    }

    /**
     * @return \SprykerEco\Client\Afterpay\Dependency\Client\AfterpayToQuoteInterface
     */
    public function getQuoteClient()
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::CLIENT_QUOTE);
    }

    /**
     * @return \SprykerEco\Client\Afterpay\Zed\AfterpayStubInterface
     */
    public function createZedStub()
    {
        return new AfterpayStub($this->getZedRequestClient());
    }
}
