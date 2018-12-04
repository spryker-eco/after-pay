<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Client\AfterPay\Dependency\Client;

interface AfterPayToLocaleClientInterface
{
    /**
     * @return string
     */
    public function getCurrentLocale();
}
