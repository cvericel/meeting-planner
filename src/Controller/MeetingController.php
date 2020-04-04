<?php


namespace App\Controller;

use App\Entity\Meeting;
use App\Repository\AvailabilityRepository;
use App\Repository\MeetingDateRepository;
use App\Repository\MeetingGuestRepository;
use App\Repository\MeetingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class MeetingController extends AbstractController
{
    /**
     * @var MeetingRepository
     */
    private $repository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(MeetingRepository $repository, EntityManagerInterface $entityManager)
    {
        $this->repository = $repository;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/reunion", name="meeting.index")
     * @return Response
     */
    public function index () : Response
    {
        $meeting = $this->repository->findAllById(1);

        return $this->render('meeting/index.html.twig', [
            'meetings' => $meeting,
            'current_menu' => 'meeting'
        ]);
    }

    /**
     * @Route("/reunion/{slug}-{id}", name="meeting.show", requirements={"slug": "[a-z0-9-]*"})
     * @param UserRepository $userRepository
     * @param MeetingDateRepository $meetingDateRepository
     * @param Meeting $meeting
     * @param Request $request
     * @param $slug
     * @param MeetingGuestRepository $meetingGuestRepository
     * @param AvailabilityRepository $availabilityRepository
     * @param Security $security
     * @return Response
     */
    public function show (UserRepository $userRepository, MeetingDateRepository $meetingDateRepository,
                          Meeting $meeting, Request $request, $slug,
                          MeetingGuestRepository $meetingGuestRepository,
                          AvailabilityRepository $availabilityRepository,
                          Security $security) : Response
    {
        $meetingSlug = $meeting->getSlug();
        $meetingDate = $meetingDateRepository->findAllById($meeting->getId());

        if ($meetingSlug !== $slug) {
            return $this->redirectToRoute('meeting.show', [
                'id' => $meeting->getId(),
                'slug' => $meetingSlug
            ], 301);
        }

        if($security->getUser()) {
            if ($security->getUser() === $meeting->getUser())
            {

                return $this->render('admin/meeting/show.html.twig', [
                    'meeting' => $meeting,
                    'meetingDate' => $meetingDate,
                    'current_menu' => 'admin',
                ]);

            } else {

                $guest = $meetingGuestRepository->findUserInMeetingGuest($meeting, $security->getUser()->getId());
                $availability = $availabilityRepository->findAllAvailabilityInMeeting($guest->getId(), $meeting);

                return $this->render('meeting/show.html.twig', [
                    'meeting' => $meeting,
                    'meetingDate' => $meetingDate,
                    'current_menu' => 'admin',
                    'availability' => $availability,
                    'guest' => $guest
                ]);
            }
        } else {
            $token = $request->get('t');
            $guest = $meetingGuestRepository->findInMeetingGuestWithToken($meeting, $token);
            if ($guest) {
                $availability = $availabilityRepository->findAllAvailabilityInMeeting($guest->getId(), $meeting);
                return $this->render('meeting/show.html.twig', [
                    'meeting' => $meeting,
                    'meetingDate' => $meetingDate,
                    'current_menu' => 'admin',
                    'availability' => $availability,
                    'guest' => $guest
                ]);
            }
            return $this->render('error/forbidden.html.twig');
        }
    }


}