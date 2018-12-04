<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

use Generated\Shared\DataBuilder\ItemBuilder;
use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\ItemTransfer;

class AfterPayFacadeCaptureTest extends AfterPayFacadeAbstractTest
{
    /**
     * @return void
     */
    public function testCapture(): void
    {
        $call = $this->createCallTransfer();
        $item = $this->createItemTransfer();
        $this->doFacadeCall($item, $call);
        $this->doTest();
    }

    /**
     * @return \Generated\Shared\Transfer\ItemTransfer
     */
    protected function createItemTransfer(): ItemTransfer
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
    protected function doFacadeCall(ItemTransfer $item, AfterPayCallTransfer $call): void
    {
        $this->facade->capturePayment($item, $call);
    }

    /**
     * @return void
     */
    protected function doTest(): void
    {
        //Is transaction accepted
        //Is captured amount updated?
    }
}
