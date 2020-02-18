<?php


namespace App\Controller\Admin;


use App\Entity\GuestWithAccount;
use App\Entity\GuestWithoutAccount;
use App\Entity\MeetingGuest;
use App\Repository\MeetingGuestRepository;
use App\Repository\MeetingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/admin/meeting-{id_meeting}/guest")
 */
class AdminGuestController extends AbstractController
{


    private $entityManager;

    /**
     * @var Security
     */
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @Route("/addGuest", name="admin.meeting_guest.add", methods={"POST"})
     * @param Request $request
     * @param UserRepository $userRepository
     * @param $id_meeting
     * @param EntityManagerInterface $entityManager
     * @param MeetingRepository $meetingRepository
     * @param MeetingGuestRepository $meetingGuestRepository
     * @return Response
     */
    public function addGuest (Request $request, UserRepository $userRepository, $id_meeting,
                              EntityManagerInterface $entityManager,
                              MeetingRepository $meetingRepository,
                                MeetingGuestRepository $meetingGuestRepository): Response
    {
        $meeting = $meetingRepository->find($id_meeting);
        $email = $request->get('q');
        if ($meeting->getUser() === $meeting->getUser()) {
            $user = $userRepository->findOneByEmail($email);
            //we test if the user has already been invited
            if ($user) {
                $already_invited = $meetingGuestRepository->findAlreadyInWithAccount($user->getId(), $id_meeting);
                if ($already_invited) {
                    $html = "Already invited";

                    return new Response($html, 400);
                } else {

                    // Create meeting guest
                    $meeting_guest = new MeetingGuest();
                    $entityManager->persist($meeting_guest);
                    $meeting_guest->setMeeting($meeting);

                    // Create guest with account
                    $guestWithAccount = new GuestWithAccount($meeting_guest, $user);
                    $entityManager->persist($guestWithAccount);
                    $meeting_guest->setGuestWithAccount($guestWithAccount);
                    $entityManager->flush();


                    return $this->render('admin/meeting/__guestRow.html.twig', [
                        'meeting' => $meeting,
                        'guest' => $meeting_guest
                    ]);
                }
            } else {
                // invited outside person
                $already_invited = $meetingGuestRepository->findAlreadyInWithoutAccount($email, $id_meeting);
                if ($already_invited) {
                    $html = "Already invited";

                    return new Response($html, 400);
                } else {
                    // Create meeting guest
                    $meeting_guest = new MeetingGuest();
                    $entityManager->persist($meeting_guest);
                    $meeting_guest->setMeeting($meeting);

                    // Create guest without account
                    $guestWithoutAccount = new GuestWithoutAccount($meeting_guest, $email);
                    $meeting_guest->setGuestWithoutAccount($guestWithoutAccount);
                    $entityManager->flush();

                    return $this->render('admin/meeting/__guestRow.html.twig', [
                        'meeting' => $meeting,
                        'guest' => $meeting_guest
                    ]);
                }
            }

        }

        return new Response("Invalid email !", 400);
    }

    /**
     * Delete a meeting guest from one meeting
     * @Route("/{id}", name="admin.meeting_guest.delete", methods={"DELETE"})
     * @param Security $security
     * @param MeetingGuest $meetingGuest
     * @return Response
     */
    public function delete (Security $security, MeetingGuest $meetingGuest): Response
    {
        $authUser = $security->getUser()->getId();
        $authorUser = $meetingGuest->getMeeting()->getUser()->getId();
        if ($authUser === $authorUser) {
            // Remove meeting guest
            $this->entityManager->remove($meetingGuest);
            $this->entityManager->flush();

            return new Response(null, 204);
        }

        return new Response(null, 403);
    }

    /**
     * @Route("/{id}/update", name="admin.meeting_guest.update", methods={"POST"})
     * @param MeetingGuest $meetingGuest
     * @return Response
     */
    public function update(MeetingGuest $meetingGuest): Response
    {
        $meeting = $meetingGuest->getMeeting();
        if ($meeting->getUser() == $this->security->getUser()) {
            $this->entityManager->persist($meetingGuest);
            if ($meetingGuest->getRole() == "GUEST") {
                $meetingGuest->setRole("ADMIN");
            } else {
                $meetingGuest->setRole("GUEST");
            }
            $this->entityManager->flush();
            return $this->render('admin/meeting/__guestUpdateTd.html.twig', [
                'id_meeting' => $meeting->getId(),
                'guest' => $meetingGuest
            ]);
        } else {
            return new Response(null, 400);
        }

    }
}