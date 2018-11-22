<?php

/**
 * MIT License
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize\RequestBuilder;

use Generated\Shared\Transfer\AfterpayCallTransfer;
use SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface;

class OneStepAuthorizeRequestBuilder implements AuthorizeRequestBuilderInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface
     */
    protected $orderToRequestTransferMapper;

    /**
     * @param \SprykerEco\Zed\Afterpay\Business\Payment\Mapper\OrderToRequestTransferInterface $orderToRequestTransferMapper
     */
    public function __construct(OrderToRequestTransferInterface $orderToRequestTransferMapper)
    {
        $this->orderToRequestTransferMapper = $orderToRequestTransferMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer
     */
    public function buildAuthorizeRequest(AfterpayCallTransfer $afterpayCallTransfer)
    {
        return $this
            ->orderToRequestTransferMapper
            ->orderToAuthorizeRequest($afterpayCallTransfer);
    }
}
