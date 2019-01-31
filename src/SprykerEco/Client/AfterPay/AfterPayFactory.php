<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Client\AfterPay;

use Spryker\Client\Kernel\AbstractFactory;
use SprykerEco\Client\AfterPay\Dependency\Client\AfterPayToZedRequestClientInterface;
use SprykerEco\Client\AfterPay\Zed\AfterPayStub;
use SprykerEco\Client\AfterPay\Zed\AfterPayStubInterface;

class AfterPayFactory extends AbstractFactory
{
    /**
     * @return \SprykerEco\Client\AfterPay\Zed\AfterPayStubInterface
     */
    public function createZedAfterPayStub(): AfterPayStubInterface
    {
        return new AfterPayStub($this->getZedRequestClient());
    }

    /**
     * @return \SprykerEco\Client\AfterPay\Dependency\Client\AfterPayToZedRequestClientInterface
     */
    public function getZedRequestClient(): AfterPayToZedRequestClientInterface
    {
        return $this->getProvidedDependency(AfterPayDependencyProvider::CLIENT_ZED_REQUEST);
    }
}
