<?php
namespace AppBundle\Model;


class AisDecoder {

	const SHIPTYPE = array(
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
        41 => 'High speed craft (HSC), Hazardous category A', 42 => 'High speed craft (HSC), Hazardous category B', 43 => 'High speed craft (HSC), Hazardous category C', 44 => 'High speed craft (HSC), Hazardous category D', 45 => 'High speed craft (HSC), Reserved for future use', 46 => 'High speed craft (HSC), Reserved for future use', 47 => 'High speed craft (HSC), Reserved for future use', 48 => 'High speed craft (HSC), Reserved for future use', 49 => 'High speed craft (HSC), Reserved for future use', 50 => 'Pilot Vessel', 51 => 'Search and Rescue vessel', 52 => 'Tug', 53 => 'Port Tender', 54 => 'Anti-pollution equipment', 55 => 'Law Enforcement', 56 => 'Spare - Local Vessel', 57 => 'Spare - Local Vessel',
        58 => 'Medical Transport', 59 => 'Noncombatant ship according to RR Resolution No. 18', 60 => 'Passenger, all ships of this type', 61 => 'Passenger, Hazardous category A', 62 => 'Passenger, Hazardous category B', 63 => 'Passenger, Hazardous category C', 64 => 'Passenger, Hazardous category D', 65 => 'Passenger, Reserved for future use', 66 => 'Passenger, Reserved for future use', 67 => 'Passenger, Reserved for future use', 68 => 'Passenger, Reserved for future use', 69 => 'Passenger, Reserved for future use', 70 => 'Cargo, all ships of this type', 71 => 'Cargo, Hazardous category A', 72 => 'Cargo, Hazardous category B', 73 => 'Cargo, Hazardous category C', 74 => 'Cargo, Hazardous category D', 75 => 'Cargo, Reserved for future use', 76 => 'Cargo, Reserved for future use', 77 => 'Cargo, Reserved for future use', 78 => 'Cargo, Reserved for future use', 79 => 'Cargo, No additional information', 80 => 'Tanker, all ships of this type', 81 => 'Tanker, Hazardous category A', 82 => 'Tanker, Hazardous category B', 83 => 'Tanker, Hazardous category C', 84 => 'Tanker, Hazardous category D', 85 => 'Tanker, Reserved for future use', 86 => 'Tanker, Reserved for future use', 87 => 'Tanker, Reserved for future use', 88 => 'Tanker, Reserved for future use', 89 => 'Tanker, No additional information', 90 => 'Other Type, all ships of this type', 91 => 'Other Type, Hazardous category A', 92 => 'Other Type, Hazardous category B', 93 => 'Other Type, Hazardous category C', 94 => 'Other Type, Hazardous category D', 95 => 'Other Type, Reserved for future use', 96 => 'Other Type, Reserved for future use', 97 => 'Other Type, Reserved for future use', 98 => 'Other Type, Reserved for future use', 99 => 'Other Type, no additional information');

	public function ordutf8($string, &$offset) {
    $code = ord(substr($string, $offset,1)); 
    if ($code >= 128) {        //otherwise 0xxxxxxx
        if ($code < 224) $bytesnumber = 2;                //110xxxxx
        else if ($code < 240) $bytesnumber = 3;        //1110xxxx
        else if ($code < 248) $bytesnumber = 4;    //11110xxx
        $codetemp = $code - 192 - ($bytesnumber > 2 ? 32 : 0) - ($bytesnumber > 3 ? 16 : 0);
        for ($i = 2; $i <= $bytesnumber; $i++) {
            $offset ++;
            $code2 = ord(substr($string, $offset, 1)) - 128;        //10xxxxxx
            $codetemp = $codetemp*64 + $code2;
        }
        $code = $codetemp;
    }
    $offset += 1;
    if ($offset >= strlen($string)) $offset = -1;
    return $code;
}


