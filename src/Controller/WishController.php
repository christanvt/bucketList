<?php

namespace App\Controller;

use App\Entity\Wish;
use App\Form\WishType;
use App\Util\Censurator;
use App\Repository\WishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Route("/wish/", name="wish_")
 */
class WishController extends AbstractController
{
    /**
     * @Route("/", name="list")
     */
    public function list(WishRepository $wishRepository): Response
    {
        return $this->render('wish/list.html.twig', [
            'wishs' => $wishRepository->findAll(
                ['date_created' => 'DESC'],
            ),
        ]);
    }

    /**
     *  @Route("/create/", name="create")
     */
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        Censurator $censurator
    ): Response {
        $user = $this->getUser()->getFirstname();
        $wish = new Wish();
        $wish
            ->setDateCreated(new \DateTime())
            ->setAuthor($user);
        $wishForm = $this->createForm(WishType::class, $wish);
        $wishForm->handleRequest($request);

        if ($wishForm->isSubmitted() && $wishForm->isValid()) {
            $wish->setDescription($censurator->purify($wish->getDescription()));
            $entityManager->persist($wish);
            $entityManager->flush();

            $this->addFlash('success', 'Chose à faire bien crée');
            return $this->redirectToRoute('wish_details', ['id' => $wish->getId()]);
        }

        return $this->render('wish/create.html.twig', [
            'wishForm' => $wishForm->createView(),
        ]);
    }

    /**
     *  @Route("/details/{id}/", name="details")
     */
    public function details(int $id, WishRepository $wishRepository): Response
    {
        $wish = $wishRepository->find($id);

        return $this->render('wish/details.html.twig', [
            'controller_name' => 'WishController',
            'wish' => $wish,
        ]);
    }
}
