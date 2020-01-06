<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminMeetingController extends AbstractController
{
    /**
     * @Route("/admin/meeting", name="admin_meeting")
     */
    public function index()
    {
        return $this->render('admin_meeting/index.html.twig', [
            'controller_name' => 'AdminMeetingController',
        ]);
    }
}
