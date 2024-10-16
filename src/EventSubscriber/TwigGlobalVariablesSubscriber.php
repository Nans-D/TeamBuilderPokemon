<?php


namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;
use Symfony\Bundle\SecurityBundle\Security;

class TwigGlobalVariablesSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $security;

    public function __construct(Environment $twig, Security $security)
    {
        $this->twig = $twig;
        $this->security = $security;
    }

    public function onKernelController(ControllerEvent $event)
    {
        $user = $this->security->getUser();
        $this->twig->addGlobal('isAuthenticated', $user ? true : false);
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
