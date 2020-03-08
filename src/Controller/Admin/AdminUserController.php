<?php


namespace App\Controller\Admin;


use App\Entity\User;
use App\Form\EditUserType;
use App\Repository\UserRepository;
use App\Service\UploaderHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
     * @Route("/admin/myaccount/{slug}", name="admin.user.show", requirements={"slug": "[a-z0-9-]*"})
     * @param Request $request
     * @param UploaderHelper $uploaderHelper
     * @return Response
     */
    public function edit (Request $request, UploaderHelper $uploaderHelper) : Response
    {
        //dd(phpinfo());
        /** @var User $user */
        $user = $this->security->getUser();
        $form = $this->createForm(EditUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() and $form->isValid()) {
            /** @var UploadedFile $uploadedFile */
            $uploadedFile = $form['imageFile']->getData();

            // Test pour eviter de rentrer dans l'upload d'une image de profil s'il n'y en a pas de selectioner
            if ($uploadedFile) {
                $newFilename = $uploaderHelper->uploadArticleImage($uploadedFile);
                $user->setImageFilename($newFilename);
            }

            //if description update user
            $description = $form['description']->getData();
            if($description) $user->setDescription($description);

            $this->entityManager->persist($user);
            $this->getDoctrine()->getManager()->flush();
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'current_menu' => 'myaccount',
            'form' => $form->createView()
        ]);
    }
}