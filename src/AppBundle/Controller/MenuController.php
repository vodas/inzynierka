<?php
namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;



class MenuController extends Controller
{
    
    public function menuAction()
    {
        return $this->render('menu.html.twig', array(
        ));
    }
}
