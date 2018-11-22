<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Client\Afterpay\Dependency\Client;

class AfterpayToLocaleBridge implements AfterpayToLocaleInterface
{
    /**
     * @var \Spryker\Client\Locale\LocaleClientInterface
     */
    protected $localeClient;

    /**
     * @param \Spryker\Client\Locale\LocaleClientInterface $localeClient
     */
    public function __construct($localeClient)
    {
        $this->localeClient = $localeClient;
    }

    /**
     * @return string
     */
    public function getCurrentLocale()
    {
        return $this->localeClient->getCurrentLocale();
    }
}
