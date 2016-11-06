<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Model\AisDecoder;

class AisController extends Controller
{
    /**
     * @Route("/ais/dekoder", name="dekoder")
     */
    public function dekoderAction(Request $request)
    {
        $aisMessage = $request->request->get('aismessage');
	$message = NULL;
	if ($aisMessage != NULL && $aisMessage != '') {
		$decoder = new AisDecoder();
		$message = $decoder->decode($aisMessage);
	}
        return $this->render('default/decoder.html.twig',array('message' => $message));
    }

    /**
     * @Route("/ais/zatoka", name="single")
     */
    public function zatokaAction(Request $request)
    {
        
        return $this->render('default/zatoka.html.twig',array());
    }

}
