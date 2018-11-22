<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Client\Afterpay\Dependency\Client;

interface AfterpayToLocaleInterface
{
    /**
     * @return string
     */
    public function getCurrentLocale();
}
