<?php

namespace Logeecom\CleverReachPlugin\Controller\API\Backend;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ConnectionServiceInterface;
use Shopware\Core\Framework\Api\Response\JsonApiResponse;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CheckStatusController
 *
 * @package Sendcloud\Shipping\Controller\API\Backend
 */
class CheckStatusController extends AbstractController
{
    private ConnectionServiceInterface $connectionService;

    public function __construct(ConnectionServiceInterface $connectionService)
    {
        $this->connectionService = $connectionService;
    }

    /**
     * Returns connection status
     *
     * @RouteScope(scopes={"api"})
     * @Route(path="/api/v{version}/cleverreach/connectionStatus", name="api.cleverreach.connectionStatus", methods={"GET", "POST"})
     * @Route(path="/api/cleverreach/connectionStatus", name="api.sendcloud.cleverreach.new", methods={"GET", "POST"})
     *
     * @return JsonApiResponse
     **/
    public function checkConnectionStatus(): JsonApiResponse
    {
        return new JsonApiResponse(['isConnected' => $this->connectionService->isIntegrationConnected()]);
    }

    /**
     * Returns connection status
     *
     * @RouteScope(scopes={"api"})
     * @Route(path="/api/v{version}/cleverreach/initialSyncInProgress", name="api.cleverreach.initialSyncInProgress", methods={"GET", "POST"})
     * @Route(path="/api/cleverreach/initialSyncInProgress", name="api.sendcloud.cleverreach.initialSyncInProgress.new", methods={"GET", "POST"})
     *
     * @return JsonApiResponse
     **/
    public function checkIfInitialSyncInProgress(): JsonApiResponse
    {
        return new JsonApiResponse(['inProgress' => $this->connectionService->isInitialSyncInProgress()]);
    }
}
