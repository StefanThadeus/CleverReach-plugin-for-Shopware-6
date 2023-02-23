<?php

namespace Logeecom\CleverReachPlugin\DataAccess;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\LanguageRepositoryInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepositoryInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class LanguageRepository implements LanguageRepositoryInterface
{
    private EntityRepositoryInterface $languageRepository;

    public function __construct(EntityRepositoryInterface $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }

    public function getLanguageId(string $language): string
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('name', $language));
        $languageId = $this->languageRepository->searchIds($criteria, Context::createDefaultContext())->firstId();

        if (!$languageId) {
            $languageId = $this->languageRepository->searchIds(new Criteria(), Context::createDefaultContext())->firstId();
        }

        return $languageId;
    }
}