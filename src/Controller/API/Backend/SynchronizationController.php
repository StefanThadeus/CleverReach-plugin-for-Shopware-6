<?php

namespace Logeecom\CleverReachPlugin\Controller\API\Backend;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\AuthServiceInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ConnectionServiceInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\SynchronizationServiceInterface;
use Shopware\Core\Framework\Api\Response\JsonApiResponse;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SynchronizationController extends AbstractController
{
    private SynchronizationServiceInterface $synchronizationService;
    private AuthServiceInterface $authService;

    public function __construct(SynchronizationServiceInterface $synchronizationService, AuthServiceInterface $authService)
    {
        $this->synchronizationService = $synchronizationService;
        $this->authService = $authService;
    }

    /**
     * Returns page for display
     *
     * @RouteScope(scopes={"api"})
     *
     * @Route(path="/api/v{version}/cleverreach/manualSync", name="api.cleverreach.manualSync", methods={"GET", "POST"})
     * @Route(path="/api/cleverreach/manualSync", name="api.cleverreach.manualSync.new", methods={"GET", "POST"})
     *
     * @return JsonApiResponse
     **/
    public function startManualSync(): JsonApiResponse
    {
        $this->synchronizationService->startManualSync();
        return new JsonApiResponse(['manualSyncStarted' => true]);
    }

    /**
     * Returns page for display
     *
     * @RouteScope(scopes={"api"})
     *
     * @Route(path="/api/v{version}/cleverreach/getClientId", name="api.cleverreach.getClientId", methods={"GET", "POST"})
     * @Route(path="/api/cleverreach/getClientId", name="api.cleverreach.getClientId.new", methods={"GET", "POST"})
     *
     * @return JsonApiResponse
     **/
    public function getClientId(): JsonApiResponse
    {
        return new JsonApiResponse(['clientId' => $this->authService->getClientId()]);
    }
}