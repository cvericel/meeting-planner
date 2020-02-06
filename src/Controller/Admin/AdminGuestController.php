<?php


namespace App\Controller\Admin;


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

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        if ($meeting->getUser() === $meeting->getUser()) {
            $email = $request->get('q');
            $user = $userRepository->findOneByEmail($email);
            //we test if the user has already been invited
            if ($user) {
                $already_invited = $meetingGuestRepository->findAlreadyIn($user->getId(), $id_meeting);
                if ($already_invited) {
                    $html = "Already invited";
                    return new Response($html, 400);
                } else {
                    $meeting_guest = new MeetingGuest();
                    $entityManager->persist($meeting_guest);
                    $meeting_guest->setMeeting($meeting);
                    $meeting_guest->setUser($user);
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
}