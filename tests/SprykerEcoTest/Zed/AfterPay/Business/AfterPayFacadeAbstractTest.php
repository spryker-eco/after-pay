<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use Codeception\TestCase\Test;
use Generated\Shared\DataBuilder\AfterPayCallBuilder;
use Generated\Shared\DataBuilder\QuoteBuilder;
use Generated\Shared\DataBuilder\TaxTotalBuilder;
use SprykerEcoTest\Zed\AfterPay\Mock\AfterPayFacadeMock;

class AfterPayFacadeAbstractTest extends Test
{
    /**
     * @var \SprykerEcoTest\Zed\AfterPay\Mock\AfterPayFacadeMock
     */
    protected $facade;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->facade = new AfterPayFacadeMock();
    }

    /**
     * @return \Generated\Shared\Transfer\AfterPayCallTransfer
     */
    protected function createCallTransfer()
    {
        $call = (new AfterPayCallBuilder())
            ->withBillingAddress()
            ->withShippingAddress()
            ->withTotals()
            ->withItem()
            ->build();

        $call->getTotals()->setTaxTotal(
            $this->createTaxTotalTransfer()
        );

        return $call;
    }

    /**
     * @return \Generated\Shared\Transfer\QuoteTransfer
     */
    protected function createQuoteTransfer()
    {
        $quote = (new QuoteBuilder())
            ->withBillingAddress()
            ->withShippingAddress()
            ->withCustomer()
            ->withTotals()
            ->withItem()
            ->withAnotherItem()
            ->build();

        $quote->getTotals()->setTaxTotal(
            $this->createTaxTotalTransfer()
        );

        return $quote;
    }

    /**
     * @return \Generated\Shared\Transfer\TaxTotalTransfer
     */
    protected function createTaxTotalTransfer()
    {
        return (new TaxTotalBuilder())->build();
    }
}
