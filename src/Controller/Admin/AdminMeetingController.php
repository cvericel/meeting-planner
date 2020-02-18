<?php


namespace App\Controller\Admin;


use App\Entity\Meeting;
use App\Entity\MeetingDate;
use App\Form\InvitationType;
use App\Form\MeetingDateType;
use App\Form\MeetingType;
use App\Repository\GuestRepository;
use App\Repository\MeetingDateRepository;
use App\Repository\MeetingGuestRepository;
use App\Repository\MeetingRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

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
    public function index(): Response
    {
        $meetings = $this->repository->findAll();
        return $this->render('admin/meeting/index.html.twig', [
            'meetings' => $meetings,
            'current_menu' => 'admin'
        ]);
    }

    /**
     * @Route("/admin/meeting/create", name="admin.meeting.create")
     * @param Request $request
     * @param Security $security
     * @return Response
     */
    public function new(Request $request, Security $security): Response
    {
        $meeting = new Meeting();
        $form = $this->createForm(MeetingType::class, $meeting);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $meeting->setUser($security->getUser());
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
     * @param UserRepository $userRepository
     * @param MeetingDateRepository $meetingDateRepository
     * @return Response
     */
    public function edit(Meeting $meeting, Request $request, UserRepository $userRepository, MeetingDateRepository $meetingDateRepository, MeetingGuestRepository $meetingGuestRepository): Response

    {
        //Meeting edit form
        $form = $this->createForm(MeetingType::class, $meeting);
        $form->handleRequest($request);

        //Meeting add new date form
        $meeting_date = new MeetingDate();
        $date_form = $this->createForm(MeetingDateType::class, $meeting_date, [
            'action' => $this->generateUrl('admin.meeting_date.create', [
                'id_meeting' => $meeting->getId()
            ])
        ]);
        $date_form->handleRequest($request);

        /**
         * Ajout de nouvelle date via AJAX
         */
        if ($date_form->isSubmitted() && $date_form->isValid()) {
            $meeting_date->setMeeting($meeting);
            $this->entityManager->persist($meeting_date);
            $this->entityManager->flush();
            $this->addFlash('success', 'Date ajouté');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->flush();
            $this->addFlash('success', 'Bien modifié avec succès');
            return $this->redirectToRoute('admin.meeting.index');
        }

        $meeting_date_list = $meetingDateRepository->findAllById($meeting->getId());
        $meeting_date_guest_list = $meetingGuestRepository->findAllInMeeting($meeting->getId());
        return $this->render('admin/meeting/edit.html.twig', [
            'meeting' => $meeting,
            'meeting_date_list' => $meeting_date_list,
            'meeting_date_guest_list' => $meeting_date_guest_list,
            'form' => $form->createView(),
            'date_form' => $date_form->createView(),
            'current_menu' => 'admin'
        ]);
    }

    /**
     * @Route("/admin/meeting/{id}", name="admin.meeting.delete", methods="DELETE")
     * @param Meeting $meeting
     * @param Request $request
     * @return Response
     */
    public function delete(Meeting $meeting, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete' . $meeting->getId(), $request->get('_token'))) {
            $this->entityManager->remove($meeting);
            $this->entityManager->flush();
            $this->addFlash('success', 'Bien supprimé avec succès');
        }
        return $this->redirectToRoute('admin.meeting.index');

    }
}