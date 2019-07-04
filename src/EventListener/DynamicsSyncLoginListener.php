<?php declare(strict_types=1);

namespace Devigner\DynamicsCRMBundle\EventListener;

use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Devigner\DynamicsCRMBundle\Dynamics\ClientTrait;
use Devigner\DynamicsCRMBundle\Entity\DynamicsInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

class DynamicsSyncLoginListener implements EventSubscriberInterface
{
    use ClientTrait;

    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents(): array
    {
        return [
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        ];
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event): void
    {
        $user = $event->getAuthenticationToken()->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }

        if (!$user instanceof DynamicsInterface) {
            return;
        }

        if (!$user->canSync()) {
            return;
        }

        $this->dynamicsConnector->pullEntity($user);
    }
}
