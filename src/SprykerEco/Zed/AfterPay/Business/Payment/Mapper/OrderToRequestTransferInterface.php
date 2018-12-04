<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Mapper;

use Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterPayCallTransfer;
use Generated\Shared\Transfer\AfterPayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterPayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterPayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface OrderToRequestTransferInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayAuthorizeRequestTransfer
     */
    public function orderToAuthorizeRequest(AfterPayCallTransfer $afterpayCallTransfer): AfterPayAuthorizeRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCaptureRequestTransfer
     */
    public function orderToBaseCaptureRequest(AfterPayCallTransfer $afterpayCallTransfer): AfterPayCaptureRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRequestOrderItemTransfer
     */
    public function orderItemToAfterPayItemRequest(ItemTransfer $itemTransfer): AfterPayRequestOrderItemTransfer;

    /**
     * @param \Generated\Shared\Transfer\AfterPayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayCancelRequestTransfer
     */
    public function orderToBaseCancelRequest(AfterPayCallTransfer $afterpayCallTransfer): AfterPayCancelRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterPayRefundRequestTransfer
     */
    public function orderToBaseRefundRequest(OrderTransfer $orderTransfer): AfterPayRefundRequestTransfer;
}
