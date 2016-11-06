<?php
namespace AppBundle\Model;

class AisDecoder {


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
		$numberOfFillBits = substr($aisArray[6],0,1);
		$checksum = substr($aisArray[6],2,4);
		
		$crc = 0;
		$offset = 0;
		$mess = substr($aisMessage, 0, -3);
		for($i = 0; $i<strlen($mess); $i++) {
			$char = substr($mess,$i,1);
			$crc ^= $this->ordutf8($char,$offset);
		}

		if (strtoupper(dechex($crc)) == $checksum) {

	

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
		$message['type'] =  bindec(substr($binaryPayload,0,6));
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
			if (substr($binaryPayload,91+$i,1) == 1) {
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
		return $message;
		} else {
		return "bad checksum";
		}

	}
}
