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
            $currentTime = null;
        foreach ($rows as $row) {
            if(substr($row, 0, 1)=='!') {
                $row = str_replace("\r", '', $row);
                $message = $decoder->decode($row);
                //dump($row);
                if ($message != 'error' || $message != 'bad checksum') {

                    if ($message['type']==4) {
                        $currentTime = $message['year'];
                        //echo $currentTime;
                    }

                    elseif (in_array($message['type'], array(1,2,3))) {
                        array_push($messages, array('datetime' =>'', 'message' => $message));
                    }
                }
            }
        }
   
        return $this->render('default/zatoka.html.twig',array());
    }

}
