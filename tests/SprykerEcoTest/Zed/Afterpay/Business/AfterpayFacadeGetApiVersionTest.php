<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEcoTest\Zed\Afterpay\Business;

class AfterpayFacadeGetApiVersionTest extends AfterpayFacadeAbstractTest
{
    /**
     * @return void
     */
    public function testGetApiVersion()
    {
        $output = $this->doFacadeCall();
        $this->doTest($output);
    }

    /**
     * @return string
     */
    protected function doFacadeCall()
    {
        return $this->facade->getApiVersion();
    }

    /**
     * @param string $output
     *
     * @return void
     */
    protected function doTest($output)
    {
        $this->assertNotEmpty($output);
    }
}
