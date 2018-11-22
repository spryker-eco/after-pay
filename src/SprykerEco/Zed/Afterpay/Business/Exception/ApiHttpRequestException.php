<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Exception;

use Exception;
use Generated\Shared\Transfer\AfterpayApiResponseErrorTransfer;

class ApiHttpRequestException extends Exception
{

    /**
     * @var $error
     */
    protected $error;

    /**
     * @var $detailedMessage
     */
    protected $detailedMessage;

    /**
     * @param \Generated\Shared\Transfer\AfterpayApiResponseErrorTransfer $error
     *
     * @return void
     */
    public function setError(AfterpayApiResponseErrorTransfer $error)
    {
        $this->error = $error;
    }

    /**
     * @return \Generated\Shared\Transfer\AfterpayApiResponseErrorTransfer
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $message
     *
     * @return void
     */
    public function setDetailedMessage(string $message)
    {
        $this->detailedMessage = $message;
    }

    /**
     * @return string
     */
    public function getDetailedMessage()
    {
        if (empty($this->detailedMessage)) {
            return parent::getMessage();
        }
        return $this->detailedMessage;
    }

}
