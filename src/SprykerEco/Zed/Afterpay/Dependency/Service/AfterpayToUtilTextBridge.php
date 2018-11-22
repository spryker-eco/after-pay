<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Dependency\Service;

class AfterpayToUtilTextBridge implements AfterpayToUtilTextInterface
{
    /**
     * @var \Spryker\Service\UtilText\UtilTextServiceInterface
     */
    protected $utilTextService;

    /**
     * @param \Spryker\Service\UtilText\UtilTextServiceInterface $utilTextService
     */
    public function __construct($utilTextService)
    {
        $this->utilTextService = $utilTextService;
    }

    /**
     * @param string $string
     * @param string $separator
     * @param bool $upperCaseFirst
     *
     * @return string
     */
    public function separatorToCamelCase($string, $separator = '-', $upperCaseFirst = false)
    {
        return $this->utilTextService->separatorToCamelCase($string, $separator, $upperCaseFirst);
    }

    /**
     * @param string $string
     * @param string $separator
     *
     * @return string
     */
    public function camelCaseToSeparator($string, $separator = '-')
    {
        return $this->utilTextService->camelCaseToSeparator($string, $separator);
    }
}
