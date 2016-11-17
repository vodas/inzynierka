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
            $decoder = new AisDecoder();
            $txtFile = file_get_contents('/var/www/html/application/var/ais/aisdata.txt');
            $rows = explode("\n", $txtFile);
            $messages = array();
        foreach ($rows as $row) {
            if(substr($row, 0, 1)=='!') {
                $message = $decoder->decode($row);
                array_push($messages, array('datetime' =>'', 'message' => ''));
            }
        }
   
        return $this->render('default/zatoka.html.twig',array());
    }

}
