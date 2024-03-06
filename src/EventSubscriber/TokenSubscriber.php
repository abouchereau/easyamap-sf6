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

class TokenSubscriber implements EventSubscriberInterface
{
    public function __construct(EntityManagerInterface $entityManager,
                                 UserRepository $usersRepository,
                                 #[CurrentUser] ?User $user) {
        $this->usersRepository = $usersRepository;
        $this->entityManager = $entityManager;
        $this->user = $user;
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
        //s'assurer que la session n'a pas expiré
        $session = new Session();
        if (!$session->has('roles') && $this->user != null) {
            $this->usersRepository->loadRoles($this->user);
        }

    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}