	public function decode($aisMessage) {
		$aisArray = explode(',',$aisMessage);
		$id = substr($aisArray[0],0,3);
		if ($id == '!AD') {
		    $typeOfStation = 'MMEA 4.0 Dependent AIS Base Station';
		    } else if ($id == '!AI') {
		    $typeOfStation = 'Mobile AIS station';
		    } else if ($id == '!AN') {
		    $typeOfStation = 'NMEA 4.0 Aid to Navigation AIS station';
		    } else if ($id == '!AR') {
		    $typeOfStation = 'NMEA 4.0 AIS Receiving Station';
		    } else if ($id == '!AS') {
		    $typeOfStation = 'NMEA 4.0 Limited Base Station';
		    } else if ($id == '!AT') {
		    $typeOfStation = 'NMEA 4.0 AIS Transmitting Station';
		    } else if ($id == '!AX') {
		    $typeOfStation = 'NMEA 4.0 Repeater AIS station';
		    } else if ($id == '!BS') {
		    $typeOfStation = 'Base AIS station (deprecated in NMEA 4.0)';
		    } else if ($id == '!SA') {
		    $typeOfStation = 'NMEA 4.0 Physical Shore AIS Station';
		    }
		$countOfFragments = $aisArray[1];
		$fragmentNumber = $aisArray[2];
		$sequentialMessageId = $aisArray[3];
		$radioChannel = $aisArray[4];
		$payload = str_split($aisArray[5]);
		if(array_key_exists(6, $aisArray)) {
			$numberOfFillBits = substr($aisArray[6],0,1);
		} else {
			return "error";
		}
		$checksum = substr($aisArray[6],2,4);
		
		$crc = 0;
		$offset = 0;
		$mess = substr($aisMessage, 0, -3);
		$mess = substr($mess, 1);
		for($i = 0; $i<strlen($mess); $i++) {
			$char = substr($mess,$i,1);
			$crc ^= $this->ordutf8($char,$offset);
		}
		
		$check = strtoupper(dechex($crc));

		if (strlen($check)==1) {
			$check = '0'.$check;
		}
		if ($check == $checksum) {

	

		$ais_map64 = array(
		    '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
		    ':', ';', '<', '=', '>', '?', '@', 'A', 'B', 'C',
		    'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
		    'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W',
		    '`', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i',
		    'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's',
		    't', 'u', 'v', 'w'
		);
		$binaryPayload = '';
		foreach ($payload as $ascChar) {
		    foreach ($ais_map64 as $key => $char) {
			if ($ascChar == $char) {
			    $binaryPayload = $binaryPayload.sprintf( "%06d", decbin( $key ));
			}
		    }
		}
		$message = array();
		$message['seqid'] = $sequentialMessageId;
		$message['part'] = $fragmentNumber;
		$message['binaryPayload'] = $binaryPayload;
		$message['type'] =  bindec(substr($binaryPayload,0,6));
		if(in_array($message['type'], array(1,2,3))) {

			$message['repeatIndicator'] = bindec(substr($binaryPayload,6,2));
			$message['mmsi'] = bindec(substr($binaryPayload,8,30));
			$message['navigationalStatus'] = bindec(substr($binaryPayload,38,4));
			if ($message['navigationalStatus'] == 0) {
			    $message['navigationalStatus'] = 'under way using engine';
			} elseif($message['navigationalStatus'] == 1) {
			    $message['navigationalStatus'] = 'at anchor';
			} elseif($message['navigationalStatus'] == 2) {
			    $message['navigationalStatus'] ='not under command';
			} elseif($message['navigationalStatus'] == 3) {
			    $message['navigationalStatus'] ='restricted maneuverability';
			} elseif($message['navigationalStatus'] == 4) {
			    $message['navigationalStatus'] ='constrained by her draught';
			} elseif($message['navigationalStatus'] == 5) {
			    $message['navigationalStatus'] ='moored';
			} elseif($message['navigationalStatus'] == 6) {
			    $message['navigationalStatus'] ='aground';
			} elseif($message['navigationalStatus'] == 7) {
			    $message['navigationalStatus'] = 'engaged in fishing';
			} elseif($message['navigationalStatus'] == 8) {
			    $message['navigationalStatus'] = ' under way sailing';
			} elseif($message['navigationalStatus'] == 9) {
			    $message['navigationalStatus'] = 'reserved for future amendment of navigational status for ships carrying DG, HS, or MP, or IMO hazard or pollutant category C, high speed craft (HSC)';
			} elseif($message['navigationalStatus'] == 10) {
			    $message['navigationalStatus'] = 'reserved for future amendment of navigational status for ships carrying dangerous goods (DG), harmful substances (HS) or marine pollutants (MP), or IMO hazard or pollutant category A, wing in ground (WIG)';
			} elseif($message['navigationalStatus'] == 11) {
			    $message['navigationalStatus'] ='power-driven vessel towing astern (regional use)';
			} elseif($message['navigationalStatus'] == 12) {
			    $message['navigationalStatus'] ='power-driven vessel pushing ahead or towing alongside (regional use)';
			} elseif($message['navigationalStatus'] == 13) {
			    $message['navigationalStatus'] ='reserved for future use';
			} elseif($message['navigationalStatus'] == 14) {
			    $message['navigationalStatus'] ='AIS-SART (active), MOB-AIS, EPIRB-AIS';
			} elseif($message['navigationalStatus'] == 15) {
			    $message['navigationalStatus'] ='undefined = default (also used by AIS-SART, MOB-AIS and EPIRB-AIS under test)';
			}



			if (substr($binaryPayload,42,1) == 0) {
			    $message['rateOfTurn'] = bindec(substr($binaryPayload,43,7));
			} elseif(substr($binaryPayload,42,1) == 1){
			    $value='';
			    for ($i=1;$i < 8;$i++) {
				if (substr($binaryPayload,42+$i,1) == 1) {
				    $value = $value.'0';
				} else {
				    $value = $value.'1';
				}
			    }
			    $message['rateOfTurn'] = -(bindec($value)+1);
			}
			if($message['rateOfTurn'] == 0) {
			    $message['rateOfTurn'] = 'not turning';
			} elseif ($message['rateOfTurn'] > 0 && $message['rateOfTurn'] < 127) {
			    $message['rateOfTurn'] = ($message['rateOfTurn']/4.733)*($message['rateOfTurn']/4.733)."  turning right at up to 708 deg per min or higher";
			} elseif ($message['rateOfTurn'] < 0 && $message['rateOfTurn'] > -127) {
			    $message['rateOfTurn'] = ($message['rateOfTurn']/4.733)*($message['rateOfTurn']/4.733)."  turning left at up to 708 deg per min or higher";
			} elseif ($message['rateOfTurn'] == 127) {
			    $message['rateOfTurn'] = 'turning right at more than 5 deg per 30 s (No TI available)';
			} elseif($message['rateOfTurn'] == -127) {
			    $message['rateOfTurn'] = 'turning left at more than 5 deg per 30 s (No TI available)';
			} elseif ($message['rateOfTurn'] == -128) {
			    $message['rateOfTurn'] = 'indicates no turn information available (default)';
			}
			$message['speedOverGround'] = bindec(substr($binaryPayload,50,10))/10;
			$message['positionAccuracy'] = substr($binaryPayload,60,1);
			if ($message['positionAccuracy'] == 1) {
			    $message['positionAccuracy'] = 'DGPS-quality fix with an accuracy of < 10ms';
			} else {
			    $message['positionAccuracy'] = 'an unaugmented GNSS fix with accuracy > 10m';
			}
			if (substr($binaryPayload,61,1) == 0) {
			    $message['longitude'] = bindec(substr($binaryPayload, 62, 27))/600000;
			} else {
			    $value = '';
			    for ($i = 0 ; $i < 27; $i++) {
				if (substr($binaryPayload,62+$i,1) == 1) {
				    $value = $value.'0';
				} else {
				    $value = $value.'1';
				}
			    }
			    $message['longitude'] = -(bindec($value)+1)/600000;
			    if ($message['longitude'] == 181) {
				$message['longitude'] = 'not available';
			    }
			}
			if (substr($binaryPayload,89,1) == 0) {
			    $message['latitude'] = bindec(substr($binaryPayload, 90, 26))/600000;
			} else {
			    $value = '';
			    for ($i = 0 ; $i < 26; $i++) {
				if (substr($binaryPayload,90+$i,1) == 1) {
				    $value = $value.'0';
				} else {
				    $value = $value.'1';
				}
			    }
			    $message['latitude'] = -(bindec($value)+1)/600000;
			}
			if($message['latitude'] == 91) {
			    $message['latitude'] = 'not available';
			}
			$message['courseOverGround'] = bindec(substr($binaryPayload,116,12))/10;
			if ($message['courseOverGround'] == 360) {
			    $message['courseOverGround'] = 'not available';
			}
			$message['trueHeading'] = bindec(substr($binaryPayload,128,9));
			if ($message['trueHeading'] == 511) {
			    $message['trueHeading'] = 'not available';
			}
			$message['timeStamp'] = bindec(substr($binaryPayload,137,6));
			if ($message['timeStamp'] == 60) {
			    $message['timeStamp'] = 'not available';
			} else if ($message['timeStamp'] == 61) {
			    $message['timeStamp'] = 'positioning system is in manual input mode';
			} else if ($message['timeStamp'] == 62) {
			    $message['timeStamp'] = 'electronic position fixing system operates in estimated (dead reckoning) mode';
			} else if ($message['timeStamp'] == 63) {
			    $message['timeStamp'] = 'positioning system is inoperative';
			}
			$message['maneuverIndicator'] = bindec(substr($binaryPayload,143,2));
			if ($message['maneuverIndicator'] == 0) {
			    $message['maneuverIndicator'] = 'not available';
			} elseif ($message['maneuverIndicator'] == 1) {
			    $message['maneuverIndicator'] = 'No special maneuver';
			} elseif ($message['maneuverIndicator'] == 2) {
			    $message['maneuverIndicator'] = 'Special maneuver';
			}
			$message['raimFlag'] = substr($binaryPayload,148,1);
		} elseif ($message['type']==4) {
			$message['year'] = bindec(substr($binaryPayload, 38, 14));
			$message['month'] = bindec(substr($binaryPayload, 52, 4));
			$message['day'] = bindec(substr($binaryPayload, 56, 5));
			$message['hour'] = bindec(substr($binaryPayload, 61, 5));
			$message['minute'] = bindec(substr($binaryPayload, 66, 6)); 
			$message['second'] = bindec(substr($binaryPayload, 72, 6));
		} elseif ($message['type'] == 18) {
			$message['repeatIndicator'] = bindec(substr($binaryPayload, 6, 2));
			$message['mmsi'] = bindec(substr($binaryPayload, 8, 30));
			$message['speedOverGround'] = bindec(substr($binaryPayload, 46, 10))/10;
			$message['positionAccuracy'] = bindec(substr($binaryPayload, 56, 1));
			if ($message['positionAccuracy'] == 1) {
			    $message['positionAccuracy'] = 'DGPS-quality fix with an accuracy of < 10ms';
			} else {
			    $message['positionAccuracy'] = 'an unaugmented GNSS fix with accuracy > 10m';
			}
			if (substr($binaryPayload,57,1) == 0) {
			    $message['longitude'] = bindec(substr($binaryPayload, 58, 27))/600000;
			} else {
			    $value = '';
			    for ($i = 0 ; $i < 27; $i++) {
				if (substr($binaryPayload,58+$i,1) == 1) {
				    $value = $value.'0';
				} else {
				    $value = $value.'1';
				}
			    }
			    $message['longitude'] = -(bindec($value)+1)/600000;
			    if ($message['longitude'] == 181) {
				$message['longitude'] = 'not available';
			    }
			}
			if (substr($binaryPayload,85,1) == 0) {
			    $message['latitude'] = bindec(substr($binaryPayload, 86, 26))/600000;
			} else {
			    $value = '';
			    for ($i = 0 ; $i < 26; $i++) {
				if (substr($binaryPayload,86+$i,1) == 1) {
				    $value = $value.'0';
				} else {
				    $value = $value.'1';
				}
			    }
			    $message['latitude'] = -(bindec($value)+1)/600000;
			}
			if($message['latitude'] == 91) {
			    $message['latitude'] = 'not available';
			}
			$message['courseOverGround'] = bindec(substr($binaryPayload,112,12))/10;
			if ($message['courseOverGround'] == 360) {
			    $message['courseOverGround'] = 'not available';
			}
			$message['trueHeading'] = bindec(substr($binaryPayload,124,9));
			if ($message['trueHeading'] == 511) {
			    $message['trueHeading'] = 'not available';
			}

			$message['timeStamp'] = bindec(substr($binaryPayload,133
				,6));
			if ($message['timeStamp'] == 60) {
			    $message['timeStamp'] = 'not available';
			} else if ($message['timeStamp'] == 61) {
			    $message['timeStamp'] = 'positioning system is in manual input mode';
			} else if ($message['timeStamp'] == 62) {
			    $message['timeStamp'] = 'electronic position fixing system operates in estimated (dead reckoning) mode';
			} else if ($message['timeStamp'] == 63) {
			    $message['timeStamp'] = 'positioning system is inoperative';
			}



		} elseif ($message['type'] == 24 && $message['part'] == 1) {
			$asc_6bit = array(0 => '@', 1 => 'A', 2 => 'B', 3 => 'C',
					4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G',
					8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K',
					12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O',
					16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S',
					20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W',
					24 => 'X', 25 => 'Y', 26 => 'Z', 27 => '[',
					28 => "'\'", 29 => ']', 30 => "\^", 31 => "\_",
					32 => ' ', 33 => '!', 34 => '"', 35 => '\#',
					36 => '$', 37 => '%', 38 => '&', 39 => "\'",
					40 => '(', 41 => ')', 42 => "\*", 43 => "\+",
					44 => ",", 45 => "-", 46 => ".", 47 => "/",
					48 => '0', 49 => '1', 50 => '2', 51 => '3',
					52 => '4', 53 => '5', 54 => '6', 55 => '7',
					56 => '8', 57 => '9', 58 => ':', 59 => ';',
					60 => '<', 61 => '=', 62 => '>', 63 => '?'   
			);
			$shipname = '';
			$vendorid = '';
			$callsign = '';
			$message['repeatIndicator'] = bindec(substr($binaryPayload, 6, 2));
			$message['mmsi'] = bindec(substr($binaryPayload, 8, 30));
			$message['part'] = bindec(substr($binaryPayload, 38, 2));
			if ($message['part'] == 0) {
				$message['shipname'] = substr($binaryPayload, 40, 120);
				$i = 0;
				while ($i < 119) {
					$shipname = $shipname.$asc_6bit[bindec(substr($message['shipname'],$i,6))];
					$i+=6;
				}
			$message['shipname'] = $shipname;
			} elseif ($message['part'] == 1) {
				$message['shiptype'] = bindec(substr($binaryPayload, 40, 8));
				$message['shiptype'] = self::SHIPTYPE[$message['shiptype']];
				$message['vendorid'] = substr($binaryPayload, 48, 18);
				$i = 0;
				while ($i < 17) {
					$vendorid = $vendorid.$asc_6bit[bindec(substr($message['vendorid'],$i,6))];
					$i+=6;
				}
				$message['vendorid'] = $vendorid;
				$message['unitModelCode'] = bindec(substr($binaryPayload, 66, 4));
				$message['serialNumber'] = bindec(substr($binaryPayload, 70, 20));
				$message['callsign'] = substr($binaryPayload, 90, 42);
				$i = 0;
				while ($i < 41) {
					$callsign = $callsign.$asc_6bit[bindec(substr($message['callsign'],$i,6))];
					$i+=6;
				}
				$message['callsign'] = $callsign;
				$message['to_bow'] = bindec(substr($binaryPayload, 132, 9));
				$message['to_stern'] = bindec(substr($binaryPayload, 141, 9));
				$message['to_port'] = bindec(substr($binaryPayload, 150, 6));
				$message['to_starboard'] = bindec(substr($binaryPayload, 156, 6));
				$message['mothershipmmsi'] = bindec(substr($binaryPayload, 132, 30));

			}
		}
		return $message;
		} else {
		return "bad checksum";
		}

	}

