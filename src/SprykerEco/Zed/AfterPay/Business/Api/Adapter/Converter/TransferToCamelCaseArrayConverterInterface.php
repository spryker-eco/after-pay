<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;

interface TransferToCamelCaseArrayConverterInterface
{
    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $transfer
     *
     * @return array
     */
    public function convert(AbstractTransfer $transfer): array;
}
