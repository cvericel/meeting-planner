<?php

namespace App\Controller;

use App\Entity\MeetingDate;
use App\Form\MeetingDateType;
use App\Repository\MeetingDateRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/meeting/date")
 */
class MeetingDateController extends AbstractController
{
    /**
     * @Route("/", name="meeting_date_index", methods={"GET"})
     * @param MeetingDateRepository $meetingDateRepository
     * @return Response
     */
    public function index(MeetingDateRepository $meetingDateRepository): Response
    {
        return $this->render('meeting_date/index.html.twig', [
            'meeting_dates' => $meetingDateRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="meeting_date_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $meetingDate = new MeetingDate();
        $form = $this->createForm(MeetingDateType::class, $meetingDate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($meetingDate);
            $entityManager->flush();

            return $this->redirectToRoute('meeting_date_index');
        }

        return $this->render('meeting_date/new.html.twig', [
            'meeting_date' => $meetingDate,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="meeting_date_show", methods={"GET"})
     */
    public function show(MeetingDate $meetingDate): Response
    {
        return $this->render('meeting_date/show.html.twig', [
            'meeting_date' => $meetingDate,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="meeting_date_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, MeetingDate $meetingDate): Response
    {
        $form = $this->createForm(MeetingDateType::class, $meetingDate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('meeting_date_index');
        }

        return $this->render('meeting_date/edit.html.twig', [
            'meeting_date' => $meetingDate,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="meeting_date_delete", methods={"DELETE"})
     */
    public function delete(Request $request, MeetingDate $meetingDate): Response
    {
        if ($this->isCsrfTokenValid('delete'.$meetingDate->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($meetingDate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('meeting_date_index');
    }
}
