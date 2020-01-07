<?php


namespace App\Controller\Admin;


use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/admin/Mon-compte/{slug}", name="admin.user.show", requirements={"slug": "[a-z0-9-]*"})
     * @param Request $request
     * @return Response
     */
    public function edit (Request $request) : Response
    {
        $user = $this->security->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            $this->entityManager->persist($user);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('admin.meeting.index');
        }
        return $this->render('admin/user/index.html.twig', [
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}