<?php

namespace App\Controller;

use App\Form\CheckAuthenticatorCodeType;
use Doctrine\ORM\EntityManagerInterface;
use Scheb\TwoFactorBundle\Security\TwoFactor\Provider\Google\GoogleAuthenticatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Scheb\TwoFactorBundle\Model\Google\TwoFactorInterface;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\IsGranted;

final class Scheb2FAController extends AbstractController
{

    #[Route(path: '/enable2fa', name: 'app_2fa_enable')]
    #[IsGranted('ROLE_USER')]
    public function enable2FA(Request $request, GoogleAuthenticatorInterface $googleAuthenticator, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        if (!($user instanceof TwoFactorInterface)) {
            throw new NotFoundHttpException('Cannot display QR code');
        }


        if (!$user->getGoogleAuthenticatorSecret()) {
            $secret = $googleAuthenticator->generateSecret();
            $user->setGoogleAuthenticatorSecret($secret);
            $entityManager->persist($user);
            $entityManager->flush();
        }

        if ($request->isMethod('POST')) {
            $code = $request->request->get('auth_code');

            if ($googleAuthenticator->checkCode($user, $code)) {
                $user->setIsTwoFactorActivated(true);
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash('success', '2FA activée avec succès!');

                return $this->redirectToRoute('app_profile');
            }
        }

        $qrCodeContent = $googleAuthenticator->getQRContent($user);
        if (!$qrCodeContent) {
            throw new \RuntimeException('Impossible de générer le contenu du QR code.');
        }

        // Génération du QR code encodé en base64
        $qrCodeBase64 = $this->generateQRCode($qrCodeContent);

        return $this->render('login/2fa_form.html.twig', [
            'qrCodeBase64' => $qrCodeBase64,
        ]);
    }
    private function generateQRCode(string $qrCodeContent): string
    {
        $qrCode = new QrCode($qrCodeContent);

        $writer = new PngWriter();

        return base64_encode($writer->write($qrCode)->getString());
    }

    #[Route('/2fa_check', name: '2fa_login_check')]
    public function check2FA(Request $request, GoogleAuthenticatorInterface $googleAuthenticator): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        if ($request->isMethod('POST')) {
            $code = $request->request->get('auth_code');

            if ($googleAuthenticator->checkCode($user, $code)) {
                $this->addFlash('success', 'Authentification réussie!');
                return $this->redirectToRoute('app_profile');
            } else {
                $this->addFlash('error', 'Le code est incorrect, veuillez réessayer.');
            }
        }

        return $this->render('login/2fa_setup.html.twig');
    }

    #[Route('/disable_2fa', name: 'app_disable_2fa')]
    public function disable2FA(Request $request, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_profile');
        }

        if ($request->isMethod('POST')) {
            $user->setIsTwoFactorActivated(false);
            $user->setGoogleAuthenticatorSecret(null);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'L\'authentification à deux facteurs a été désactivée avec succès.');

            return $this->redirectToRoute('app_mydrilla');
        }

        return $this->render('login/disable_2fa.html.twig', [
            'user' => $user,
        ]);
    }
}
