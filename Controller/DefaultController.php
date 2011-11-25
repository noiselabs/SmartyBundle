<?php

namespace NoiseLabs\Bundle\SmartyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController extends Controller
{
    
    public function indexAction($name)
    {
        return $this->render('SmartyBundle:Default:index.html.twig', array('name' => $name));
    }
}
