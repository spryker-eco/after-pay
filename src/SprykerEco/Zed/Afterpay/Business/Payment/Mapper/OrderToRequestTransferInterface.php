<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Mapper;

use Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer;
use Generated\Shared\Transfer\AfterpayCallTransfer;
use Generated\Shared\Transfer\AfterpayCancelRequestTransfer;
use Generated\Shared\Transfer\AfterpayCaptureRequestTransfer;
use Generated\Shared\Transfer\AfterpayRefundRequestTransfer;
use Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer;
use Generated\Shared\Transfer\ItemTransfer;
use Generated\Shared\Transfer\OrderTransfer;

interface OrderToRequestTransferInterface
{
    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayAuthorizeRequestTransfer
     */
    public function orderToAuthorizeRequest(AfterpayCallTransfer $afterpayCallTransfer): AfterpayAuthorizeRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCaptureRequestTransfer
     */
    public function orderToBaseCaptureRequest(AfterpayCallTransfer $afterpayCallTransfer): AfterpayCaptureRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\ItemTransfer $itemTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRequestOrderItemTransfer
     */
    public function orderItemToAfterpayItemRequest(ItemTransfer $itemTransfer): AfterpayRequestOrderItemTransfer;

    /**
     * @param \Generated\Shared\Transfer\AfterpayCallTransfer $afterpayCallTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayCancelRequestTransfer
     */
    public function orderToBaseCancelRequest(AfterpayCallTransfer $afterpayCallTransfer): AfterpayCancelRequestTransfer;

    /**
     * @param \Generated\Shared\Transfer\OrderTransfer $orderTransfer
     *
     * @return \Generated\Shared\Transfer\AfterpayRefundRequestTransfer
     */
    public function orderToBaseRefundRequest(OrderTransfer $orderTransfer): AfterpayRefundRequestTransfer;
}
