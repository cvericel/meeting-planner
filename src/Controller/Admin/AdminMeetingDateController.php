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
        $meeting = $meetingRepository->findOneBy(['id' => $id_meeting]);
        if ($this->security->getUser() === $meeting->getUser()) {
            if ($request->isXmlHttpRequest()) {
                // Get the meeting
                // Test if the user is the author
                dump($this->security->getUser());
                dump($meeting->getUser());

                $this->entityManager->persist($meeting);
                $meeting->setChosenDate($meetingDate);
                $this->entityManager->flush();
                return new Response("", 200);
                //TODO sent mail
            } else {
                return $this->render('admin/meeting_date/index.html.twig', [
                    'meeting_dates' => $meetingDate
                ]);
            }
        } else {
            return $this->render('error/forbidden.html.twig');
        }
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


}
