<?php


namespace App\Controller;


use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class AdminUserController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Security
     */
    private $security;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $entityManager, Security $security)
    {
        $this->userRepository = $userRepository;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @Route("/admin/User/{slug}-{id}, name="admin.user.show" requirements={"slug": "[a-z0-9-]*"}))
     * @return Response
     */
    public function edit () : Response
    {
        $user = $this->security->getUser();

        return $this->render('admin/user/index.html.twig', [
            'user' => $user
        ]);
    }
}