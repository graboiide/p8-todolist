<?php

namespace App\Controller;

use App\Entity\Role;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{
    /**
     * @Route("/users", name="user_list")
     * @IsGranted("ROLE_ADMIN")
     * @param UserRepository $userRepository
     * @return Response
     */
    public function listAction(UserRepository $userRepository)
    {
        return $this->render('user/list.html.twig', ['users' => $userRepository->findAll()]);
    }



    /**
     * Ajoute l'utilisateur au role sauf pour ROLE_USER qui est automatiquement ajouter
     * @param User $user
     * @param EntityManagerInterface $manager
     */
    private function addRoles(User $user,EntityManagerInterface $manager)
    {
        if($this->isGranted("ROLE_ADMIN"))
            foreach ($user->getUserRoles() as $role){
                if($role->getTitle() !== "ROLE_USER"){
                    $role->addUser($user);
                    $manager->persist($role);
                }
            }
    }

    /**
     * @Route("/users/{id}/edit", name="user_edit")
     * @param User $userEdit
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     * @Security ("is_granted('ROLE_ADMIN') or ( is_granted('ROLE_USER') and user === userEdit )")
     */
    public function editAction(User $userEdit, Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $manager)
    {
        $form = $this->createForm(UserType::class, $userEdit);
        $roles = $this->test($userEdit->getUserRoles()->toArray());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Role $role */
            foreach ($roles as $role){
                    $role->removeUser($userEdit);
                    $manager->persist($role);
            }
            $this->addRoles($userEdit,$manager);

            $password = $encoder->encodePassword($userEdit, $userEdit->getPassword());
            $userEdit->setPassword($password);
            $manager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");
            if($this->isGranted('ROLE_ADMIN'))
                return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $userEdit]);
    }
    /**
     * @Route("/users/create", name="user_create")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */
    public function createAction(Request $request,UserPasswordEncoderInterface $encoder,EntityManagerInterface $manager)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //seul les admins peuvent changer le role d'un utilisateur
            if($this->isGranted("ROLE_ADMIN"))
                $this->addRoles($user,$manager);
            $user->setPassword($encoder->encodePassword($user,$user->getPassword()));
            $manager->persist($user);
            $manager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

             return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }
    private function test($roles){
        return $roles;
    }
}
