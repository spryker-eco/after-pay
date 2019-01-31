<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize\RequestBuilder;

use Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterPayCallTransfer;
use SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface;

class OneStepAuthorizeRequestBuilder implements AuthorizeRequestBuilderInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface
     */
    protected $orderToRequestTransferMapper;

    /**
     * @param \SprykerEco\Zed\AfterPay\Business\Payment\Mapper\OrderToRequestTransferInterface $orderToRequestTransferMapper
     */
    public function __construct(OrderToRequestTransferInterface $orderToRequestTransferMapper)
    {
        $this->orderToRequestTransferMapper = $orderToRequestTransferMapper;
    }

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterPayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer
     */
    public function buildAuthorizeRequest(AfterPayCallTransfer $afterPayCallTransfer): AfterPayAuthorizeRequestTransfer
    {
        return $this->orderToRequestTransferMapper->orderToAuthorizeRequest($afterPayCallTransfer);
    }
}
