<?php

namespace App\Controller;
/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use App\Repository\SettingRepository;
use App\Repository\UserRepository;
use App\Util\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Controller used to manage current user. The #[CurrentUser] attribute
 * tells Symfony to inject the currently logged user into the given argument.
 * It can only be used in controllers and it's an alternative to the
 * $this->getUser() method, which still works inside controllers.
 *
 * @author Romain Monteil <monteil.romain@gmail.com>
 */
#[Route('/user'), IsGranted(User::ROLE_USER)]
final class UserController extends AbstractController
{
    #[Route('/edit/{id}', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(
        #[CurrentUser] User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        ?string $id = null
    ): Response {
        $form = $this->createForm(UserType::class, $user, ["with_address"=>$id!=null]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'user.updated_successfully');

            return $this->redirectToRoute('user_edit', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/change-password', name: 'user_change_password', methods: ['GET', 'POST'])]
    public function changePassword(
        #[CurrentUser] User $user,
        Request $request,
        EntityManagerInterface $entityManager,
        LogoutUrlGenerator $logoutUrlGenerator,
    ): Response {
        $form = $this->createForm(ChangePasswordType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirect($logoutUrlGenerator->getLogoutPath());
        }

        return $this->render('user/change_password.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/', name: 'user', methods: ['GET']), IsGranted(User::ROLE_ADMIN)]
    public function index(
        #[CurrentUser] User $user,
        UserRepository $userRepository,
        SettingRepository $settingRepository
    ) {
        $entities = $userRepository->findAllOrderByLastname();
        $withAddress = $settingRepository->get('useAddress', $_SERVER['APP_ENV']);
        return $this->render('User/index.html.twig', array(
            'entities' => $entities,
            'withAddress' => $withAddress
        ));
    }

    #[Route('/create', name: 'user_create', methods: ['POST']), IsGranted(User::ROLE_ADMIN)]
    public function create(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity->setCreatedAt(new \DateTime());
            $em->persist($entity);
            $em->flush();
            $sendMail = $form['sendMail']->getData();
            $mailSent = 'no';
            if ($sendMail) {
                $mailSent = $this->sendMail($entity->getEmail(),$entity->getUsername(),$entity->getPassword());
            }



            $this->get('session')->getFlashBag()->add('notice', 'Les données ont été mises à jour.');
            if ($mailSent !== 'no') {
                if ($mailSent)
                    $this->get('session')->getFlashBag()->add('notice', 'Un mail a été envoyé à l\'adhérent.');
                else
                    $this->get('session')->getFlashBag()->add('error', 'Echec lors de l\'envoi du mail.');
            }
            return $this->redirect($this->generateUrl('user'));
        }
        else {
            $this->get('session')->getFlashBag()->add('error', 'Problème lors de l\'enregistrement des données '.$form->getErrors(true, false));
        }
        return $this->render('User/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }


    #[Route('/new', name: 'user_new', methods:  ['GET', 'POST']), IsGranted(User::ROLE_ADMIN)]
    public function new(SettingRepository $settingRepository,
                        EntityManagerInterface $entityManager,
                        Request $request)
    {
        $entity = new User();
        $form = $this->createForm(UserType::class, $entity, array(
            'method' => 'POST',
            'is_new' => true,
            'with_address' => $settingRepository->get('useAddress', $_SERVER['APP_ENV']),
            'from_admin'=> true));
        //$all = $request->request->all($form->getName());
        //$all['username'] = "youpi";
        //$request->request->replace($all);
        $form->handleRequest($request);
        //$form->add('submit', SubmitType::class, array('label' => 'Create'));
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                die("iop");
                $entityManager->flush();
                $this->addFlash('success', 'L\'utilisateur a été ajouté.');

             /*   $mailSent = $this->sendMail($entity->getEmail(),$entity->getUsername(),$entity->getPassword());
                if ($mailSent)
                    $this->addFlash('notice', 'Un mail a été envoyé à l\'adhérent avec ses identifiants.');
                else
                    $this->addFlash('error', 'Echec lors de l\'envoi du mail à l\'adhérent.');*/
                return $this->redirect($this->generateUrl('user'));
            }
            else {
                $this->addFlash('error', 'Problème lors de l\'enregistrement des données '.$form->getErrors(true, false));
                return $this->render('User/new.html.twig', array(
                    'entity' => $entity,
                    'form'   => $form->createView(),
                ));
            }


            //return $this->redirectToRoute('user_new', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('User/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }




    public function userEdit()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entity = $this->get('security.token_storage')->getToken()->getUser();
        $editForm = $this->createEditForm($entity);
        $editForm->remove('isAdmin');
        $editForm->add('submit_adherent', SubmitType::class, array('label' => 'Update'));
        return $this->render('User/userEdit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView()
        ));
    }

    /**
     * Edits an existing User entity.
     *
     */
    public function update(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('App\Entity\User')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }
        $form_values = $request->get('amap_orderbundle_user');
        $adherent = isset($form_values['submit_adherent']);

        //   $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->add('submit', SubmitType::class, array('label' => 'Update'));
        $editForm->handleRequest($request);
        $canBeDeleted = $em->getRepository('App\Entity\User')->canBeDeleted($id);

        if ($editForm->isValid()) {
            $em->flush();
            $this->get('session')->getFlashBag()->add('notice', 'Les données ont été mises à jour.');
            if ($adherent)
                return $this->redirect($this->generateUrl('informations_adherent'));
            else
                return $this->redirect($this->generateUrl('user_edit', array('id' => $id)));
        }
        else {
            $this->get('session')->getFlashBag()->add('error', 'Problème lors de l\'enregistrement des données '.$editForm->getErrors(true, false));
        }
        return $this->render('User/edit.html.twig', array(
            'entity'      => $entity,
            'form'   => $editForm->createView(),
            'can_be_deleted' => $canBeDeleted
            //  'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a User entity.
     *
     */
    public function delete(Request $request, $id)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('App\Entity\User')->find($id);

        if (!$entity) {
            $this->get('session')->getFlashBag()->add('error', 'Problème lors de la suppression');
            throw $this->createNotFoundException('Unable to find User entity.');
        } else {
            $this->get('session')->getFlashBag()->add('notice', 'L\'élément a été supprimé.');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('user'));
    }

    public function activate($id, $active)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository('App\Entity\User')->find($id);
        $entity->setIsActive($active);
        $em->persist($entity);
        $em->flush();
        return $this->redirect($this->generateUrl('user_edit',array('id' => $id)));
    }

    protected function sendMail($email, $lastname, $password) {
        //création d'un mot de passe temporaire à modifier lors de la 1ere connexion

        //$url = 'http://contrats.la-riche-en-bio.com';
        $msg = $this->renderView('Emails/new_user.html.twig',
            array('lastname' => $lastname,'password' => $password)
        );


        $message = (new \Swift_Message())
            ->setSubject('easyamap : identifiants connexion')
            ->setFrom(array('ne_pas_repondre@easyamap.fr' => "easyamap"))
            ->setTo($email)
            ->setBody($msg);
        $mailer = $this->get('mailer');
        return $mailer->send($message);
    }

    #[Route('/donnees_personnelles', name: 'donnees_personnelles', methods: ['GET'])]
    public function displayCurrentUser(
        #[CurrentUser] User $user
    ) {
        return $this->render('user/show.html.twig', array('user' => $user));
    }

    #[Route('/donnees_personnelles/edit', name: 'donnees_personnelles_edit', methods: ['GET','POST'])]
    public function editCurrentUser(
        #[CurrentUser] User $user,
        SettingRepository $settingRep,
        EntityManagerInterface $entityManager,
        Request $request,
    )
    {
        $withAddress = $settingRep->get('useAddress', $_SERVER['APP_ENV']);
        $tab = [
            'with_address' => $withAddress,
            'is_new' => false,
            'from_admin' => false
        ];
        $form = $this->createForm(UserType::class, $user, $tab);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('donnees_personnelles', [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('user/edit.html.twig', array(
            'entity'      => $user,
            'form'   => $form,
        ));
    }

    #[Route('/donnees_personnelles/mot_de_passe', name: 'donnees_personnelles_mot_de_passe', methods: ['GET','POST'])]
    public function editPassword(
        #[CurrentUser] User $user,
        EntityManagerInterface $entityManager,
        Request $request,
        UserPasswordHasherInterface $passwordHasher
    ) {

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
           /* if (!$this->_mdpIsValid($data['password'])) {

                $this->addFlash('error','Le mot de passe doit respecter les exigences de sécurité.');
            }
            else {*/
            $hashedPassword = $passwordHasher->hashPassword(
                $user,
                $data['password']
            );
            $user->setPassword($hashedPassword);
            $entityManager->flush();
            $this->addFlash('success','Le mot de passe a été mis à jour.');
            return $this->redirectToRoute('donnees_personnelles', [], Response::HTTP_SEE_OTHER);

          /*  }*/
        }

        return $this->render('user/change_password.html.twig', array(
            'entity'      => $user,
            'form'   => $form,
        ));
    }



}
