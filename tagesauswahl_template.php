<?php 
$zeitraeumeEvent = zeitblockZuZeitraum([$day => $eventDates],true);
$zeitraeumeEventData = array();
foreach($zeitraeumeEvent as $e){
    $start = intval(date_format(date_create($e["startpunkt"]),"H"));
    $end = intval(date_format(date_create($e["endpunkt"]),"H"));
    if($end == 0 && date_format(date_create($e["endpunkt"]),"Y-m-d") != $day ) {
        $end = 24;
    }
    $zeitraeumeEventData[] = array("start" => $start, "end" => $end);
}
if($personDates == null || count($personDates) == 0){
    $personDates = array_fill(0,24,0);
}
$zeitraeumePerson = auswahlZuZeitraum([$day => $personDates],true);
$wochentage = array("So", "Mo", "Di", "Mi", "Do", "Fr", "Sa");
date_default_timezone_set("Europe/Berlin");
$wochentag = $wochentage[date_format(date_create($day),"w")];

if(!isset($createMode)){
    $createMode = false;
}
$selectAllText = ($createMode) ? "Alles auswählen" : "Ich kann";
$selectNothingText = ($createMode) ? "Auswahl löschen" : "Ich kann nicht";
$addFirstSliderText = ($createMode) ? "+ Slider hinzufügen" : "+ sicher";

if($day == "2000-01-01"){
    $titel = "Montag - Freitag";
    $formName = "weekday";
} else if($day == "2000-01-02"){
    $titel = "Samstag - Sonntag";
    $formName = "weekend";
} else {
    $titel = $wochentag." ".date_format(date_create($day),"d.m.Y");
    $formName = $day;
}

?>

<div class="day" day="<?php echo $day ?>" id="day-<?php echo $day ?>" create="<?php echo intval($createMode); ?>" 
 defaultValues="<?php echo implode(",",$eventDates) ?>" values="<?php echo implode(",",$personDates) ?>">
    <div class="dayHeader">
        <h2><?php echo $titel ?></h2>
        <span class="buttonsSpan">
            <button type="button" onclick="ichKann(event)" class="sel3" 
             zeitraum='<?php echo json_encode($zeitraeumeEventData) ?>'><?php echo $selectAllText; ?></button>
            <button type="button" onclick="ichKannNicht(event)" class="sel0"><?php echo $selectNothingText; ?></button>
        </span>
        <span class="buttonsSpan">
            <button type="button" onclick="einschraenkung(event)" sicherheit="3" 
             class="sel3"><?php echo $addFirstSliderText; ?></button>
            <?php if(!$createMode){ ?>
            <button type="button" onclick="einschraenkung(event)" sicherheit="2" class="sel2"> + wahrscheinlich</button>
            <button type="button" onclick="einschraenkung(event)" sicherheit="1" class="sel1"> + unsicher</button>
            <?php } ?>
        </span>
    </div>
    <div class="dayChart">
        <div class="dayChartTable">
            <table>
                <tbody>
                    <tr>
                        <?php
                            foreach($personDates as $hour => $wert){
                                $klasse = "sel".$wert;
                                $klasseborder = "sel".$wert."border";
                                echo "<td class='$klasse $klasseborder'>$hour:&#8203;00</td>";
                            }
                        ?>
                    </tr>
                </tbody>
            </table>
        </div> 
        <div class="dayChartData">
            
            <?php 
            foreach($zeitraeumePerson as $num => $zeitraum){
                $start = intval(date_format(date_create($zeitraum["startpunkt"]),"H"));
                $end = intval(date_format(date_create($zeitraum["endpunkt"]),"H"));
                if($end == 0 && date_format(date_create($zeitraum["endpunkt"]),"Y-m-d") != $day ) {
                    $end = 24;
                }
                $klasse = "sicher".$zeitraum["sicherheit"];
                ?>
                <div class="sliderBox">
                    <input type="range" min="0" max="24" value="<?php echo $start; ?>" class="slider <?php echo $klasse; ?>" 
                        id="slider1-<?php echo $day."-".$num; ?>" autocomplete="off" oninput="sliderInput(event)">
                </div>
                <div class="sliderBox">
                    <input type="range" min="0" max="24" value="<?php echo $end; ?>" class="slider <?php echo $klasse; ?>" 
                        id="slider2-<?php echo $day."-".$num; ?>" autocomplete="off" oninput="sliderInput(event)">
                </div>
            <?php } ?>
        </div>
        
    </div>
    <div class="sliderValues">
        <h3>Slider:</h3>
        <table id="sliderTextTable-<?php echo $day; ?>">  
            <tbody>
                <?php foreach($zeitraeumePerson as $num => $zeitraum){
                    $start = intval(date_format(date_create($zeitraum["startpunkt"]),"H"));
                    $end = intval(date_format(date_create($zeitraum["endpunkt"]),"H"));
                    if($end == 0 && date_format(date_create($zeitraum["endpunkt"]),"Y-m-d") != $day ) {
                        $end = 24;
                    } ?>
                <tr id="sliderText-<?php echo $day."-".$num; ?>"><td>
                    <span><b>Einschränkung <?php echo $num+1; ?>: </b></span>
                    <span>start: <input type="number" min="0" max="24" value="<?php echo $start; ?>" autocomplete="off"
                    id="sliderText1-<?php echo $day."-".$num; ?>" onChange="sliderTextChange(event)"></span>
                    <span>ende: <input type="number" min="0" max="24" value="<?php echo $end; ?>" autocomplete="off"
                    id="sliderText2-<?php echo $day."-".$num; ?>" onChange="sliderTextChange(event)"></span>
                    <?php if(!$createMode) { ?>
                    <span>sicherheit: 
                    <select id="sliderSicherheit-<?php echo $day."-".$num; ?>" class="sel<?php echo  $zeitraum["sicherheit"]?>"
                    autocomplete="off" oninput="sliderSicherheitChange(event)">
                        <option value="3" class="sel3" <?php if($zeitraum["sicherheit"] == 3) echo "selected"; ?>>sicher</option>
                        <option value="2" class="sel2" <?php if($zeitraum["sicherheit"] == 2) echo "selected"; ?>>wahrscheinlich</option>
                        <option value="1" class="sel1" <?php if($zeitraum["sicherheit"] == 1) echo "selected"; ?>>unsicher</option>
                    </select></span>
                    <?php } ?>
                    <span><button type="button" id="sliderDelete-<?php echo $day."-".$num; ?>" onClick="sliderLoeschen(event)">Löschen</button>
                    </span>
                </td></tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <input name="<?php echo $formName; ?>" id="form-<?php echo $day; ?>" type="hidden"
     value="<?php echo implode(",",$personDates) ?>">
</div>
