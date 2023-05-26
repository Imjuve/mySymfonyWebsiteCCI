<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterUserType;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $repo
    ) {
    }
    #[Route('/register', name: 'app.register')]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash('error','Déjà connecté');
            return $this->redirectToRoute('homepage');
        }
        $user = new User();

        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $user->setPassword($passwordHasher->hashPassword($user, $form->get('password')->getData())
        );
        $this->repo->save($user, true);
        $this->addFlash('success', 'Félicitation, vous êtes désormais membre de notre site');
        return $this->redirectToRoute('app.login');
        }
        return $this->render('Frontend/Register/register.html.twig',[
            'form'=> $form,
        ]);
    }
}
