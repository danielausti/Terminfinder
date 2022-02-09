<?php

function bloeckeZusammen($zeitbloecke, $auswahlbloecke){
    $bloeckeZusammen = array();
    foreach($zeitbloecke as $day => $dayBlocks){
        if(array_key_exists($day, $auswahlbloecke)){
            $newDayBlock = array();
            foreach($dayBlocks as $hour => $wert){
                if($wert == -1){
                    $newDayBlock[$hour] = $wert;
                } else {
                    $newDayBlock[$hour] = $auswahlbloecke[$day][$hour];
                } 
            }
            $bloeckeZusammen[$day] = $newDayBlock;
        } else {
            $bloeckeZusammen[$day] = $dayBlocks;
        }
    }
    return $bloeckeZusammen;
}

function zeitraumZuZeitblock($zeitraeume){
    $zeitbloecke = array();
    foreach($zeitraeume as $zeitraum){
        $start = date_create($zeitraum["startpunkt"]);
        $startDay = date_format($start,"Y-m-d");
        $end = date_create($zeitraum["endpunkt"]);
        $endDay = date_format($end,"Y-m-d");

        $sicherheit = (isset($zeitraum["sicherheit"])) ? $zeitraum["sicherheit"] : 0;

        for($d = date_create($startDay); $d <= date_create($endDay); date_modify($d,'+1 day')){
            $day = date_format($d,"Y-m-d");
            if(!array_key_exists($day,$zeitbloecke) && 
            !(intval(date_format($end,"H"))==0 && $day == date_format($end,"Y-m-d"))){
                $fill = ($sicherheit == 0) ? -1 : 0;
                $zeitbloecke += array($day => array_fill(0,24,$fill));
            }            
            $dayStart = ($startDay == $day) ? intval(date_format($start,"H")) : 0;
            $dayEnd = ($endDay == $day) ? intval(date_format($end,"H")) : 24;

            for($i = $dayStart; $i < $dayEnd; $i++){
                if($zeitbloecke[$day][$i] < $sicherheit)
                    $zeitbloecke[$day][$i] = $sicherheit;
            }
        }
    }
    ksort($zeitbloecke);
    return $zeitbloecke;
}

function auswahlZuZeitraumGeteilt($zeitbloecke, $terminlaenge){
    ksort($zeitbloecke);
    $zeitraeume = array();

    $sicherheitDavor = -1;
    $startpunkt = "";
    $endpunkt = "";
    $bloeckeZusammen = array();

    foreach($zeitbloecke as $day => $dayBlocks){
        foreach($dayBlocks as $hour => $sicherheit){
            $bloeckeZusammen["$day $hour:00:00"] = $sicherheit;
        }
    }
    $bzLength = count($bloeckeZusammen);
    for($i = 0; $i < $bzLength - ($terminlaenge-1); $i++){
        $abschnitt = array_slice($bloeckeZusammen,$i,$terminlaenge);
        $niedrigsteSicherheit = 3;
        foreach($abschnitt as $abschSicherheit){
            if($abschSicherheit<$niedrigsteSicherheit){$niedrigsteSicherheit = $abschSicherheit;}
        }
        if($niedrigsteSicherheit > 0){
            $zeitraum = array(
                "startpunkt"=>array_key_first($abschnitt),
                "endpunkt"=>date_format(date_modify(date_create(array_key_last($abschnitt)),"+1 hours"),"Y-m-d H:i:s"),
                "sicherheit"=>$niedrigsteSicherheit
            );
            $zeitraeume[] = $zeitraum;
        }
    }
    return $zeitraeume;
}

function auswahlZuZeitraum($zeitbloecke){
    ksort($zeitbloecke);
    $zeitbloecke[date_format(date_modify(date_create(array_key_last($zeitbloecke)),"+1 day"),"Y-m-d")] = array(-1);
    $zeitraeume = array();

    $sicherheitDavor = -1;
    $startpunkt = "";
    $endpunkt = "";

    foreach($zeitbloecke as $day => $dayBlocks){
        foreach($dayBlocks as $hour => $sicherheit){
            if(!($sicherheitDavor == $sicherheit)){
                $endpunkt = "$day $hour:00:00";

                if($sicherheitDavor != -1 && $sicherheitDavor != 0){
                    $zeitraum = array(
                        "startpunkt"=>$startpunkt,
                        "endpunkt"=>$endpunkt,
                        "sicherheit"=>$sicherheitDavor
                    );
                    $zeitraeume[] = $zeitraum;
                }
                $startpunkt = "$day $hour:00:00";
            }
            $sicherheitDavor = $sicherheit;
        }
    }
    return $zeitraeume;
}

function zeitblockZuZeitraum($zeitbloecke){
    ksort($zeitbloecke);
    $zeitbloecke[date_format(date_modify(date_create(array_key_last($zeitbloecke)),"+1 day"),"Y-m-d")] = array(-1);
    $zeitraeume = array();

    $sicherheitDavor = -1;
    $startpunkt = "";
    $endpunkt = "";

    foreach($zeitbloecke as $day => $dayBlocks){
        foreach($dayBlocks as $hour => $sicherheit){
            if(!($sicherheitDavor == $sicherheit)){
                $endpunkt = "$day $hour:00:00";

                if($sicherheitDavor != -1){
                    $zeitraum = array(
                        "startpunkt"=>$startpunkt,
                        "endpunkt"=>$endpunkt
                    );
                    $zeitraeume[] = $zeitraum;
                }
                $startpunkt = "$day $hour:00:00";
            }
            $sicherheitDavor = $sicherheit;
        }
    }
    return $zeitraeume;
}

function convertCreated($value){
    if($value == 0 || $value == -1){
        return -1;
    } else {
        return 0;
    }
}

?>