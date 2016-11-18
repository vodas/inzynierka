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

            $returnArr = array();
            if ($request->request->get('datetime') != ''){
            $requestedDate = new \DateTime($request->request->get('datetime'));
            $requestedTimestamp =$requestedDate->getTimestamp();
            $requestedBefore = $requestedTimestamp - 600;

            $decoder = new AisDecoder();
            $txtFile = file_get_contents('/var/www/html/inzynierka/var/ais/aisdata.txt');
            $rows = explode("\n", $txtFile);
            $messages = array();
            $currentTime = null;
        foreach ($rows as &$row) {
            if(substr($row, 0, 1)=='!') {
                $row = str_replace("\r", '', $row);
                $message = $decoder->decode($row);
                //dump($row);
                
                if ($message != "error" && $message != "bad checksum") {
                    
                    if ($message['type']==4 && $message['year']!=''&&$message['year']<2017) {

                        if(strlen($message['month'])==1) {
                            $message['month']='0'.$message['month'];
                        }

                        if(strlen($message['day'])==1) {
                            $message['day']='0'.$message['day'];
                        }

                        if(strlen($message['hour'])==1) {
                            $message['hour']='0'.$message['hour'];
                        }

                        if(strlen($message['minute'])==1) {
                            $message['minute']='0'.$message['minute'];
                        }

                         if(strlen($message['second'])==1) {
                            $message['second']='0'.$message['second'];
                        }

                        $date = new \DateTime($message['year'].'-'.$message['month'].'-'.$message['day'].' '.$message['hour'].':'.$message['minute'].':'.$message['second']);
                

                        $currentTime = $date->getTimestamp();
                    }

                    elseif (in_array($message['type'], array(1,2,3))) {
                       $message['time'] = $currentTime;
                    if($message['time']>=$requestedBefore&& $message['time']<=$requestedTimestamp) {
                        if(in_array($message['mmsi'], array_column($returnArr, 'mmsi'))) {
                            foreach($returnArr as &$arr) {
                                if ($arr['mmsi']==$message['mmsi']) {
                                    $arr['longitude'] = $message['longitude']; 
                                    $arr['latitude'] = $message['latitude'];
                                }
                            }
                        } else {
                            array_push($returnArr, $message);
                        }
                    }
                    }
                }
            }
        }


        
    }
      
        return $this->render('default/zatoka.html.twig',array('points' =>$returnArr));
    }

}
