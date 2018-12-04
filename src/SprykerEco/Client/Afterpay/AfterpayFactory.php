<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\Afterpay;

use Spryker\Client\Kernel\AbstractFactory;
use SprykerEco\Client\Afterpay\Dependency\Client\AfterpayToZedRequestClientInterface;
use SprykerEco\Client\Afterpay\Zed\AfterpayStub;
use SprykerEco\Client\Afterpay\Zed\AfterpayStubInterface;

class AfterpayFactory extends AbstractFactory
{
    /**
     * @return \SprykerEco\Client\Afterpay\Zed\AfterpayStubInterface
     */
    public function createZedAfterpayStub(): AfterpayStubInterface
    {
        return new AfterpayStub($this->getZedRequestClient());
    }

    /**
     * @return \SprykerEco\Client\Afterpay\Dependency\Client\AfterpayToZedRequestClientInterface
     */
    public function getZedRequestClient(): AfterpayToZedRequestClientInterface
    {
        return $this->getProvidedDependency(AfterpayDependencyProvider::CLIENT_ZED_REQUEST);
    }
}
