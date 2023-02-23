<?php

namespace Logeecom\CleverReachPlugin\Controller\API\Backend;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ConnectionServiceInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\SynchronizationServiceInterface;
use Shopware\Core\Framework\Api\Response\JsonApiResponse;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RouterController
 */
class RouterController extends AbstractController
{
    public const WELCOME_STATE_CODE = 'welcome';
    public const DASHBOARD_STATE_CODE = 'dashboard';

    private ConnectionServiceInterface $connectionService;
    private SynchronizationServiceInterface $synchronizationService;

    public function __construct(ConnectionServiceInterface $connectionService, SynchronizationServiceInterface $synchronizationService)
    {
        $this->connectionService = $connectionService;
        $this->synchronizationService = $synchronizationService;
    }

    /**
     * Returns page for display
     *
     * @RouteScope(scopes={"api"})
     *
     * @Route(path="/api/v{version}/cleverreach/router", name="api.cleverreach.router", methods={"GET", "POST"})
     * @Route(path="/api/cleverreach/router", name="api.cleverreach.router.new", methods={"GET", "POST"})
     *
     * @return JsonApiResponse
     **/
    public function handle(): JsonApiResponse
    {
        return new JsonApiResponse(['page' => $this->getPage()]);
    }

    private function getPage(): string
    {
        if (!$this->connectionService->isIntegrationConnected()) {
            return self::WELCOME_STATE_CODE;
        }

        $this->checkForInitialSync();
        return self::DASHBOARD_STATE_CODE;
    }

    private function checkForInitialSync(): void
    {
        if ($this->synchronizationService->getSyncStatus() != 'done') {
            $this->synchronizationService->startInitialSync();
        }
    }
}