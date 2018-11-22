<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Afterpay\Business;

use Generated\Shared\DataBuilder\ItemBuilder;
use SprykerEcoTest\Zed\Afterpay\Mock\AfterpayFacadeMock;

class AfterpayFacadeCaptureTest extends AfterpayFacadeAbstractTest
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
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $call
     *
     * @return void
     */
    protected function doFacadeCall($item, $call)
    {
        (new AfterpayFacadeMock())->capturePayment($item, $call);
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
