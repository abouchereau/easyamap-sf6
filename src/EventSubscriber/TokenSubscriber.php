<?php

namespace App\EventSubscriber;

use App\Controller\TokenAuthenticatedController;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class TokenSubscriber implements EventSubscriberInterface
{
   /* public function __construct(
        private array $tokens
    ) {
    }*/

    public function onKernelController(ControllerEvent $event,
                                       EntityManagerInterface $entityManager,
                                       UserRepository $usersRepository,
                                       #[CurrentUser] ?User $user): void
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
        if (!$session->has('roles') && gettype($user)=="object") {
            $usersRepository->loadRoles($user);
        }


    /*    $controller = $event->getController();

        // when a controller class defines multiple action methods, the controller
        // is returned as [$controllerInstance, 'methodName']
        if (is_array($controller)) {
            $controller = $controller[0];
        }

        if ($controller instanceof TokenAuthenticatedController) {
            $token = $event->getRequest()->query->get('token');
            if (!in_array($token, $this->tokens)) {
                throw new AccessDeniedHttpException('This action needs a valid token!');
            }
        }*/
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'onKernelController',
        ];
    }
}