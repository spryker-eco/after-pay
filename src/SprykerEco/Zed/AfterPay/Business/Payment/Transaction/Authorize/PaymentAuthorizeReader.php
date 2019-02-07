<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize;

use SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface;

class PaymentAuthorizeReader implements PaymentAuthorizeReaderInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface
     */
    protected $afterPayQueryContainer;

    /**
     * @param \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface $afterPayQueryContainer
     */
    public function __construct(AfterPayQueryContainerInterface $afterPayQueryContainer)
    {
        $this->afterPayQueryContainer = $afterPayQueryContainer;
    }
}
