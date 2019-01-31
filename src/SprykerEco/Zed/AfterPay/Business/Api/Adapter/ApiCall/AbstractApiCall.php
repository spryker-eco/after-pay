<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Api\Adapter\ApiCall;

use Spryker\Shared\Kernel\Transfer\AbstractTransfer;
use Spryker\Shared\Log\LoggerTrait;
use SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException;

class AbstractApiCall
{
    use LoggerTrait;

    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Api\Adapter\Converter\TransferToCamelCaseArrayConverterInterface
     */
    protected $transferConverter;

    /**
     * @var \SprykerEco\Zed\AfterPay\Dependency\Service\AfterPayToUtilEncodingServiceInterface
     */
    protected $utilEncoding;

    /**
     * @param \Spryker\Shared\Kernel\Transfer\AbstractTransfer $requestTransfer
     *
     * @return string
     */
    protected function buildJsonRequestFromTransferObject(AbstractTransfer $requestTransfer): string
    {
        $requestArray = $this->transferConverter->convert($requestTransfer);
        return $this->utilEncoding->encodeJson($requestArray);
    }

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Exception\ApiHttpRequestException $apiHttpRequestException
     *
     * @return void
     */
    protected function logApiException(ApiHttpRequestException $apiHttpRequestException): void
    {
        $this->getLogger()->error(
            $apiHttpRequestException->getDetailedMessage(),
            ['exception' => $apiHttpRequestException]
        );
    }
}
