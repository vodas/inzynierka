<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Model\AisDecoder;

class AisController extends Controller
{

    define('SHIPTYPE', array(
        0 => 'not available', 1 => 'Reserved for future use', 2 => 'Reserved for future use',
        3 => 'Reserved for future use', 4 => 'Reserved for future use', 5 => 'Reserved for future use',
        6 => 'Reserved for future use', 7 => 'Reserved for future use', 8 => 'Reserved for future use',
        9 => 'Reserved for future use', 10 => 'Reserved for future use', 11 => 'Reserved for future use',
        12 => 'Reserved for future use', 13 => 'Reserved for future use', 14 => 'Reserved for future use',
        15 => 'Reserved for future use', 16 => 'Reserved for future use', 17 => 'Reserved for future use',
        18 => 'Reserved for future use', 19 => 'Reserved for future use', 20 => 'Wing in ground (WIG), all ships of this type',
        21 => 'Wing in ground (WIG), Hazardous category A', 22 => 'Wing in ground (WIG), Hazardous category B',
        23 => 'Wing in ground (WIG), Hazardous category C', 24 => 'Wing in ground (WIG), Hazardous category D',
        25 => 'Wing in ground (WIG), Reserved for future use', 26 => 'Wing in ground (WIG), Reserved for future use',
        27 => 'Wing in ground (WIG), Reserved for future use', 28 => 'Wing in ground (WIG), Reserved for future use',
        29 => 'Wing in ground (WIG), Reserved for future use', 30 => 'Fishing', 31 => 'Towing', 32 => 'Towing: length exceeds 200m or breadth exceeds 25m', 33 => 'Dredging or underwater ops', 34 => 'Diving ops', 35 => 'Military ops', 36 => 'Sailing',
        37 => 'Pleasure Craft', 38 => 'Reserved', 39 => 'Reserved', 40 => 'High speed craft (HSC), all ships of this type',
    ));
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
            $shipDataBuffer = array();
            $ships = array();
        foreach ($rows as &$row) {
            if(substr($row, 0, 1)=='!') {
                $row = str_replace("\r", '', $row);
                $message = $decoder->decode($row);
                
                if ($message != "error" && $message != "bad checksum") {

                    if($message['type']==24 && $message['part']!= 2) {
                        
                    }

                     if ($message['type'] == 5) {
                         $shipDataBuffer[$message['seqid']]['payload'] = $message['binaryPayload'];
                     }
                     if ($message['part'] == 2) {
                        if (array_key_exists($message['seqid'], $shipDataBuffer)) {

                            
                            $shipData = $decoder->decodeType_5($shipDataBuffer[$message['seqid']]['payload'],$message['binaryPayload']);
                            $ships[$shipData['mmsi']] = $shipData;
                            unset($shipDataBuffer[$message['seqid']]);
                        }
                     }
                    
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

                    elseif (in_array($message['type'], array(1,2,3, 18))) {
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