	public function decodeType_5($part_1, $part_2){

		$asc_6bit = array(0 => '@', 1 => 'A', 2 => 'B', 3 => 'C',
					4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G',
					8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K',
					12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O',
					16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S',
					20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W',
					24 => 'X', 25 => 'Y', 26 => 'Z', 27 => '[',
					28 => "'\'", 29 => ']', 30 => "\^", 31 => "\_",
					32 => ' ', 33 => '!', 34 => '"', 35 => '\#',
					36 => '$', 37 => '%', 38 => '&', 39 => "\'",
					40 => '(', 41 => ')', 42 => "\*", 43 => "\+",
					44 => ",", 45 => "-", 46 => ".", 47 => "/",
					48 => '0', 49 => '1', 50 => '2', 51 => '3',
					52 => '4', 53 => '5', 54 => '6', 55 => '7',
					56 => '8', 57 => '9', 58 => ':', 59 => ';',
					60 => '<', 61 => '=', 62 => '>', 63 => '?'   
			);

		$payload = $part_1.$part_2;
		$message = array();
		$message['repeatIndicator'] = bindec(substr($payload,6,2));
		$message['mmsi'] = bindec(substr($payload,8,30));
		$message['ais_version'] = bindec(substr($payload, 38, 2));
		$message['imo'] = bindec(substr($payload, 40, 30));
		$message['callsign'] = substr($payload, 70, 42);
		$callsign = '';
		$shipname = '';
		$destination = '';
		$i = 0;
		while ($i < 41) {
			$callsign = $callsign.$asc_6bit[bindec(substr($message['callsign'],$i,6))];
			$i+=6;
		}
		$message['callsign'] = $callsign;

		$message['shipname'] = substr($payload, 112, 120);
		$i = 0;
		while ($i < 119) {
			$shipname = $shipname.$asc_6bit[bindec(substr($message['shipname'],$i,6))];
			$i+=6;
		}
		$message['shipname'] = $shipname;


		$message['shiptype'] = bindec(substr($payload, 232, 8));
		$message['shiptype'] = self::SHIPTYPE[$message['shiptype']];
		$message['to_bow'] = bindec(substr($payload, 240, 9));
		$message['to_stern'] = bindec(substr($payload, 249, 9));
		$message['to_port'] = bindec(substr($payload, 258, 6));
		$message['to_starboard'] = bindec(substr($payload, 264, 6));
		$message['epfd'] = bindec(substr($payload, 270, 4));
		$message['month'] = bindec(substr($payload, 274, 4));
		$message['day'] = bindec(substr($payload, 278, 5));
		$message['hour'] = bindec(substr($payload, 283, 5));
		$message['minute'] = bindec(substr($payload, 288, 6));
		$message['draught'] = bindec(substr($payload, 294, 8))/10;
		$message['destination'] = substr($payload, 302, 120);
		$i = 0;
		while ($i < 119) {
			$destination = $destination.$asc_6bit[bindec(substr($message['destination'],$i,6))];
			$i+=6;
		}
		$message['destination'] = $destination;
		return $message;
	}
}
