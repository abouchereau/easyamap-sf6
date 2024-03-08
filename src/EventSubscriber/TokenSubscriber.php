<?php

namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Core\Security;
class TokenSubscriber implements EventSubscriberInterface
{
    public function __construct(EntityManagerInterface $entityManager,
                                 UserRepository $usersRepository,
                                Security $security) {
        $this->usersRepository = $usersRepository;
        $this->entityManager = $entityManager;
    }

    public function onKernelController(ControllerEvent $event, string $what, TraceableEventDispatcher $eventDispatcher): void
    {
       /*  $previous_url = $request->headers->get('referer');
        $user = $this->get('security.token_storage')->getToken()->getUser();
        if (strpos($previous_url, '/contrat/') !== false && gettype($user)=="object") {
            $em = $this->getDoctrine()->getManager();
            $em->getRepository('App\Entity\Booth')->unlockContract($previous_url,$user);
        }
*/

    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}