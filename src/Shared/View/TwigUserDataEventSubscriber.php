<?php

declare(strict_types=1);

namespace App\Shared\View;

use App\Shared\Service\SlugCreator;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Twig\Environment;

class TwigUserDataEventSubscriber implements EventSubscriberInterface
{
    public function __construct(private Environment $twig, private Security $security, private SlugCreator $slugger)
    {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (($user = $this->security->getUser()) === null) {
            return;
        }

        $this->twig->addGlobal('currentUserUsername', $user->getUsername());
        $this->twig->addGlobal('currentUserSlug', $this->slugger->createSlug($user->getUsername()));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}
