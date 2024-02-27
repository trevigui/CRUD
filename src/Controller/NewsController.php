<?php

// Aller voir les fichiers de cours sur le GitHub de Jérémie.
// Les protocoles HTTP pour update et supprimer (PUT et DELETE) ne sont pas à utiliser pour faire un CRUD avec Symfony !!!

namespace App\Controller;

use App\Form\CrudType;
use App\Entity\Articles; //Pour instancier la classe Articles contenues dedans
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
    public function news(ArticlesRepository $repo): Response
    {
        #$data = $this->getDoctrine()->getRepository(Crud::class)->findAll();
        $data = $repo->findAll();
        return $this->render('news/index.html.twig', [
            'controller_name' => 'NewsController',
            'data' => $data
        ]);
    }

    /**
     * @Route("/create", name="app_create", methods={"GET","POST"})
     */
    public function create(Request $request): Response
    { // C'est toujours obligatoire de spécifier une classe et le type de formualaire renvoyant vers le formulaire créé.
        $crud = new Articles(); // La classe de l'entity Articles, je crée une nouvelle entrée dans mon tableau ici, createForm s'occupe de la peupler.
        $form = $this->createForm(CrudType::class, $crud); // Le type a été créé lors de la commande make: //CrudType a été créé dans le dossier Form lorsqu'on make:form. //ne pas oublier d'indiquer l'instaciation de $crud ("sinon erreur Symfony SQL can't be null").

        $form->handleRequest($request); // Gestion des erreurs et affichage sur le formulaire directement !
        if ($form->isSubmitted() == true && $form->isValid() == true) {
            $sendDatabase = $this->getDoctrine()->getManager();
            $sendDatabase->persist($crud);
            $sendDatabase->flush();

            $this->addFlash('monErreur', 'Soumission réussie !'); //monErreur sera rappelé plus tard, un addFlash() par route. (Une route C, R U, D, chacune a son propre message addflash(). )
            return $this->redirectToRoute('app_news');
        }
        return $this->render('news/createForm.html.twig', [
            'form' => $form->createView() // Le createView() est impératif pour afficher les formulaires.
        ]);
    }

    /** 
     * @Route ("/update/{id}", name="update", methods={"GET", "POST"})
     */
    // POST est obligatoire pour effectuer la modification
    public function update($id, Request $request): Response
    {
        $crud = $this->getDoctrine()->getRepository(Articles::class)->find($id); //taxi into repo into entity as class into colonneVar
        $form = $this->createForm(CrudType::class, $crud); // Quand tu ne sais pas quoi mettre comme variable avant une méthode, c'est qu'il faut mettre $this.
        $form->handleRequest($request);
        if ($form->isSubmitted() == true && $form->isValid() == true) { //When you want to check if something is valid, you first have to make sure it was submitted. handlerequest does the former but checking if it is valid is a good idea.
            $sendDatabase = $this->getDoctrine()->getManager();
            $sendDatabase->persist($crud);
            $sendDatabase->flush();

            $this->addFlash('monErreur', 'Soumission réussie !');
            return $this->redirectToRoute('app_news');
        }
        return $this->render('news/updateForm.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route ("/delete/{id}", name="delete", methods={"GET", "POST"})
     * Above id is only indicative, it is in no way related to the code below.
     */
    public function delete($id): Response
    {
        $crud = $this->getDoctrine()->getRepository(Articles::class)->find($id); //in Doctrine, add : repo into entity into class into colonne. this $id is the important one, it's the one stored in the Entity (the actual column)
        // Pas de handleRequest car il n'y a pas besoin de query la DB, on ne modifie que la doctrine (donc la vraie DB en même temps)
        $sendDatabase = $this->getDoctrine()->getManager(); //getting the new data into the taxi, manager n'est rien de plus que le taxi dans Doctrine (qui est lui le translation layer vers SQL)
        $sendDatabase->remove($crud); // remove() au lieu de persist()
        $sendDatabase->flush();

        $this->addFlash('monErreur', 'Suppression réussie !');
        return $this->redirectToRoute('app_news');
    }



    /**
     * @Route("news/create2", name="create2", methods={"GET", "POST"})
     * J'essaie de faire la nouvelle méthode sans getDoctrine()
     */
    public function create2(Request $request): Response
    {
        $newElement = new Articles();
        $form = $this->createForm(CrudType::class, $newElement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $toBeSent = $this->getDoctrine()->getManager();
            $toBeSent->persist($newElement);
            $toBeSent->flush();

            $this->addFlash('monErreur', 'Soumission réussie !'); //monErreur sera rappelé plus tard, un addFlash() par route. (Une route C, R U, D, chacune a son propre message addflash(). )
            return $this->redirectToRoute('app_news');
        }
        return $this->render('news/createForm.html.twig', [
            'form' => $form->createView() // Le createView() est impératif pour afficher les formulaires.
        ]);
    }
}
