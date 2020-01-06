<?php


namespace App\Controller\Admin;


use App\Entity\Meeting;
use App\Form\MeetingType;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminMeetingController extends AbstractController
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
     * @Route("/admin", name="admin.meeting.index")
     * @return Response
     */
    public function index () : Response
    {
        $meetings = $this->repository->findAll();
        return $this->render('admin/meeting/index.html.twig', compact('meetings'));
    }

    /**
     * @Route("/admin/meeting/create", name="admin.meeting.create")
     * @param Request $request
     * @return Response
     */
    public function new (Request $request) : Response
    {
        $meeting = new Meeting();
        $form = $this->createForm(MeetingType::class, $meeting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($meeting);
            $this->entityManager->flush();
            $this->addFlash('success', 'Bien crée avec succès');

            return $this->redirectToRoute('admin.meeting.index');
        }
        return $this->render('admin/meeting/create.html.twig', [
            'meeting' => $meeting,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/meeting/{id}", name="admin.meeting.edit", methods="GET|POST")
     * @param Meeting $meeting
     * @param Request $request
     * @return Response
     */
    public function edit (Meeting $meeting, Request $request) : Response
    {
        $form = $this->createForm(MeetingType::class, $meeting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Bien modifié avec succès');
            return $this->redirectToRoute('admin.meeting.index');
        }
        return $this->render('admin/meeting/edit.html.twig', [
            'meeting' => $meeting,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/admin/meeting/{id}", name="admin.meeting.delete", methods="DELETE")
     * @param Meeting $meeting
     * @return Response
     */
    public function delete (Meeting $meeting, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $meeting->getId(), $request->get('_token'))) {
            $this->entityManager->remove($meeting);
            $this->entityManager->flush();
            $this->addFlash('success', 'Bien supprimé avec succès');

        }
        return $this->redirectToRoute('admin.meeting.index');

    }
}