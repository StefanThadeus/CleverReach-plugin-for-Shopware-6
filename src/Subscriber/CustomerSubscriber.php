<?php

namespace Logeecom\CleverReachPlugin\Subscriber;

use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\ReceiverServiceInterface;
use Logeecom\CleverReachPlugin\BusinessLogic\Interfaces\SynchronizationServiceInterface;
use Shopware\Core\Checkout\Customer\CustomerEvents;
use Shopware\Core\Checkout\Customer\Event\CustomerRegisterEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityDeletedEvent;
use Shopware\Core\Framework\DataAbstractionLayer\Event\EntityWrittenEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;

class CustomerSubscriber implements EventSubscriberInterface
{
    private static $customerEmail = '';

    private ReceiverServiceInterface $receiverService;

    public function __construct(ReceiverServiceInterface $receiverService)
    {
        $this->receiverService = $receiverService;
    }

    public static function getSubscribedEvents(): array
    {
        return
            [
                CustomerEvents::CUSTOMER_WRITTEN_EVENT => 'onCustomerWritten',
                CustomerEvents::CUSTOMER_REGISTER_EVENT => 'onCustomerRegistered',
                CustomerEvents::CUSTOMER_DELETED_EVENT => 'onCustomerDeleted',
                ControllerEvent::class => 'saveDataForDelete',
            ];
    }

    public function onCustomerWritten(EntityWrittenEvent $event): void
    {
        $idArray = $event->getIds();
        if (!count($idArray)) {
            return;
        }

        $id = $idArray[0];

        $email = $this->receiverService->getReceiverEmail($id);
        if ($email !== self::$customerEmail) {
            $this->receiverService->syncReceiverEmailChange(self::$customerEmail, $email);
        }
        $this->receiverService->syncReceiver($id);
    }

    public function onCustomerRegistered(CustomerRegisterEvent $event): void
    {
        $this->receiverService->syncReceiver($event->getCustomerId());
    }

    public function onCustomerDeleted(EntityDeletedEvent $event): void
    {
        $this->receiverService->removeReceiverByEmail(self::$customerEmail);
    }

    public function saveDataForDelete(ControllerEvent $controllerEvent): void
    {
        $request = $controllerEvent->getRequest();
        $routeName = $request->get('_route');
        if ($routeName === 'api.customer.delete' || $routeName === 'api.customer.update') {
            $path = $request->get('path');
            // check if route contains subpaths
            if (!strpos($path, '/')) {
                self::$customerEmail = $this->receiverService->getReceiverEmail($path);
            }
        }
    }
}