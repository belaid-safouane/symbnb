<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends Controller
{
    /**
     * permet d'afficher et de gérer le formulaire de connexion
     * 
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils)
    {
        $error = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();
        return $this->render('account/login.html.twig',[
            'hasError' => $error != null,
            'username' => $username
        ]);
    }

    /**
     * permet de se deconnecter
     * 
     * @Route("/logout", name="account_logout")
     *
     * @return void
     */
    public function logout(){

    }

/**
 * permet d'afficher le formulaire d'inscription
 * 
 * @Route("/register", name="account_register")
 *
 * @return Response
 */
    public function register(Request $request, ObjectManager $manager, UserPasswordEncoderInterface $encoder)
    {
        $user =new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $hash = $encoder->encodePassword($user, $user->getHash());
            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a bien été créé !"
            );
            return $this->redirectToRoute('account_login');
        }

        return $this->render('account/registration.html.twig',[
            'form' => $form->createView()
        ]);
    }
/**
 * permet d'afficher et de traiter le formulaire de profile
 * 
 * @Route("/account/profile", name="account_profile")
 * @IsGranted("ROLE_USER")
 *
 * @return Response
 */
    public function profile(Request $request, ObjectManager $manager){
        $user = $this->getUser();

        $form = $this->createForm(AccountType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($user);
            $manager->flush();       

            $this->addFlash(
                'success',
                "Les données du profile ont été enregistré avec succés"
            );
         }
        return $this->render('account/profile.html.twig',[
            'form' => $form->createView()
        ]);
    }
/**
 * Permet de modifier le mot de passe
 * 
 * @Route("/account/password-update", name="account-password")
 * 
 * @IsGranted("ROLE_USER")
 *
 * @return Response
 */
    public function updatePassword(Request $request, UserPasswordEncoderInterface $encoder, ObjectManager $manager){
        $passwordUpdate = new PasswordUpdate();

        $user=$this->getUser();

        $form = $this->createForm(PasswordUpdateType::class,$passwordUpdate);
     
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            // verifier que le oldPasswird du formulaire soit le meme que le password de l'user
            if(!password_verify($passwordUpdate->getOldPassword(),$user->getHash())){
            //Gérer l'erreur
            $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe !"));

            } else {
                $newPassword = $passwordUpdate->getNewPassword();
                $hash = $encoder->encodePassword($user, $newPassword);

                $user->setHash($hash);
                $manager->persist($user);
                $manager->flush(); 
                $this->addFlash(
                    'success',
                    "Les données du profile ont été enregistré avec succés"
                );
                return $this->redirectToRoute('homepage');
            }
         }
        return $this->render('account/password.html.twig',[
            'form' => $form->createView()
        ]);
    }
/**
 * Permet de d'afficher le profil de l'utilisateur connecté
 * 
 * @Route("/account",name="account_index")
 * 
 * @IsGranted("ROLE_USER")
 *
 * @return Response
 */
    public function myAccount(){
        return $this->render('user/index.html.twig',[
            'user'=>$this->getUser()
        ]);
    }
}
