<?php

namespace App\Controller\Admin;


use App\Entity\MeetingDate;
use App\Form\MeetingDateType;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
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

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
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
