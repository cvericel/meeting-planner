<?php


namespace App\Controller\Admin;


use App\Entity\Availability;
use App\Entity\MeetingDate;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminAvailabilityController extends AbstractController
{
    /**
     * @Route("/{id}/cancel", name="admin.availability.cancel", methods={"POST"})
     * @param Availability $availability
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function cancel (Availability $availability, EntityManagerInterface $manager): Response
    {
        $meeting_date = $availability->getMeetingDate();
        $manager->remove($availability);
        $manager->flush();
        return $this->render('meeting/__availability_card.html.twig', [
            'meeting' => $meeting_date->getMeeting(),
            'date' => $meeting_date
        ]);
    }}