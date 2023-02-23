<?php

namespace Logeecom\CleverReachPlugin\DataAccess;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\PaymentRepositoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class PaymentRepository implements PaymentRepositoryInterface
{
    private EntityRepositoryInterface $paymentRepository;

    public function __construct(EntityRepositoryInterface $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    public function getDefaultPaymentMethodId(string $salesChannelId = ''): string
    {
        $criteria = (new Criteria())
            ->setLimit(1)
            ->addFilter(new EqualsFilter('active', true));

        if ($salesChannelId) {
            $criteria->addFilter(new EqualsFilter('salesChannels.id', $salesChannelId));
        }

        /** @var string $id */
        $id = $this->paymentRepository->searchIds($criteria, Context::createDefaultContext())->firstId();

        return $id;
    }
}