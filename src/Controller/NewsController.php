<?php

namespace App\Controller;

use App\Form\CrudType;
use App\Entity\Articles;
use App\Repository\ArticlesRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request; // Importer la classe httpFoundation\Request

class NewsController extends AbstractController
{
    /**
     * @Route("/", name="app_news", methods={"GET"})
     */
    public function news(ArticlesRepository $data): Response
    {
        return $this->render('news/index.html.twig', [
            'controller_name' => 'NewsController',
            'data' => $data
        ]);
    }

    /**
     * @Route("/create", name="app_create", methods={"GET", "POST"})
     */
    public function create(Request $request): Response
    {
        $crud = new Articles(); // La classe de l'entity Articles
        $form = $this->createForm(CrudType::class, $crud); // Le type a été créé lors de la commande make:
        $form->handleRequest($request);
        if ($form->isSubmitted() == true && $form->isValid() == true) {
            $sendDatabase = $this->getDoctrine()->getManager();
            $sendDatabase->persist($crud);
            $sendDatabase->flush();

            $this->addFlash('notice', 'Soumission réussie !');
            return $this->redirectToRoute('app_news');
        }
        return $this->render('news/createForm.html.twig', [
            'form' => $form->createView() // Le createView() est impératif pour afficher les formulaires.
        ]);
    }
}
