<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\Afterpay\Business\Payment\Transaction\Authorize;

use Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayAuthorization;
use SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface;

class PaymentAuthorizeWriter implements PaymentAuthorizeWriterInterface
{
    /**
     * @var \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface
     */
    protected $afterpayQueryContainer;

    /**
     * @param \SprykerEco\Zed\Afterpay\Persistence\AfterpayQueryContainerInterface $afterpayQueryContainer
     */
    public function __construct(AfterpayQueryContainerInterface $afterpayQueryContainer)
    {
        $this->afterpayQueryContainer = $afterpayQueryContainer;
    }

    /**
     * @param string $orderReference
     * @param string $idReservation
     * @param string $idCheckout
     *
     * @return void
     */
    public function save(string $orderReference, string $idReservation, string $idCheckout): void
    {
        $authorizationEntity = $this->getPaymentAuthorizeEntity($orderReference);
        $authorizationEntity
            ->setOrderReference($orderReference)
            ->setIdReservation($idReservation)
            ->setIdCheckout($idCheckout)
            ->save();
    }

    /**
     * @param string $orderReference
     *
     * @return \Orm\Zed\Afterpay\Persistence\SpyPaymentAfterpayAuthorization
     */
    protected function getPaymentAuthorizeEntity(string $orderReference): SpyPaymentAfterpayAuthorization
    {
        $existingEntity = $this->afterpayQueryContainer
            ->queryAuthorizationByOrderReference($orderReference)
            ->findOne();
        if (!$existingEntity) {
            $existingEntity = new SpyPaymentAfterpayAuthorization();
        }

        return $existingEntity;
    }
}
