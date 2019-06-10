<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Exception;

use Exception;
use Generated\Shared\Transfer\AfterPayApiResponseErrorTransfer;

class ApiHttpRequestException extends Exception
{
    /**
     * @var \Generated\Shared\Transfer\AfterPayApiResponseErrorTransfer
     */
    protected $error;

    /**
     * @var string
     */
    protected $detailedMessage;

    /**
     * @param \Generated\Shared\Transfer\AfterPayApiResponseErrorTransfer $error
     *
     * @return void
     */
    public function setError(AfterPayApiResponseErrorTransfer $error): void
    {
        $this->error = $error;
    }

    /**
     * @return \Generated\Shared\Transfer\AfterPayApiResponseErrorTransfer
     */
    public function getError(): AfterPayApiResponseErrorTransfer
    {
        return $this->error;
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function setDetailedMessage(string $message): void
    {
        $this->detailedMessage = $message;
    }

    /**
     * @return string
     */
    public function getDetailedMessage(): string
    {
        if (empty($this->detailedMessage)) {
            return parent::getMessage();
        }

        return $this->detailedMessage;
    }
}
