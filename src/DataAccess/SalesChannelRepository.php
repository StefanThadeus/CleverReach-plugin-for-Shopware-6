<?php

namespace Logeecom\CleverReachPlugin\DataAccess;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\SalesChannelRepositoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class SalesChannelRepository implements SalesChannelRepositoryInterface
{
    private EntityRepositoryInterface $salesChannelTranslationRepository;

    public function __construct(EntityRepositoryInterface $salesChannelTranslationRepository)
    {
        $this->salesChannelTranslationRepository = $salesChannelTranslationRepository;
    }

    public function getSalesChannelId(string $ShopName): string
    {
        if ($ShopName) {
            if (explode(' ', $ShopName) > 1) {
                $salesChannelName = explode(' ', $ShopName)[1];
            } else {
                $salesChannelName = $ShopName;
            }
        } else {
            $salesChannelName = '';
        }

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', $salesChannelName));
        $results = $this->salesChannelTranslationRepository->searchIds($criteria, Context::createDefaultContext())->getIds();

        $salesChannelId = count($results) ? $results[0]['salesChannelId'] : '';

        if (!$salesChannelId) {
            $results = $this->salesChannelTranslationRepository->searchIds(new Criteria(), Context::createDefaultContext())->getIds();
            $salesChannelId = count($results) ? $results[0]['salesChannelId'] : '';
        }

        return $salesChannelId;
    }
}