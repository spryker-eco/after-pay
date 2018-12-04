<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use Generated\Shared\DataBuilder\ItemBuilder;
use SprykerEcoTest\Zed\AfterPay\Mock\AfterPayFacadeMock;

class AfterPayFacadeCaptureTest extends AfterPayFacadeAbstractTest
{
    /**
     * @return void
     */
    public function setUp()
    {
        parent::setUp();
        //Create payment in database.
    }

    /**
     * @return void
     */
    public function tearDown()
    {
        parent::tearDown();
        //Remove payments from database.
    }

    /**
     * @return void
     */
    protected function testCapture()
    {
        $call = $this->createCallTransfer();
        $item = $this->createItemTransfer();
        $this->doFacadeCall($item, $call);
        $this->doTest();
    }

    /**
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function createItemTransfer()
    {
        $item = (new ItemBuilder())
            ->build();

        return $item;
    }

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $item
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $call
     *
     * @return void
     */
    protected function doFacadeCall($item, $call)
    {
        (new AfterPayFacadeMock())->capturePayment($item, $call);
    }

    /**
     * @return void
     */
    protected function doTest()
    {
        //Is transaction accepted
        //Is captured amount updated?
    }
}
