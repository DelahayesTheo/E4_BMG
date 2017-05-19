<?php

namespace BMG\gestBundle\Controller;

use BMG\gestBundle\BMGgestBundle;
use BMG\gestBundle\Form\AuteurOuvrageType;
use BMG\gestBundle\Form\AuteurType;
use BMG\gestBundle\Form\OuvrageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

use BMG\gestBundle\Entity\Ouvrage;

class gererOuvragesController extends Controller
{
    /**
     * @Route("/listerOuvrage/",
     *     name="bmg_lister_ouvrage")
     */
    public function listerAction()
    {
       try {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $ouvrageRepository = $em->getRepository('BMGgestBundle:Ouvrage');
            $listeOuvrages = $ouvrageRepository->findAll();

            return $this->render('BMGgestBundle:Ouvrage:listeOuvrage.html.twig', array(
                "lesOuvrages" => $listeOuvrages
            ));
        } catch (\Exception $e) {
            $this->addFlash(
                'error',
                'Erreur dans l\'affichage des ouvrages.');

            return $this->render('BMGgestBundle:Default:index.html.twig');
        }
    }

    /**
     * @Route("/consulterOuvrage/{id}/",
     *     defaults={"id":000},
     *     name="bmg_consulter_ouvrage")
     */
    public function consulterAction($id)
    {
        $hasErrors = false;
        if ( $id != '000' ) {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $ouvrageRepository = $em->getRepository('BMGgestBundle:Ouvrage');
            $lOuvrage = $ouvrageRepository->findOneBy(array('noOuvrage' => $id));

            if ( $lOuvrage == null ) {
                $this->addFlash(
                    'notice',
                    'Cet ouvrage n\'existe pas'
                );
                $hasErrors = true;
            }
        } else {
            $this -> addFlash(
                'notice',
                'Aucun ouvrage n\'as été transmis pour la consultation'
            );
            $hasErrors = true;
        }

        if ( $hasErrors ) {
            return $this->redirectToRoute('bmg_lister_ouvrage');
        } else {
            return $this->render('BMGgestBundle:Ouvrage:consulterOuvrage.html.twig', array(
                'ouvrage' => $lOuvrage
            ));
        }
    }

    /**
     * @Route("/ajouterAuteurOuvrage/{id}/",
     *     defaults={"id":000},
     *     name="bmg_ajouter_auteur_ouvrage")
     */
    public function ajouterAuteurOuvrageAction(Request $request, $id)
    {
        $hasErrors = false;
        if ($id != '000') {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $ouvrageRepository = $em->getRepository('BMGgestBundle:Ouvrage');
            $lOuvrage = $ouvrageRepository->findOneBy(array('noOuvrage' => $id));

            if ( $lOuvrage == null ) {
                $this->addFlash(
                    'notice',
                    'Cet ouvrage n\'existe pas'
                );
                $hasErrors = true;
            } else {
                $form = $this->createForm(AuteurOuvrageType::class, $lOuvrage);

            }

        }
    }

    /**
     * @Route("/ajouterOuvrage/",
     *     name="bmg_ajouter_ouvrage")
     */
    public function ajouterAction(Request $request)
    {
        $ouvrage = new Ouvrage();

        $form = $this
            ->createForm(OuvrageType::class, $ouvrage);

        $form->handleRequest($request);
        if ($form->isValid() && $form->isSubmitted()) {
            //try{
                $em = $this
                    ->getDoctrine()
                    ->getManager();

                if ($ouvrage->getDateAcquisition() > new \DateTime()) {
                    $this->addFlash(
                        'error',
                        'La date ne peut pas être supérieur a la date d\'aujourd\'hui'
                    );
                    return $this->redirectToRoute('bmg_ajouter_ouvrage');
                }
                $em->persist($ouvrage);
                $em->flush();

                $this->addFlash(
                    'notice',
                    'L\'auteur ' . $ouvrage->getTitre() . ' a bien été ajouté'
                );

                return $this->redirectToRoute('bmg_consulter_ouvrage', array('id' => $ouvrage->getNoOuvrage()));
            /*} catch (\Exception $e) {
                $this->addFlash(
                    'error',
                    'Erreur dans l\'ajout'
                );
            }*/

        }

        return $this->render('BMGgestBundle:Ouvrage:ajouterOuvrage.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/modifierOuvrage/{id}",
     *     defaults={"id"=000},
     *     name="bmg_modifier_ouvrage")
     */
    public function modifierAction(Request $request, $id)
    {
        $hasErrors = false;
        if ($id != "000") {
            $em = $this
                ->getDoctrine()
                ->getManager();

            $ouvrageRepository = $em->getRepository('BMGgestBundle:Ouvrage');
            $lOuvrage = $ouvrageRepository->findOneBy(array('noOuvrage' => $id));
            if ( $lOuvrage == null ) {
                $this->addFlash(
                    'error',
                    'Ce genre n\'existe pas'
                );
                $hasErrors = true;
            } else {
                $form = $this->createForm(OuvrageType::class, $lOuvrage);

                $form->handleRequest($request);
                if ( $form->isValid() && $form->isSubmitted() ) {
                    $em->flush();
                    $this->addFlash(
                        'notice',
                        'L\'ouvrage ' . $id . '-' . $lOuvrage->getTitre() . ' à été modifié'
                    );

                    return $this->redirectToRoute('bmg_consulter_ouvrage', array('id' => $id));
                }
            }
        } else {
            $this->addFlash(
                'error',
                'Aucun ouvrage n\'as été transmis pour la modification'
            );
            $hasErrors = true;
        }
        if ( $hasErrors ) {
            return $this->redirectToRoute('bmg_lister_auteur');
        } else {
            return $this->render('BMGgestBundle:Ouvrage:modifierOuvrage.html.twig', array(
                "form" => $form->createView()
            ));
        }
    }

    /**
     * @Route("/supprimerOuvrage/{id}",
     *     defaults={"id"=000},
     *     name="bmg_supprimer_ouvrage")
     */
    public function supprimerAction($id)
    {
        if ($id != "000") {
            $em = $this
                ->getDoctrine()
                ->getManager();
            $auteurRepository = $em->getRepository('BMGgestBundle:Ouvrage');
            $lOuvrage = $auteurRepository->findOneBy(array('noOuvrage' => $id));

            if ($lOuvrage === NULL) {
                $this->addFlash(
                    'error',
                    "Cet ouvrage n'existe pas !"
                );
            } else {
                $prets = $em->getRepository('BMGgestBundle:Pret')->findBy(array(
                    'ouvrage' => $id,
                    'dateRet' => null
                    ));
                if ( count($prets) == 0 ) {
                    $em->remove($lOuvrage);
                    $em->flush();
                    $this->addFlash(
                        'notice',
                        'L\'ouvrage ' . $id . '-' . $lOuvrage->getTitre() . ' a été supprimé'
                    );
                } else {
                    $this->addFlash(
                        'error',
                        'L\'ouvrage est référencé par un prêt, suppression impossible'
                    );
                }
            }
        } else {
            $this->addFlash(
                'error',
                "Aucun ouvrage n'a été transmis pour la suppression"
            );
        }

        return $this->redirectToRoute('bmg_lister_ouvrage');
    }
}