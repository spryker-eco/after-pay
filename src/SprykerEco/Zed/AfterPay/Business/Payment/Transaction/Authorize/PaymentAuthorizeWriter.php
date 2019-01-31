<?php

/**
 * MIT License
 * For full license information, please view the LICENSE file that was distributed with this source code.
 */

namespace SprykerEco\Zed\AfterPay\Business\Payment\Transaction\Authorize;

use Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayAuthorization;
use SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface;

class PaymentAuthorizeWriter implements PaymentAuthorizeWriterInterface
{
    /**
     * @var \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface
     */
    protected $afterPayQueryContainer;

    /**
     * @param \SprykerEco\Zed\AfterPay\Persistence\AfterPayQueryContainerInterface $afterPayQueryContainer
     */
    public function __construct(AfterPayQueryContainerInterface $afterPayQueryContainer)
    {
        $this->afterPayQueryContainer = $afterPayQueryContainer;
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
     * @return \Orm\Zed\AfterPay\Persistence\SpyPaymentAfterPayAuthorization
     */
    protected function getPaymentAuthorizeEntity(string $orderReference): SpyPaymentAfterPayAuthorization
    {
        $existingEntity = $this->afterPayQueryContainer
            ->queryAuthorizationByOrderReference($orderReference)
            ->findOne();
        if (!$existingEntity) {
            $existingEntity = new SpyPaymentAfterPayAuthorization();
        }

        return $existingEntity;
    }
}
