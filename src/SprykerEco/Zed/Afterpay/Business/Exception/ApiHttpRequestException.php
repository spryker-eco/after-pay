<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Exception;

use Exception;
use Generated\Shared\Transfer\AfterpayApiResponseErrorTransfer;

class ApiHttpRequestException extends Exception
{
    /**
     * @var \Generated\Shared\Transfer\AfterpayApiResponseErrorTransfer
     */
    protected $error;

    /**
     * @var string
     */
    protected $detailedMessage;

    /**
     * @param \Generated\Shared\Transfer\AfterpayApiResponseErrorTransfer $error
     *
     * @return void
     */
    public function setError(AfterpayApiResponseErrorTransfer $error): void
    {
        $this->error = $error;
    }

    /**
     * @return \Generated\Shared\Transfer\AfterpayApiResponseErrorTransfer
     */
    public function getError(): AfterpayApiResponseErrorTransfer
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
