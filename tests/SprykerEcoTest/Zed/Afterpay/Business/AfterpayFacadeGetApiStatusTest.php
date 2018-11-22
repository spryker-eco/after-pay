<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Afterpay\Business;

class AfterpayFacadeGetApiStatusTest extends AfterpayFacadeAbstractTest
{
    /**
     * @return void
     */
    public function testGetApiStatus()
    {
        $output = $this->doFacadeCall();
        $this->doTest($output);
    }

    /**
     * @return int
     */
    protected function doFacadeCall()
    {
        return $this->facade->getApiStatus();
    }

    /**
     * @param int $output
     *
     * @return void
     */
    protected function doTest($output)
    {
        $this->assertNotEmpty($output);
    }
}
