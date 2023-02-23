<?php

namespace Logeecom\CleverReachPlugin\Controller\API\Frontend;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ReceiverServiceInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\WebhookServiceInterface;
use Shopware\Core\Framework\Api\Response\JsonApiResponse;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractController
{
    private WebhookServiceInterface $webhookService;
    private ReceiverServiceInterface $receiverService;

    public function __construct(WebhookServiceInterface $webhookService, ReceiverServiceInterface $receiverService)
    {
        $this->webhookService = $webhookService;
        $this->receiverService = $receiverService;
    }

    /**
     * Returns redirect url for CleverReach connection screen
     *
     * @RouteScope(scopes={"api"})
     *
     * @Route(path="/api/v{version}/cleverreach/webhook/createReceiver", name="api.cleverreach.webhook.createReceiver", defaults={"auth_required"=false, "auth_enabled"=false}, methods={"GET", "POST"})
     * @Route(path="/api/cleverreach/webhook/createReceiver", name="api.cleverreach.webhook.createReceiver.new", defaults={"auth_required"=false, "auth_enabled"=false}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @return void
     */
    public function handleCustomerCreatedEvent(Request $request): Response
    {
        $eventName = $this->webhookService->getReceiverCreatedEventName();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->webhookService->confirmWebhookRegistration($eventName, $_GET['secret']);
        } else {
            if ($request->headers->get('x-cr-calltoken') === $this->webhookService->getEventCallToken($eventName)) {
                $requestContent = json_decode($request->getContent());
                $receiverId = $requestContent->payload->pool_id;
                $this->receiverService->syncCustomer($receiverId);
            }
        }
    }

    /**
     * Returns redirect url for CleverReach connection screen
     *
     * @RouteScope(scopes={"api"})
     *
     * @Route(path="/api/v{version}/cleverreach/webhook/updateReceiver", name="api.cleverreach.webhook.updateReceiver", defaults={"auth_required"=false, "auth_enabled"=false}, methods={"GET", "POST"})
     * @Route(path="/api/cleverreach/webhook/updateReceiver", name="api.cleverreach.webhook.updateReceiver.new", defaults={"auth_required"=false, "auth_enabled"=false}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @return void
     */
    public function handleCustomerUpdatedEvent(Request $request): Response
    {
        $eventName = $this->webhookService->getReceiverUpdatedEventName();

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            return $this->webhookService->confirmWebhookRegistration($eventName, $_GET['secret']);
        } else {
            if ($request->headers->get('x-cr-calltoken') === $this->webhookService->getEventCallToken($eventName)) {
                $requestContent = json_decode($request->getContent());
                $receiverId = $requestContent->payload->pool_id;
                $this->receiverService->syncCustomer($receiverId);
            }
        }
    }
}