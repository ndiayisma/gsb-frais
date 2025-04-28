<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_USER')]
final class TwoFactorController extends AbstractController
{
    #[Route(path: '/enable2fa', name: 'app_2fa_enable')]
    #[IsGranted('ROLE_USER')]
    public function enable2fa(Request $request, EntityManagerInterface $entityManager, GoogleAuthenticatorInterface $googleAuthenticator): Response
    {
        $connectedUser = $this->getUser();


        if (!($connectedUser instanceof TwoFactorInterface)) {
            throw new NotFoundHttpException('Cannot display QR code');
        }


        $form = $this->createForm(CheckAuthenticatorCodeType::class);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $code = $form->get('authenticatorCode')->getData();
            if ($googleAuthenticator->checkCode($connectedUser, strval($code))) {
                $this->addFlash('success', 'Authentification à 2 facteurs activée avec succès.');
            } else {
                $connectedUser->setGoogleAuthenticatorSecret(null);
                $entityManager->persist($connectedUser);
                $entityManager->flush();
                $this->addFlash('danger', 'Code invalide. L\'authentification à 2 facteurs n\'a pas pu être activée. Flashez à nouveau le QR code.');
            }
            return $this->redirectToRoute('app_2fa_enable');
        } else {
            if ($connectedUser->isGoogleAuthenticatorEnabled()) {
                if (count($request->getSession()->getFlashBag()->all()) === 0){
                    $this->addFlash('info', 'Authentification à 2 facteurs déjà activée.');
                }
            } else {
                $secret = $googleAuthenticator->generateSecret();
                $connectedUser->setGoogleAuthenticatorSecret($secret);
                $entityManager->persist($connectedUser);
                $entityManager->flush();
            }
        }


        $qrCodeContent = $googleAuthenticator->getQRContent($connectedUser);


        return $this->render('security/enable2fa.html.twig', [
            'qrCodeContent' => $qrCodeContent,
            'form' => $form,
            ]);


    /*#[Route('/2fa/setup', name: '2fa_setup')]
    public function setup2FA(GoogleAuthenticatorInterface $googleAuthenticator, EntityManagerInterface $entityManager): Response
    {

        $user = $this->getUser();

        if (!$user->isGoogleAuthenticatorEnabled()) {
            $secret = $googleAuthenticator->generateSecret();
            $user->setGoogleAuthenticatorSecret($secret);
            $user->enableGoogle();
            $entityManager->persist($user);
            $entityManager->flush();
        }

        $qrCodeUrl = $googleAuthenticator->getQRContent($user);

        return $this->render('two_factor/2fa_setup.html.twig', [
            'qrCodeUrl' => $qrCodeUrl,
        ]);*/
    }
}
