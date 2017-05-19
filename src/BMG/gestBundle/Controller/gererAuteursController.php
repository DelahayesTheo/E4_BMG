<?php

namespace BMG\gestBundle\Controller;

use BMG\gestBundle\BMGgestBundle;
use BMG\gestBundle\Form\AuteurType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use BMG\gestBundle\Entity\Auteur;

class gererAuteursController extends Controller
{
    /**
     * @Route("/listerAuteurs/",
     *     name="bmg_lister_auteur")
     */
    public function listerAction()
    {
        try {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $auteurRepository = $em->getRepository('BMGgestBundle:Auteur');
            $listeAuteurs = $auteurRepository->findAll();

            return $this->render('BMGgestBundle:Auteur:listeAuteur.html.twig', array(
                "lesAuteurs" => $listeAuteurs
            ));
        } catch (\Exception $e) {
            $this->addFlash(
                'error',
                'Erreur dans l\'affichage des auteurs.');

            return $this->render('BMGgestBundle:Default:index.html.twig');
        }
    }

    /**
     * @Route("/consulterAuteur/{id}/",
     *     defaults={"id":000},
     *     name="bmg_consulter_auteur")
     */
    public function consulterAction($id)
    {
        $hasErrors = false;
        if ( $id != '000' ) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $auteurRepository = $em->getRepository('BMGgestBundle:Auteur');
            $lAuteur = $auteurRepository->findOneBy(array('idAuteur' => $id));

            if ( $lAuteur == null ) {
                $this->addFlash(
                    'notice',
                    'Cet auteur n\'existe pas'
                );
                $hasErrors = true;
            }
        } else {
            $this -> addFlash(
                'notice',
                'Aucun auteur n\'as été transmis pour la consultation'
            );
            $hasErrors = true;
        }

        if ( $hasErrors ) {
            return $this->redirectToRoute('bmg_lister_auteur');
        } else {
            return $this->render('BMGgestBundle:Auteur:consulterAuteur.html.twig', array(
                'auteur' => $lAuteur
            ));
        }
    }

    /**
     * @Route("/ajouterAuteur/",
     *     name="bmg_ajouter_auteur")
     */
    public function ajouterAction(Request $request)
    {
        $auteur = new Auteur();

        $form = $this
            ->createForm(AuteurType::class, $auteur);

        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            try{
                $em = $this
                    ->getDoctrine()
                    ->getManager();

                $em->persist($auteur);
                $em->flush();
                $this->addFlash(
                    'notice',
                    'L\'auteur ' . $auteur->getNomAuteur() . ' a bien été ajouté'
                );

                return $this->redirectToRoute('bmg_consulter_auteur', array('id' => $auteur->getIdAuteur()));
            } catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    'Erreur dans l\'ajout'
                );
            }

        }

        return $this->render('BMGgestBundle:Auteur:ajouterAuteur.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/supprimerAuteur/{id}/",
     *     defaults={"id" = 000},
     *     name="bmg_supprimer_auteur")
     */
    public function supprimerAction($id)
    {
        if ($id != "000") {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $auteurRepository = $em->getRepository('BMGgestBundle:Auteur');
            $lAuteur = $auteurRepository->findOneBy(array('idAuteur' => $id));

            if ($lAuteur === NULL) {
                $this->addFlash(
                    'error',
                    "Cet auteur n'existe pas !"
                );
            } else {
                $ouvrages = $lAuteur->getOuvrages();
                if ( count($ouvrages) == 0 ) {
                    $em->remove($lAuteur);
                    $em->flush();
                    $this->addFlash(
                        'notice',
                        'L\'auteur ' . $id . '-' . $lAuteur->getNomAuteur() . ' a été supprimé'
                    );
                } else {
                    $this->addFlash(
                        'error',
                        'L\'auteur à des ouvrages enregistrés, suppression impossible'
                    );
                }
            }
        } else {
            $this->addFlash(
                'error',
                "Aucun auteur n'a été transmis pour la suppression"
            );
        }

        return $this->redirectToRoute('bmg_lister_auteur');
    }

    /**
     * @Route("/modifierAuteur/{id}/",
     *     defaults={"id" = 000},
     *     name="bmg_modifier_auteur")
     */
    public function modifierAction(Request $request, $id)
    {
        $hasErrors = false;
        if ($id != "000") {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $auteurRepository = $em->getRepository('BMGgestBundle:Auteur');
            $lAuteur = $auteurRepository->findOneBy(array('idAuteur' => $id));
            if ( $lAuteur == null ) {
                $this->addFlash(
                    'error',
                    'Ce genre n\'existe pas'
                );
                $hasErrors = true;
            } else {
                $form = $this->createForm(AuteurType::class, $lAuteur);

                $form->handleRequest($request);
                if ( $form->isValid() && $form->isSubmitted() ) {
                    $em->flush();
                    $this->addFlash(
                        'notice',
                        'L\'auteur ' . $id . '-' . $lAuteur->getNomAuteur() . ' à été modifié'
                    );

                    return $this->redirectToRoute('bmg_consulter_auteur', array('id' => $id));
                }
            }
        } else {
            $this->addFlash(
                'error',
                'Aucun auteur n\'as été transmis pour la modification'
            );
            $hasErrors = true;
        }
        if ( $hasErrors ) {
            return $this->redirectToRoute('bmg_lister_auteur');
        } else {
            return $this->render('BMGgestBundle:Auteur:modifierAuteur.html.twig', array(
                "form" => $form->createView()
            ));
        }
    }
}
