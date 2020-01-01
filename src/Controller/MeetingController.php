<?php


namespace App\Controller;

use App\Entity\Meeting;
use App\Repository\MeetingRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
        $meeting[0]->setTitle("via l'objet manager");
        $this->entityManager->flush();
        dump($meeting);
        return $this->render('meeting/index.html.twig', [
            'meetings' => $meeting,
            'current_menu' => 'meeting'
        ]);
    }

    /**
     * @Route("/reunion/{slug}-{id}", name="meeting.show", requirements={"slug": "[a-z0-9-]*"})
     * @param Meeting $meeting
     * @param $slug
     * @return Response
     */
    public function show (Meeting $meeting, $slug) : Response
    {
        $meetingSlug = $meeting->getSlug();
        if ($meetingSlug !== $slug) {
            return $this->redirectToRoute('meeting.show', [
                'id' => $meeting->getId(),
                'slug' => $meetingSlug
            ], 301);
        }
        return $this->render('meeting/show.html.twig', [
            'meeting' => $meeting,
            'current_menu' => 'meeting'
        ]);
    }
}