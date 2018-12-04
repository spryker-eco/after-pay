<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Api\Adapter\Converter;

use ArrayObject;
use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilTextInterface;

class TransferToCamelCaseArrayConverter implements TransferToCamelCaseArrayConverterInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilTextInterface
     */
    protected $utilTextService;

    /**
     * @param \SprykerEco\Zed\Afterpay\Dependency\Service\AfterpayToUtilTextInterface $utilTextService
     */
    public function __construct(AfterpayToUtilTextInterface $utilTextService)
    {
        $this->utilTextService = $utilTextService;
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $transfer
     *
     * @return array
     */
    public function convert(AbstractTransfer $transfer): array
    {
        return $this->convertTransferRecursively($transfer);
    }

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $transfer
     *
     * @return array
     */
    protected function convertTransferRecursively(AbstractTransfer $transfer): array
    {
        $originalArray = $transfer->toArray(false);
        $camelCaseArray = [];

        foreach ($originalArray as $key => $value) {
            $camelCaseKey = $this->underscoreToCamelCase($key);

            if ($value instanceof AbstractTransfer) {
                $camelCaseArray[$camelCaseKey] = $this->convertTransferRecursively($value);
            }

            if ($value instanceof ArrayObject) {
                $camelCaseArray[$camelCaseKey] = [];
                foreach ($value as $valueItem) {
                    $camelCaseArray[$camelCaseKey][] = $this->convertTransferRecursively($valueItem);
                }
            }

            if (is_scalar($value)) {
                $camelCaseArray[$camelCaseKey] = $value;
            }
        }

        return $camelCaseArray;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function underscoreToCamelCase(string $string): string
    {
        return $this->utilTextService->separatorToCamelCase($string, '_');
    }
}
