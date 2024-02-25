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
        $crud = new Articles(); // La classe de l'entity Articles
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
        if ($form->isSubmitted() == true && $form->isValid() == true) {
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
     */
    public function delete($id): Response
    {
        $crud = $this->getDoctrine()->getRepository(Articles::class)->find($id); //taxi into repo into entity into class into colonne
        $sendDatabase = $this->getDoctrine()->getManager();
        $sendDatabase->remove($crud);
        $sendDatabase->flush();

        $this->addFlash('monErreur', 'Soumission réussie !');
        return $this->redirectToRoute('app_news');
    }
}





//             Articles.find('title').delete(1)



//             $toRemove = {title:"0"}
//             $sendDatabase->remove($toRemove);
//             $sendDatabase->flush();

//             $this->addFlash('monErreur', 'Soumission réussie !'); //monErreur sera rappelé plus tard, un addFlash() par route. (Une route C, R U, D, chacune a son propre message addflash(). )
//             return $this->redirectToRoute('app_news');
//         }
//         return $this->render('news/createForm.html.twig', [
//             'form' => $form->createView() // Le createView() est impératif pour afficher les formulaires.
//         ]);
//     }
// }
