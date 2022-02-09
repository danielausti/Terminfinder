<?php 
$monthNames = ["Jan","Feb","Mär","Apr","Mai","Jun","Jul","Aug","Sep","Okt","Nov","Dez"];

$anzahlGezeigterMonate = 12;
if(isset($_GET["anzahlMonate"])){
    $anzahlGezeigterMonate = intval($_GET["anzahlMonate"]);
}

$today = date_create("NOW");
$todayString = date_format($today,"Y-m-d");
$todayWeekday = intval(date_format($today,"w"));

$lastMonthDay = intval(date_format($today,"t"));
$lastMonth = date_modify(date_create(date_format($today,"Y-m-01")),"+$anzahlGezeigterMonate months");
$lastDayInMonth = date_create(date_format($lastMonth,"Y-m-t"));
$lastDay = date_format(date_modify($lastDayInMonth,"+".(7-intval(date_format($lastDayInMonth,"w")))." days"),"Y-m-d");

?>


<div id="tableDiv">
    <div id="tableHeader">
        <table>
            <tbody>
                <tr>
                    <th><p>M&#8203;on&#8203;tag</p></th>
                    <th><p>D&#8203;ie&#8203;nstag</p></th>
                    <th><p>M&#8203;it&#8203;twoch</p></th>
                    <th><p>D&#8203;on&#8203;nerstag</p></th>
                    <th><p>F&#8203;re&#8203;itag</p></th>
                    <th><p>S&#8203;am&#8203;stag</p></th>
                    <th><p>S&#8203;on&#8203;ntag</p></th>
                </tr>
            </tbody>
        </table>
    </div>
    <div id="tableDates">
        <table id="table">
            <tbody>
                <?php 
                $weekdayShift = ($todayWeekday-1 == -1) ? 6 : $todayWeekday-1;
                $date = date_modify($today, "-".$weekdayShift." days");
                $startDate = date_format($date,"Y-m-d");

                for($k=0; $k<200; $k++){
                    echo "<tr>";
                    for($i=0; $i<7; $i++){
                        $dateString = date_format($date,"Y-m-d");
                        $day = intval(date_format($today,"d"));
                        $month = intval(date_format($today,"m"));
                        $year = intval(date_format($today,"Y"));

                        $klasseTd = "";
                        $klasseDiv = "";
                        $selectable = "true";
                        
                        if(intval($day) == 1 || $dateString == $startDate){
                            $day = $day.". ".$monthNames[$month-1];
                        }
                        if($dateString == $todayString){
                            $klasseDiv .= "today ";
                        }
                        if(intval(date_format($date, "m")) % 2 == 0){
                            $klasseDiv .= "darkerMonth ";
                        }
                        if($dateString < $todayString){
                            $klasseTd .= "notavailable ";
                            $selectable = "false";
                        } else {
                            $klasseTd .= "pointer ";
                        } ?>

                        <td class="tabledata <?php echo $klasseTd; ?>" day="<?php echo $dateString; ?>"
                            selectable="<?php echo $selectable; ?>" onmousedown='select(this)' onmouseenter='enter(this)'>
                            <div class="<?php echo $klasseDiv; ?>">
                                <h2><?php echo $day; ?></h2>
                            </div>
                        </td>
                        
                    <?php 
                    $date = date_modify($date,"+1 day");
                    }  
                    echo "</tr>";
                    if($dateString >= $lastDay){
                        break;
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
    <input type="hidden" name="calendar" id="calendarvalues">
</div>