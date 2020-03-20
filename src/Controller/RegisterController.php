<?php
declare(strict_types = 1);

namespace App\Controller;

use App\DataRequest\UserRegisterDataRequest;
use App\Entity\User;
use App\Form\RegistrationForm;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegisterController extends AbstractController
{
    private UserPasswordEncoderInterface $userPasswordEncoder;

    private UserRepository $userRepository;

    private TranslatorInterface $translator;

    /**
     * RegisterController constructor.
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param UserRepository $userRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        UserPasswordEncoderInterface $userPasswordEncoder,
        UserRepository $userRepository,
        TranslatorInterface $translator
    ) {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
    }

    public function index(Request $request): Response
    {
        $userRegisterDataRequest = new UserRegisterDataRequest();
        $registerForm = $this->createForm(RegistrationForm::class, $userRegisterDataRequest);
        $registerForm->handleRequest($request);

        if ($registerForm->isSubmitted() && $registerForm->isValid()) {
            if ($this->userRepository->userExists($userRegisterDataRequest->email)) {
                $errEmailExists = $this->translator->trans('register.form.error.email_already_exists');
                $registerForm->addError(new FormError($errEmailExists));
            } else {
                $manager = $this->getDoctrine()->getManager();
                $user = new User();
                $password = $this->userPasswordEncoder->encodePassword($user, $userRegisterDataRequest->password);
                $user->setPassword($password);
                $user->setFullName($userRegisterDataRequest->fullName);
                $user->setEmail($userRegisterDataRequest->email);
                $user->setRoles(['ROLE_USER']);
                $manager->persist($user);
                $manager->flush();

                $this->addFlash('success', $this->translator->trans('register.form.success'));

                return $this->redirectToRoute('app_login');
            }
        }

        return $this->render('register/index.html.twig', [
            'form' => $registerForm->createView(),
        ]);
    }
}
