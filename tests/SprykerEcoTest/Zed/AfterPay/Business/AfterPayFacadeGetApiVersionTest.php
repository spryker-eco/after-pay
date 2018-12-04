<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

class AfterPayFacadeGetApiVersionTest extends AfterPayFacadeAbstractTest
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
