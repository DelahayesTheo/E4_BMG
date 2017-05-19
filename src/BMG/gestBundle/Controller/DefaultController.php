<?php

namespace BMG\gestBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\Session;

class DefaultController extends Controller
{
    /**
     * @Route("/",
     *     name="bmg_index")
     */
    public function indexAction()
    {
        $session = $this->get('session');

        $session->set('id', 9999);
        $session->set('nom', 'Dupont');
        $session->set('prenom', 'Jean');
        return $this->render('BMGgestBundle:Default:index.html.twig');
    }
}
