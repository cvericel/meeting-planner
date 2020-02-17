<?php

namespace App\Controller\Admin;


use App\Entity\Availability;
use App\Entity\MeetingDate;
use App\Form\MeetingDateType;
use App\Repository\AvailabilityRepository;
use App\Repository\MeetingGuestRepository;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;


/**
 * @Route("/admin/meeting-{id_meeting}/date")
 */
class AdminMeetingDateController extends AbstractController
{
    private $entityManager;

    private $security;

    private $meetingGuestRepository;

    private $availabilityRepository;


    public function __construct(EntityManagerInterface $entityManager, Security $security, MeetingGuestRepository $meetingGuestRepository, AvailabilityRepository $availabilityRepository)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->meetingGuestRepository = $meetingGuestRepository;
        $this->availabilityRepository = $availabilityRepository;
    }

    /**
     * @Route("/{id}", name="admin.meeting_date")
     * @param Request $request
     * @param $id_meeting
     * @return Response
     */
    public function view(Request $request, $id_meeting, MeetingDate $meetingDate, MeetingRepository $meetingRepository): Response
    {
        if ($request->isXmlHttpRequest()) {
            $meeting = $meetingRepository->findOneBy(['id' => $id_meeting]);
            $this->entityManager->persist($meeting);
            $meeting->setChosenDate($meetingDate);
            $this->entityManager->flush();
            return new Response("", 200);
        }
        return $this->render('admin/meeting_date/index.html.twig', [
            'meeting_dates' => $meetingDate
        ]);
    }

    /**
     * @Route("/create", name="admin.meeting_date.create", methods={"POST"})
     * @param Request $request
     * @param $id_meeting
     * @param MeetingRepository $meetingRepository
     * @return Response
     */
    public function create(Request $request, $id_meeting, MeetingRepository $meetingRepository): Response
    {
        $meeting = $meetingRepository->find($id_meeting);

        if ($meeting->getUser() === $meeting->getUser()) {
            $meeting_date = new MeetingDate();
            $form = $this->createForm(MeetingDateType::class, $meeting_date);
            $form->handleRequest($request);

            if ($form->isValid()) {
                $this->entityManager->persist($meeting_date);
                $meeting = $meetingRepository->find($id_meeting);
                $meeting_date->setMeeting($meeting);
                $this->entityManager->flush();

                return $this->render('admin/meeting/__meetingRow.html.twig', [
                    'dates' => $meeting_date,
                    'meeting' => $meeting
                ]);

            } else {
                $html = $this->renderView('admin/meeting_date/__form.html.twig', [
                    'date_form' => $form->createView()
                ]);
                return new Response($html, 400);
            }

        } else {
            return new Response('Accées refusé', 400);
        }
    }

    /**
     * @Route("/{id}", name="admin.meeting_date.delete", methods={"DELETE"})
     * @param MeetingDate $meetingDate
     * @return Response
     */
    public function delete(MeetingDate $meetingDate): Response
    {
        //On vérifie si l'utilisateur connecté est le créateur de la reunion
        $authUser = $this->security->getUser()->getId();
        $user = $meetingDate->getMeeting()->getUser()->getId();
        if ($user == $authUser) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($meetingDate);
            $entityManager->flush();

            return new Response(null, 204);
        }

        return new Response(null, 403);
    }

    /**
     * @Route("/valid-{id}", name="admin.meeting_date.choice.valid", methods={"POST"})
     * @param MeetingDate $meetingDate
     * @return Response
     * @throws Exception
     */
    public function validDate (MeetingDate $meetingDate): Response
    {
        return $this->availability($meetingDate, True);
    }

    /**
     * @Route("/refuse-{id}", name="admin.meeting_date.choice.refuse", methods={"POST"})
     * @param MeetingDate $meetingDate
     * @return Response
     * @throws Exception
     */
    public function refuseDate(MeetingDate $meetingDate): Response
    {
        return $this->availability($meetingDate, False);
    }

    /**
     * Generic function for refuse or valid a date
     * @param MeetingDate $meetingDate
     * @param bool $choice
     * @return Response
     * @throws Exception
     */
    public function availability (MeetingDate $meetingDate, bool $choice): Response
    {
        $meeting = $meetingDate->getMeeting();
        $user = $this->security->getUser();
        // test if user is in the meeting guest
        $guest = $this->meetingGuestRepository->findUserInMeetingGuest($meeting->getId(), $user->getId());
        if ($guest) {
            /**
             * Null if is a first user choice
             * Availability $choice or null
             */
            $alreadyChoice = $this->availabilityRepository->findIfGuestAlreadyChoice($guest->getId(), $meetingDate->getId());
            if ($alreadyChoice) {
                $this->entityManager->persist($alreadyChoice);
                if ($alreadyChoice->getChoice() != $choice) {
                    $alreadyChoice->setChoice($choice);
                    $alreadyChoice->setChosenAt(new \DateTime());
                    $this->entityManager->flush();

                    return $this->render('meeting/__availability_card.html.twig', [
                        'meeting' => $meetingDate->getMeeting(),
                        'date' => $meetingDate,
                        'availability' => $alreadyChoice
                    ]);
                }
            } else {
                $availability = new Availability();
                $this->entityManager->persist($availability);
                $availability->setChoice($choice);
                $availability->setMeetingDate($meetingDate);
                $availability->setMeetingGuest($guest);
                $this->entityManager->flush();

                return $this->render('meeting/__availability_card.html.twig', [
                    'meeting' => $meetingDate->getMeeting(),
                    'date' => $meetingDate,
                    'availability' => $availability
                ]);
            }
        }

        // Same choice than before
        return new Response("", 400);
    }
}
