<?php
declare(strict_types = 1);

namespace App\Controller;

use App\DataRequest\LostPasswordDataRequest;
use App\DataRequest\NewPasswordDataRequest;
use App\Form\LostPasswordFormType;
use App\Form\NewPasswordFormType;
use App\Repository\UserRepository;
use App\Service\Mailing\PasswordResetMailService;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    private UserRepository $userRepository;

    private TranslatorInterface $translator;

    private PasswordResetMailService $passwordResetMailService;

    private UserPasswordEncoderInterface $userPasswordEncoder;

    /**
     * SecurityController constructor.
     * @param UserRepository $userRepository
     * @param TranslatorInterface $translator
     * @param PasswordResetMailService $passwordResetMailService
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     */
    public function __construct(
        UserRepository $userRepository,
        TranslatorInterface $translator,
        PasswordResetMailService $passwordResetMailService,
        UserPasswordEncoderInterface $userPasswordEncoder
    ) {
        $this->userRepository = $userRepository;
        $this->translator = $translator;
        $this->passwordResetMailService = $passwordResetMailService;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
//         if ($this->getUser()) {
//             return $this->redirectToRoute('main');
//         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error
        ]);
    }

    /**
     * @throws \Exception
     */
    public function logout(): void
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @param Request $request
     * @return Response
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function lostPassword(Request $request): Response
    {
        $lostPasswordDataRequest = new LostPasswordDataRequest();
        $lostPasswordForm = $this->createForm(LostPasswordFormType::class, $lostPasswordDataRequest);
        $lostPasswordForm->handleRequest($request);

        if ($lostPasswordForm->isSubmitted() && $lostPasswordForm->isValid()) {
            $user = $this->userRepository->getUserByEmail($lostPasswordDataRequest->email);
            if (!$user) {
                $errEmailDoesNotExist = $this->translator->trans('lostPassword.form.error.email_does_not_exist');
                $lostPasswordForm->addError(new FormError($errEmailDoesNotExist));
            } else {
                $hash = $this->getPasswordResetHash();
                $validTo = (new DateTimeImmutable)->modify('+2 hours');
                $this->userRepository->updatePasswordResetHash($user, $hash, $validTo);
                $this->passwordResetMailService->sendPasswordResetEmail($lostPasswordDataRequest->email, $hash);
                $this->addFlash('success', $this->translator->trans('lostPassword.form.success'));
            }
        }

        return $this->render('security/lostPassword.html.twig', [
            'form' => $lostPasswordForm->createView(),
        ]);
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getPasswordResetHash(): string
    {
        return md5(random_bytes(1000));
    }

    /**
     * @param Request $request
     * @param string $hash
     * @return Response
     * @throws \Exception
     */
    public function newPassword(Request $request, string $hash): Response
    {
        $hashError = '';
        $newPasswordDataRequest = new NewPasswordDataRequest();
        $user = $this->userRepository->getUserByPasswordResetHash($hash);
        if (!$user) {
            $hashError = $this->translator->trans('newPassword.form.hash_not_found');
        }

        $newPasswordForm = $this->createForm(NewPasswordFormType::class, $newPasswordDataRequest);
        $newPasswordForm->handleRequest($request);
        if ($newPasswordForm->isSubmitted() && $newPasswordForm->isValid() && $user) {
            $password = $this->userPasswordEncoder->encodePassword($user, $newPasswordDataRequest->password);
            $this->userRepository->upgradePassword($user, $password);

            $message = $this->translator->trans('newPassword.form.password_changed');
            $this->addFlash('success', $message);
            return $this->redirectToRoute('app_login');
        }

        return $this->render('security/newPassword.html.twig', [
            'form' => $newPasswordForm->createView(),
            'hashError' => $hashError,
        ]);
    }
}
