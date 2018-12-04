<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEcoTest\Zed\AfterPay\Business;

class AfterPayFacadeGetApiStatusTest extends AfterPayFacadeAbstractTest
{
    /**
     * @return void
     */
    public function testGetApiStatus(): void
    {
        $output = $this->doFacadeCall();
        $this->doTest($output);
    }

    /**
     * @return int
     */
    protected function doFacadeCall(): int
    {
        return $this->facade->getApiStatus();
    }

    /**
     * @param int $output
     *
     * @return void
     */
    protected function doTest(int $output): void
    {
        $this->assertNotEmpty($output);
    }
}
