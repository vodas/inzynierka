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
        41 => 'High speed craft (HSC), Hazardous category A', 42 => 'High speed craft (HSC), Hazardous category B', 43 => 'High speed craft (HSC), Hazardous category C', 44 => 'High speed craft (HSC), Hazardous category D', 45 => 'High speed craft (HSC), Reserved for future use', 46 => 'High speed craft (HSC), Reserved for future use', 47 => 'High speed craft (HSC), Reserved for future use', 48 => 'High speed craft (HSC), Reserved for future use', 49 => 'High speed craft (HSC), Reserved for future use', 50 => 'Pilot Vessel', 51 => 'Search and Rescue vessel', 52 -> 'Tug', 53 => 'Port Tender', 54 => 'Anti-pollution equipment', 55 => 'Law Enforcement', 56 => 'Spare - Local Vessel', 57 => 'Spare - Local Vessel',
        58 => 'Medical Transport', 59 => 'Noncombatant ship according to RR Resolution No. 18', 60 => 'Passenger, all ships of this type', 61 => 'Passenger, Hazardous category A', 62 => 'Passenger, Hazardous category B', 63 => 'Passenger, Hazardous category C', 64 => 'Passenger, Hazardous category D', 65 => 'Passenger, Reserved for future use', 66 => 'Passenger, Reserved for future use', 67 => 'Passenger, Reserved for future use', 68 => 'Passenger, Reserved for future use', 69 => 'Passenger, Reserved for future use', 70 => 'Cargo, all ships of this type', 71 => 'Cargo, Hazardous category A', 72 => 'Cargo, Hazardous category B', 73 => 'Cargo, Hazardous category C', 74 => 'Cargo, Hazardous category D', 75 => 'Cargo, Reserved for future use', 76 => 'Cargo, Reserved for future use', 77 => 'Cargo, Reserved for future use', 78 => 'Cargo, Reserved for future use', 79 => 'Cargo, No additional information', 80 => 'Tanker, all ships of this type', 81 => 'Tanker, Hazardous category A', 82 => 'Tanker, Hazardous category B', 83 => 'Tanker, Hazardous category C', 84 => 'Tanker, Hazardous category D', 85 => 'Tanker, Reserved for future use', 86 => 'Tanker, Reserved for future use', 87 => 'Tanker, Reserved for future use', 88 => 'Tanker, Reserved for future use', 89 => 'Tanker, No additional information', 90 => 'Other Type, all ships of this type', 91 => 'Other Type, Hazardous category A', 92 => 'Other Type, Hazardous category B', 93 => 'Other Type, Hazardous category C', 94 => 'Other Type, Hazardous category D', 95 => 'Other Type, Reserved for future use', 96 => 'Other Type, Reserved for future use', 97 => 'Other Type, Reserved for future use', 98 => 'Other Type, Reserved for future use', 99 => 'Other Type, no additional information'



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
