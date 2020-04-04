<?php


namespace App\Controller\User;


use App\Entity\GuestWithAccount;
use App\Entity\GuestWithoutAccount;
use App\Entity\MeetingGuest;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\AvailabilityRepository;
use App\Repository\GuestWithoutAccountRepository;
use App\Repository\MeetingGuestRepository;
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
     * @param GuestWithoutAccountRepository $gwar
     * @param AvailabilityRepository $availabilityRepository
     * @return Response
     */
    public function register (Mailer $mailer, Request $request, UserPasswordEncoderInterface $userPasswordEncoder, EntityManagerInterface $entityManager, GuestWithoutAccountRepository $gwar, AvailabilityRepository $availabilityRepository, MeetingGuestRepository $meetingGuestRepository) : Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $userPasswordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);
            $entityManager->persist($user);
            $entityManager->flush();

            // Maintenant il faut qu'on verifie si un invité existe a ce nom pour les liées
            $guests = $gwar->findAllByEmail($user->getEmail());
            if ($guests) {
                /**
                 * On doit lier les disponibiliter et les invitations
                 */
                foreach ($guests as $guest) {
                    $meetingGuest = $guest->getMeetingGuest();
                    $entityManager->persist($meetingGuest);
                    $meetingGuest->removeGuestWithoutAccount();
                    $guestWithAccount = new GuestWithAccount($meetingGuest, $user);
                    $entityManager->persist($guestWithAccount);
                    $meetingGuest->setGuestWithAccount($guestWithAccount);
                    $entityManager->flush();

                    $entityManager->remove($guest);
                    $entityManager->flush();
                }
            }

            $mailer->sendWelcomeMessage($user);

            return $this->redirectToRoute('login');
        }

        return $this->render('user/register.html.twig', [
            'registration_form' => $form->createView(),
            'current_menu' => 'login'
        ]);
    }
}