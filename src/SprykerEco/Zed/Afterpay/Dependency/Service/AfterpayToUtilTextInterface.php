<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Dependency\Service;

interface AfterpayToUtilTextInterface
{
    /**
     * @param string $string
     * @param string $separator
     * @param bool $upperCaseFirst
     *
     * @return string
     */
    public function separatorToCamelCase($string, $separator = '-', $upperCaseFirst = false);

    /**
     * @param string $string
     * @param string $separator
     *
     * @return string
     */
    public function camelCaseToSeparator($string, $separator = '-');
}
