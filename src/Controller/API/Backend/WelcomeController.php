<?php

namespace Logeecom\CleverReachPlugin\Controller\API\Backend;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\AuthServiceInterface;
use Shopware\Core\Framework\Api\Response\JsonApiResponse;
use Shopware\Core\Framework\Routing\Annotation\RouteScope;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class WelcomeController
 */
class WelcomeController extends AbstractController
{
    private AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Returns redirect url for CleverReach connection screen
     *
     * @RouteScope(scopes={"api"})
     *
     * @Route(path="/api/v{version}/cleverreach/redirectUrl", name="api.cleverreach.redirectUrl", methods={"GET", "POST"})
     * @Route(path="/api/cleverreach/redirectUrl", name="api.cleverreach.redirectUrl.new", methods={"GET", "POST"})
     *
     * @return JsonApiResponse
     **/
    public function getRedirectUrl(): JsonApiResponse
    {
        return new JsonApiResponse(['redirectUrl' => $this->authService->getRedirectUrl()]);
    }

    /**
     * Returns redirect url for CleverReach connection screen
     *
     * @RouteScope(scopes={"api"})
     *
     * @Route(path="/api/v{version}/cleverreach/callbackUrl", name="api.cleverreach.callbackUrl", defaults={"auth_required"=false, "auth_enabled"=false}, methods={"GET", "POST"})
     * @Route(path="/api/cleverreach/callbackUrl", name="api.cleverreach.callbackUrl.new", defaults={"auth_required"=false, "auth_enabled"=false}, methods={"GET", "POST"})
     *
     * @param Request $request
     * @return void
     */
    public function callbackUri(Request $request): void
    {
        if ($request->get("code")) {
            $this->authService->fetchTokens($request->get("code"));
        }
    }
}
