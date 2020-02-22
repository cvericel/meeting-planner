<?php


namespace App\Controller\User;


use App\Entity\User;
use App\Form\UserType;
use App\Service\Mailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="user.register")
     * @param Mailer $mailer
     * @param Request $request
     * @param UserPasswordEncoderInterface $userPasswordEncoder
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function register (Mailer $mailer, Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager) : Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $userPasswordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $entityManager->persist($user);
            $entityManager->flush();

            $mailer->sendWelcomeMessage($user);

            $this->addFlash("success", "Vous êtes bien inscrit !");

            return $this->redirectToRoute('login');
        }

        return $this->render('user/register.html.twig', [
            'registration_form' => $form->createView(),
            'current_menu' => 'login'
        ]);
    }
}