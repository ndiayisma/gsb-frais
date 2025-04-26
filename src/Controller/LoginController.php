<?php

namespace App\Controller;

use AllowDynamicProperties;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

#[AllowDynamicProperties] class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/profile', name: 'app_profile')]
    public function profile(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser(); // Assuming the user is logged in
        $this->entityManager = $entityManager;
        if (!$user) {
            throw $this->createAccessDeniedException("Vous devez vous connecter pour accéder à cette page.");
            // Redirect to the login page if the user is not authenticated
        }

        $isTwoFactorActivated = $user->isTwoFactorActivated();

        if ($request->isMethod('POST')) {
            // Get the form type submitted
            $formType = $request->request->get('2fa_form');

            if ($formType === '1') {
                // Redirect to the 2FA configuration page
                return $this->redirectToRoute('app_2fa_enable');
            } elseif ($formType === '0') {
                // If the user is deactivating 2FA
                if ($user->isTwoFactorActivated()) {
                    $user->setIsTwoFactorActivated(false);

                    $this->entityManager->persist($user);
                    $this->entityManager->flush();

                    // Add a flash success message
                    $this->addFlash('success', 'L\'authentification à deux facteurs a été désactivée.');
                }
            }
        }

        // Render the profile page or perform any necessary logic
        return $this->render('login/profile.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/profile/qr_a2f', name: 'app_qr_a2f')]
    public function show2FAQrCode(): Response
    {
        return $this->render('security/2fa_setup.html.twig');
    }




}
