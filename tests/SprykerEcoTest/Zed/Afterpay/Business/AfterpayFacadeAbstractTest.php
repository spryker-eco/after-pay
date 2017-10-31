<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Afterpay\Business;

use Codeception\TestCase\Test;
use Generated\Shared\DataBuilder\AfterpayCallBuilder;
use Generated\Shared\DataBuilder\TaxTotalBuilder;

class AfterpayFacadeAbstractTest extends Test
{
    /**
     * @return \Generated\Shared\Transfer\AfterpayCallTransfer
     */
    protected function createCallTransfer()
    {
        $call = (new AfterpayCallBuilder())
            ->withBillingAddress()
            ->withShippingAddress()
            ->withTotals()
            ->withItem()
            ->build();

        $call->getTotals()->setTaxTotal(
            (new TaxTotalBuilder())->build()
        );

        return $call;
    }
}