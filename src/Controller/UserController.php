<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ChangeEmailType;
use App\Form\ChangeAddressType;
use App\Form\ChangeLastNameType;
use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/user', name: "app.user")]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $repo
    ) {
    }
    private function checkUser(?User $user): ?RedirectResponse
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app.login');
        }

        if(!$user instanceof User){
            return $this->redirectToRoute('app.user.profil');
        }

        /** @var User $currentUser */   // sert à enlever la fausse erreur que detecte intelephense
        $currentUser = $this->getUser();
        if(!($currentUser->getId() == $user->getId())){
            return $this->redirectToRoute('app.user.profil');
        }
        return null;
    }

    #[Route('', name: '.profil', methods: ['GET'])]
    public function index(?User $user): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app.login');
        }
        $user = $this->getUser();
        return $this->render('user/profil.html.twig', [
            'user' => $user,

        ]);
    }

    #[Route('/modifier-nom/{id}', name: ".changeName", methods: ['GET', 'POST'])]
    public function changeName(?User $user, Request $request): Response|RedirectResponse
    {
        if ($response = $this->checkUser($user)) {
            return $response;
        }
        

        $form = $this->createForm(ChangeLastNameType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repo->save($user, true);

            return $this->redirectToRoute('app.user.profil');
        }

        return $this->render('user/changeName.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/modifier-email/{id}', name: ".changeEmail", methods: ['GET', 'POST'])]
    public function changeEmail(?User $user, Request $request): Response|RedirectResponse
    {
        if ($response = $this->checkUser($user)) {
            return $response;
        }

        $form = $this->createForm(ChangeEmailType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->repo->save($user, true);


            return $this->redirectToRoute('app.user.profil');
        }

        return $this->render('user/changeEmail.html.twig', [
            'form' => $form,
        ]);
    }
    #[Route('/modifier-adresse/{id}',name:'.changeAddress',methods:['GET','POST'])]
    public function changeAddress(?User $user, Request $request): Response|RedirectResponse
    {
        if ($response = $this->checkUser($user)) {
            return $response;
        }
        $form = $this->createForm(ChangeAddressType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $this->repo->save($user, true);
            return $this->redirectToRoute('app.user.profil');
        }
        return $this->render('user/changeAdress.html.twig',[
            'form'=>$form,
        ]);
    }
    #[Route('/modifer-password/{id}', name:'.changePassword', methods:['GET','POST'])]
    public function changePassword(?User $user, Request $request, UserPasswordHasherInterface $passwordHasher) : Response|RedirectResponse
    {
        if ($response = $this->checkUser($user)){
            return $response;
        }

        $form = $this->createForm(ChangePasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData()));
            $this->repo->save($user, true);
            $this->addFlash('success','Mot de passe modifié avec succès');
            return $this->redirectToRoute('app.user.profil');
        }
        return $this->render('user/changePassword.html.twig',[
            'form'=>$form,
        ]);
    }
}
