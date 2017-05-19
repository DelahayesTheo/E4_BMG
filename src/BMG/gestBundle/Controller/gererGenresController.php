<?php

namespace BMG\gestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use BMG\gestBundle\Entity\Genre;
use BMG\gestBundle\Form\GenreType;
use BMG\gestBundle\Form\GenreEditType;

class gererGenresController extends Controller
{
    /**
     * @Route("/listerGenres/",
     *     name="bmg_lister_genre")
     */
    public function indexAction()
    {
        try {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $genreRepository = $em->getRepository('BMGgestBundle:Genre');
            $listeGenres = $genreRepository->findAll();

            return $this->render('BMGgestBundle:Genre:listeGenres.html.twig', array(
                "lesGenres" => $listeGenres
            ));
        } catch (\Exception $e) {
            $this->addFlash(
                'error',
                'Erreur dans l\'affichage des genres.');

            return $this->render('BMGgestBundle:Default:index.html.twig');
        }

    }

    /**
     * @Route("/ajouterGenre/",
     *     name="bmg_ajouter_genre")
     */
    public function ajouterAction(Request $request)
    {
        $genre = new Genre();

        $form = $this
            ->createForm(GenreType::class, $genre);

        if ($form->handleRequest($request)->isValid()) {
            try{
                $genre->setCodeGenre(strtoupper($genre->getCodeGenre()));
                $em = $this
                    ->getDoctrine()
                    ->getManager();

                $em->persist($genre);

                $em->flush();

                $this->addFlash(
                    'notice',
                    'Le genre ' . $genre->getCodeGenre() . '-' . $genre->getLibGenre() . ' a bien été ajouté'
                );

                return $this->render('BMGgestBundle:Genre:ajouterGenre.html.twig', array(
                    "form" => $form->createView()
                ));
            } catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    'Erreur dans l\'ajout, il existe déjà un genre avec ce code ou le code est de plus de 3 caractères !'
                );
            }
        }

        return $this->render('BMGgestBundle:Genre:ajouterGenre.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/consulterGenre/{id}",
     *     defaults={"id" = 000},
     *     name="bmg_consulter_genre")
     */
    public function consulterAction($id)
    {
        $hasErrors = false;
        if ( $id != "000" ) {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $genreRepository = $em->getRepository('BMGgestBundle:Genre');
            $leGenre = $genreRepository->findOneBy(array('codeGenre'=>$id));
            if ($leGenre === NULL) {
                $this->addFlash(
                    'error',
                    "Ce genre n'existe pas !"
                );
                $hasErrors = true;
            }
        } else {
            $this->addFlash(
                'error',
                "Aucun genre n'a été transmis pour consultation"
            );
            $hasErrors = true;
        }

        if ($hasErrors) {
            return $this->redirectToRoute('bmg_lister_genre');
        } else {
            return $this->render('BMGgestBundle:Genre:consulterGenre.html.twig', array(
                "leGenre" => $leGenre
            ));
        }
    }

    /**
     * @Route("/modifierGenre/{id}",
     *     defaults={"id" = 000},
     *     name="bmg_modifier_genre")
     */
    public function modifierAction($id, Request $request)
    {
        $hasErrors = false;
        if ($id != "000") {
            $id = strtoupper($id);
            $em = $this
                ->getDoctrine()
                ->getManager();

            $genreRepository = $em->getRepository('BMGgestBundle:Genre');
            $leGenre = $genreRepository->find($id);

            if ($leGenre == NULL) {
                $this->addFlash(
                    'error',
                    'Ce genre n\'existe pas'
                );
                $hasErrors = true;
            }
        } else {
            $this->addFlash(
                'error',
                "Aucun genre n'a été transmis pour modification"
            );
            $hasErrors = true;
        }

        if ($hasErrors) {
            return $this->redirectToRoute('bmg_lister_genre');
        } else {
            $form = $this->createForm(GenreEditType::class, $leGenre);

            if ($form->handleRequest($request)->isValid()) {
                $em->persist($leGenre);
                $em->flush();
                $this->addFlash(
                    'notice',
                    'Le genre ' . $leGenre->getCodeGenre() . '-' .$leGenre->getLibGenre() . ' a été modifié'
                );
                return $this->redirectToRoute('bmg_consulter_genre', array('id' => $leGenre->getCodeGenre()));
            }
        }
        return $this->render('BMGgestBundle:Genre:modifierGenre.html.twig', array(
            "form" => $form->createView()
        ));

    }

    /**
     * @Route("/supprimerGenre/{id}",
     *     defaults={"id" = 000},
     *     name="bmg_supprimer_genre")
     */
    public function supprimerAction($id)
    {
        $hasErrors = false;
        if ( $id != "000" ) {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $genreRepository = $em->getRepository('BMGgestBundle:Genre');
            $leGenre = $genreRepository->findOneBy(array('codeGenre'=>$id));
            if ($leGenre === NULL) {
                $this->addFlash(
                    'error',
                    "Ce genre n'existe pas !"
                );
            } else {
                $ouvrageRepository = $em->getRepository('BMGgestBundle:Ouvrage');
                $ouvrageDuGenre = $ouvrageRepository->findByGenre($id);
                if ($ouvrageDuGenre == NULL) {
                    $em ->remove($leGenre);
                    $em->flush();
                    $this->addFlash(
                        'notice',
                        'Le genre ' . $id . '-' . $leGenre->getLibGenre() . ' a été supprimé !'
                    );
                } else {
                    $this->addFlash(
                        'error',
                        'Le genre à des ouvrages enregistrés, suppression impossible'
                    );
                }
            }
        } else {
            $this->addFlash(
                'error',
                "Aucun genre n'a été transmis pour suppression"
            );
        }


        return $this->redirectToRoute('bmg_lister_genre');
    }
}