<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\UserType;
use App\Util\PasswordGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Http\Logout\LogoutUrlGenerator;

/**
 * Controller used to manage current user. The #[CurrentUser] attribute
 * tells Symfony to inject the currently logged user into the given argument.
 * It can only be used in controllers and it's an alternative to the
 * $this->getUser() method, which still works inside controllers.
 *
 * @author Romain Monteil <monteil.romain@gmail.com>
 */
#[Route('/profile'), IsGranted(User::ROLE_USER)]
final class UserController extends AbstractController
{
    #[Route('/edit', name: 'user_edit', methods: ['GET', 'POST'])]
    public function edit(
        #[CurrentUser] User $user,
        Request $request,
        EntityManagerInterface $entityManager,
    ): Response {
        $form = $this->createForm(UserType::class, $user);
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

    /**
     * Lists all User entities.
     *
     */
    public function index()
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        //die(var_dump($user->getRoles()));
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('App\Entity\User')->findAllOrderByLastname();

        $withAddress = $em->getRepository('App\Entity\Setting')->get('useAddress', $_SERVER['APP_ENV']);

        return $this->render('User/index.html.twig', array(
            'entities' => $entities,
            'withAddress' => $withAddress
        ));
    }
    /**
     * Creates a new User entity.
     *
     */
    public function create(Request $request)
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

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

    /**
     * Creates a form to create a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(User $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $withAddress = $em->getRepository('App\Entity\Setting')->get('useAddress', $_SERVER['APP_ENV']);
        $form = $this->createForm(UserType::class, $entity, array(
            'action' => $this->generateUrl('user_create'),
            'method' => 'POST',
            'is_new' => true,
            'with_address' => $withAddress
        ));

        $form->add('submit', SubmitType::class, array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new User entity.
     *
     */
    public function new()
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $entity = new User();
        $password = PasswordGenerator::make();
        $entity->setPassword($password);
        $form   = $this->createCreateForm($entity);



        return $this->render('User/new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }



    /**
     * Displays a form to edit an existing User entity.
     *
     */
//    public function edit($id)
//    {
//        $this->denyAccessUnlessGranted('ROLE_ADMIN');
//
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('App\Entity\User')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find User entity.');
//        }
//
//        $editForm = $this->createEditForm($entity);
//        $editForm->add('submit', SubmitType::class, array('label' => 'Update'));
//        $canBeDeleted = $em->getRepository('App\Entity\User')->canBeDeleted($id);
//        // $deleteForm = $this->createDeleteForm($id);
//
//        return $this->render('User/edit.html.twig', array(
//            'entity'      => $entity,
//            'form'   => $editForm->createView(),
//            'can_be_deleted' => $canBeDeleted
//            //   'delete_form' => $deleteForm->createView(),
//        ));
//    }

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
     * Creates a form to edit a User entity.
     *
     * @param User $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(User $entity)
    {
        $em = $this->getDoctrine()->getManager();
        $withAddress = $em->getRepository('App\Entity\Setting')->get('useAddress',$_SERVER['APP_ENV']);
        $form = $this->createForm(UserType::class, $entity, array(
            'action' => $this->generateUrl('user_update', array('id' => $entity->getIdUser())),
            'method' => 'PUT',
            'is_new' => false,
            'with_address' => $withAddress
        ));



        return $form;
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
    public function displayCurrentUser() {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        return $this->render('User/show.html.twig', array('user' => $user));
    }
}
