<?php


namespace App\Controller\Meeting;


use App\Entity\Availability;
use App\Entity\MeetingDate;
use App\Entity\MeetingGuest;
use App\Repository\AvailabilityRepository;
use App\Repository\MeetingDateRepository;
use App\Repository\MeetingGuestRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Class MeetingDateController
 * @package App\Controller\Meeting
 */
class MeetingDateController extends AbstractController
{

    private $entityManager;

    private $security;

    private $meetingGuestRepository;

    private $availabilityRepository;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var MeetingDateRepository
     */
    private $meetingDateRepository;


    public function __construct(EntityManagerInterface $entityManager, Security $security, MeetingGuestRepository $meetingGuestRepository, AvailabilityRepository $availabilityRepository, RequestStack $requestStack, MeetingDateRepository $meetingDateRepository)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->meetingGuestRepository = $meetingGuestRepository;
        $this->availabilityRepository = $availabilityRepository;
        $this->requestStack = $requestStack;
        $this->meetingDateRepository = $meetingDateRepository;
    }

    /**
     * @Route("/accept-{id_meeting_date}/{id}",name="meeting_date.choice.accept", methods={"POST"})
     * @param $id_meeting_date
     * @param MeetingGuest $meetingGuest
     * @return Response
     * @throws Exception
     */
    public function validDate ($id_meeting_date, MeetingGuest $meetingGuest): Response
    {
        if($meetingGuest->getGuestWithAccount()) {
            if ($this->security->getUser() === $meetingGuest->getGuestWithAccount()->getUser()) {
                $meetingDate = $this->meetingDateRepository->findOneBy(['id' => $id_meeting_date]);
                return $this->availability($meetingDate, $meetingGuest, True);
            }
        }
        return new Response("Accees interdit", 400);
    }

    /**
     * @Route("/refuse-{id_meeting_date}/{id}", name="meeting_date.choice.refuse", methods={"POST"})
     * @param $id_meeting_date
     * @param MeetingGuest $meetingGuest
     * @return Response
     * @throws Exception
     */
    public function refuseDate($id_meeting_date, MeetingGuest $meetingGuest): Response
    {
        if($meetingGuest->getGuestWithAccount()) {
            if ($this->security->getUser() === $meetingGuest->getGuestWithAccount()->getUser()) {
                $meetingDate = $this->meetingDateRepository->findOneBy(['id' => $id_meeting_date]);
                return $this->availability($meetingDate, $meetingGuest, False);
            }
        }
        return new Response("Accees interdit", 400);
    }


    /**
     * @Route("/refuse_without_account-{id_meeting_date}/{id}-{token}", name="meeting_date_choice.refuse_without_account", methods={"POST"})
     * @param $id_meeting_date
     * @param MeetingGuest $meetingGuest
     * @param $token
     * @return Response
     * @throws Exception
     */
    public function refuseDateWithoutAccount($id_meeting_date, MeetingGuest $meetingGuest, $token)
    {
        if ($token === $meetingGuest->getGuestWithoutAccount()->getToken()) {
            $meetingDate = $this->meetingDateRepository->findOneBy(['id' => $id_meeting_date]);
            return $this->availability($meetingDate, $meetingGuest, False);
        } else {
            return new Response("Token pas valide", 400);
        }
    }

    /**
     * @Route("/accept_without_account-{id_meeting_date}/{id}-{token}", name="meeting_date_choice.accept_without_account", methods={"POST"})
     * @param $id_meeting_date
     * @param MeetingGuest $meetingGuest
     * @param $token
     * @return Response
     * @throws Exception
     */
    public function acceptDateWithoutAccount($id_meeting_date, MeetingGuest $meetingGuest, $token)
    {
        if ($token === $meetingGuest->getGuestWithoutAccount()->getToken()) {
            $meetingDate = $this->meetingDateRepository->findOneBy(['id' => $id_meeting_date]);
            return $this->availability($meetingDate, $meetingGuest, True);
        } else {
            return new Response("Token pas valide", 400);
        }
    }

    /**
     * Generic function for refuse or valid a date
     * @param MeetingDate $meetingDate
     * @param bool $choice
     * @return Response
     * @throws Exception
     */
    public function availability (MeetingDate $meetingDate, MeetingGuest $meetingGuest, bool $choice): Response
    {

        /**
         * Null if is a first user choice
         * Availability $choice or null
         */
        $alreadyChoice = $this->availabilityRepository->findIfGuestAlreadyChoice($meetingGuest->getId(), $meetingDate->getId());
        if ($alreadyChoice) {
            $this->entityManager->persist($alreadyChoice);
            if ($alreadyChoice->getChoice() != $choice) {
                $alreadyChoice->setChoice($choice);
                $alreadyChoice->setChosenAt(new \DateTime());
                $this->entityManager->flush();

                return $this->render('meeting/__availability_card.html.twig', [
                    'meeting' => $meetingDate->getMeeting(),
                    'date' => $meetingDate,
                    'availability' => $alreadyChoice,
                    'guest' => $meetingGuest
                ]);
            }
        } else {
            $availability = new Availability();
            $this->entityManager->persist($availability);
            $availability->setChoice($choice);
            $availability->setMeetingDate($meetingDate);
            $availability->setMeetingGuest($meetingGuest);
            $this->entityManager->flush();

            return $this->render('meeting/__availability_card.html.twig', [
                'meeting' => $meetingDate->getMeeting(),
                'date' => $meetingDate,
                'availability' => $availability,
                'guest' => $meetingGuest
            ]);
        }

        // Same choice than before
        return new Response("", 400);
    }

    /**
     * @Route("/{id}/cancel", name="availability.cancel", methods={"POST"})
     * @param Availability $availability
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function cancel (Availability $availability, EntityManagerInterface $manager): Response
    {
        if ($this->security->getUser() === $availability->getMeetingGuest()->getGuestWithAccount()->getUser()) {
            $meeting_date = $availability->getMeetingDate();
            $meeting_guest = $availability->getMeetingGuest();
            $manager->remove($availability);
            $manager->flush();
            return $this->render('meeting/__availability_card.html.twig', [
                'meeting' => $meeting_date->getMeeting(),
                'date' => $meeting_date,
                'guest' => $meeting_guest
            ]);
        } else {
            return new Response("", 400);
        }
    }

    /**
     * @Route("/{id}/cancel_without_account-{token}", name="availability.cancel_without_account", methods={"POST"})
     * @param Availability $availability
     * @param EntityManagerInterface $manager
     * @param $token
     * @return Response
     */
    public function cancelWithoutAccount (Availability $availability, EntityManagerInterface $manager, $token): Response
    {
        if($token === $availability->getMeetingGuest()->getGuestWithoutAccount()->getToken()) {
            $meeting_date = $availability->getMeetingDate();
            $meeting_guest = $availability->getMeetingGuest();
            $manager->remove($availability);
            $manager->flush();
            return $this->render('meeting/__availability_card.html.twig', [
                'meeting' => $meeting_date->getMeeting(),
                'date' => $meeting_date,
                'guest' => $meeting_guest
            ]);
        } else {
            return new Response("", 400);
        }
    }
}