<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Afterpay\Business;

use Codeception\TestCase\Test;
use Generated\Shared\DataBuilder\AfterpayCallBuilder;
use Generated\Shared\DataBuilder\QuoteBuilder;
use Generated\Shared\DataBuilder\TaxTotalBuilder;
use SprykerEcoTest\Zed\Afterpay\Mock\AfterpayFacadeMock;

class AfterpayFacadeAbstractTest extends Test
{
    /**
     * @var \SprykerEcoTest\Zed\Afterpay\Mock\AfterpayFacadeMock
     */
    protected $facade;

    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        $this->facade = new AfterpayFacadeMock();
    }

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
