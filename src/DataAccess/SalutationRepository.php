<?php

namespace Logeecom\CleverReachPlugin\DataAccess;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\SalutationRepositoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class SalutationRepository implements SalutationRepositoryInterface
{
    private EntityRepositoryInterface $salutationRepository;

    public function __construct(EntityRepositoryInterface $salutationRepository)
    {
        $this->salutationRepository = $salutationRepository;
    }

    public function getSalutationId(string $salutation): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('salutationKey', strtolower($salutation)));
        $salutationId = $this->salutationRepository->searchIds($criteria, Context::createDefaultContext())->firstId();

        if (!$salutationId) {
            $salutationId = $this->salutationRepository->searchIds(new Criteria(), Context::createDefaultContext())->firstId();
        }

        return $salutationId;
    }
}