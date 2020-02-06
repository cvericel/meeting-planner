<?php


namespace App\Controller\Meeting;


use App\Repository\MeetingGuestRepository;
use App\Repository\MeetingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Class MeetingController
 * @package App\Controller\Meeting
 */
class MeetingController extends AbstractController
{
    private $manager;

    private $meetingRepository;

    private $meetingGuestRepository;

    public function __construct(EntityManagerInterface $manager, MeetingRepository $meetingRepository, MeetingGuestRepository $meetingGuestRepository)
    {
        $this->manager = $manager;
        $this->meetingRepository = $meetingRepository;
        $this->meetingGuestRepository = $meetingGuestRepository;
    }

    /**
     *
     * @Route("/meetings", name="meeting.view")
     * @param Security $security
     * @return Response
     */
    public function index (Security $security): Response
    {
        $user = $security->getUser();
        $event = $this->meetingGuestRepository->findMeetingWithUserId($user->getId());
        return new Response(null, 200);

    }
}