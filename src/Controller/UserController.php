<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserInformationType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasher;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Validator\Constraints\UserPasswordValidator;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="app_user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_user_new", methods={"GET", "POST"})
     */
    public function new(Request $request, UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($passwordHasher->hashPassword($user,$user->getPassword()));
            $userRepository->add($user);
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('user/new.html.twig', [
            'user' => $user,
            'form' => $form
        ]);
    }

    /**
     * @Route("/{id}", name="app_user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        if($this->getUser()){
            return $this->render('user/show.html.twig', [
                'user' => $user,
            ]);
        }
        return $this->redirectToRoute("app_login");
    }

    /**
     * @Route("/{id}/edit", name="app_user_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, User $user, UserRepository $userRepository,UserPasswordHasherInterface $passwordHasher): Response
    {
        if($this->getUser()){
            $form = $this->createForm(UserInformationType::class, $user,[
                "action" => $this->generateUrl("app_user_edit",["id" => $user->getId()])
            ]);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $userRepository->add($user);
                return $this->redirectToRoute('app_user_show', [
                    "id" => $user->getId()
                ], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('user/edit.html.twig', [
                'user' => $user,
                'form' => $form,
            ]);
        }
        return $this->redirectToRoute("app_login");
    }

    /**
     * @Route("/{id}", name="app_user_delete", methods={"POST"})
     */
    public function delete(Request $request, User $user, UserRepository $userRepository): Response
    {
        if($this->getUser()){
            if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
                $userRepository->remove($user);
            }
            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->redirectToRoute("app_login");
    }

    /**
     * @param User $user
     * @param UserPasswordHasherInterface $passwordHasher
     * @param UserRepository $userRepository
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @Route("/{id}/changePassword",name="app_user_change_password", methods={"GET","POST"})
     */
    public function changePassword(User $user, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository,Request $request):Response{
        $form = $this->createFormBuilder($user,[
            "action" => $this->generateUrl("app_user_change_password",[
                "id" => $user->getId()
            ])
        ])
            ->add("actualPassword",PasswordType::class,[
                "mapped" => false,
                "label" => "Mot de passe actuel"
            ])
            ->add("newPassword",PasswordType::class,[
                "mapped" => false,
                "label" => "Nouveau mot de passe"
            ])
        ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($passwordHasher->isPasswordValid($user,$form->get("actualPassword")->getData())){
                $user->setPassword($passwordHasher->hashPassword($user,$form->get("newPassword")->getData()));
                $userRepository->add($user);
                return $this->redirectToRoute('app_user_show', [
                    "id" => $user->getId()
                ], Response::HTTP_SEE_OTHER);
            }
        }
        return $this->renderForm("user/changePassword.html.twig",[
            "form" => $form
        ]);
    }
}
