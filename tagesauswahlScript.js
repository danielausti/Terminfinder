function updateSlider(target){

    let dayBox = getDateBox(target);
    let dataBox = getSliderBox(dayBox);
    let day = dayBox.getAttribute("day");
    let form = document.getElementById("form-"+day);
    let eventDates = dayBox.getAttribute("defaultvalues").split(",").map(Number);
    let eventDatesBorders = dayBox.getAttribute("defaultvalues").split(",").map(Number);
    var beg = null;
    var end = null;
    var sicherheit = null;
    for(let elem of dataBox.children) {
        let rangeItem = elem.children[0];
        if(rangeItem != null){
            if (beg == null){
                beg = parseInt(rangeItem.value);
                sicherheit = getSicherheit(rangeItem);
            } else {
                end = parseInt(rangeItem.value);
                if(sicherheit==getSicherheit(rangeItem)){
                    if(beg < end){
                        for (let i = beg; i < end; i++) {
                            if (eventDates[i] < sicherheit){
                                if (eventDates[i] != -1)
                                    eventDates[i] = sicherheit;
                                eventDatesBorders[i] = sicherheit;
                            }
                        }
                    } else {
                        for (let i = end; i < beg; i++) {
                            if (eventDates[i] < sicherheit ){
                                if( eventDates[i] != -1)
                                    eventDates[i] = sicherheit;
                                eventDatesBorders[i] = sicherheit;
                            }
                        }
                    }
                }
                beg = null;
                end = null;
            }
        }
    };
    dayBox.setAttribute("values",eventDates.join());
    form.value = eventDates.join();
    let tableTds = getTableData(dayBox).children;
    for(let i = 0; i < tableTds.length; i++){
        tableTds[i].className = "sel" + eventDates[i];
        if (eventDatesBorders[i] > 0){
            tableTds[i].classList.add("sel" + eventDatesBorders[i] + "border");
        }
    }
}


function getDateBox(element) {
    do{
        element = element.parentElement;
    } while(!element.classList.contains("day"));
    return element;
}
function getTableData(dateObject){
    return dateObject.children[1].children[0].children[0].children[0].children[0];
}
function getSliderBox(dateObject) {
    return dateObject.children[1].children[1];
}

function getSicherheit(element){
    let classList = element.classList;
    if(classList.contains("sicher1")){
        return 1;
    } else if (classList.contains("sicher2")) {
        return 2;
    } 
    else if (classList.contains("sicher2")) {
        return 3;
    }
    else{
        return 3;
    }
}

function ichKann(event){
    let dateBox = getDateBox(event.target);
    let day = dateBox.getAttribute("day");
    let zeitraeume = JSON.parse(event.target.getAttribute("zeitraum"));
    let sliderBox = getSliderBox(getDateBox(event.target));
    let sliderTextTable = document.getElementById("sliderTextTable-" + day).firstElementChild;
    let createMode = Boolean(parseInt(dateBox.getAttribute("create")));
    console.log(createMode);
    var sliderString = "";
    var sliderTextString = "";
    var i = 0;
    zeitraeume.forEach(element => {
        let start = element.start;
        let end = element.end;
        sliderString += createSliderPair(start, end, 3, day+"-"+i);
        sliderTextString += createSliderText(start, end, 3, day + "-" + i, i+1, createMode);
        i++;
    });
    sliderBox.innerHTML = sliderString;
    sliderTextTable.innerHTML = sliderTextString;
    updateSlider(event.target);
}
function ichKannNicht(event) {
    let dateBox = getDateBox(event.target);
    let day = dateBox.getAttribute("day");
    let sliderBox = getSliderBox(dateBox);
    let sliderTextTable = document.getElementById("sliderTextTable-" + day).firstElementChild;
    sliderBox.innerHTML = "";
    sliderTextTable.innerHTML = "";
    updateSlider(event.target);
}
function einschraenkung(event) {
    let dateBox = getDateBox(event.target);
    let sicherheit = parseInt(event.target.getAttribute("sicherheit"));
    let day = dateBox.getAttribute("day");
    let createMode = Boolean(parseInt(dateBox.getAttribute("create")));
    console.log(createMode);
    let sliderBox = getSliderBox(dateBox);
    let sliderTextTable = document.getElementById("sliderTextTable-" + day).firstElementChild;
    let zeitraeume = dateBox.getAttribute("values").split(",").map(Number);
    var first0 = -1;
    for(var i = 0; i < 24; i++){
        if(zeitraeume[i] == 0){
            first0 = i;
            break;
        }
    }
    if(first0 != -1){
        let sliderPairNum = sliderBox.children.length / 2 + 1;
        var sliderString = createSliderPair(first0, first0 + 1, sicherheit, day + "-" + sliderPairNum);
        let slidernumber = sliderTextTable.children.length;
        var sliderTextString = createSliderText(first0, first0 + 1, sicherheit,
             day + "-" + sliderPairNum, slidernumber + 1, createMode);
        sliderBox.innerHTML += sliderString;
        sliderTextTable.innerHTML += sliderTextString;
        updateSlider(event.target);
    } else {
        event.target.classList.add("slidercantcreate");
        setTimeout(function(){
            event.target.classList.remove("slidercantcreate");
        },500)
    }
}

function sliderInput(event) {
    let target = event.target;

    target.setAttribute("value", target.value);
    try {
        let sliderText = document.getElementById(target.id.replace("slider", "sliderText"));
        sliderText.value = target.value;
        sliderText.setAttribute("value", target.value)
    } catch (error) {}
    updateSlider(target);
}

function sliderTextChange(event){
    let target = event.target;
    target.setAttribute("value", target.value);
    let slider = document.getElementById(target.id.replace("sliderText","slider"));
    slider.value = event.target.value;
    slider.setAttribute("value", target.value);
    updateSlider(target);
}

function sliderSicherheitChange(event){
    var slider;
    let target = event.target;
    let children = target.children;
    target.className = "sel" + target.value;
    target.setAttribute("value",target.value);
    for(let i = 0; i < children.length; i++){
        if (children[i].value == target.value){
            children[i].setAttribute("selected", "");
        } else {
            children[i].removeAttribute("selected");
        }        
    }
    for(let i = 1 ; i <= 2; i++){
        slider = document.getElementById(target.id.replace("sliderSicherheit", "slider"+i));
        
        slider.classList.remove("sicher1");
        slider.classList.remove("sicher2");
        slider.classList.remove("sicher3");
        slider.classList.add("sicher" + target.value);
    }
    updateSlider(event.target);
}

function sliderLoeschen(event){
    let target = event.target;
    var slider;
    for (let i = 1; i <= 2; i++) {
        slider = document.getElementById(target.id.replace("sliderDelete", "slider" + i));
        slider.remove();
    }
    updateSlider(target);

    let row = document.getElementById(target.id.replace("sliderDelete", "sliderText"));
    let rowParent = row.parentElement;
    row.remove();

    let rows = rowParent.children;
    for(let i = 0 ; i < rows.length; i++){
        let heading = rows[i].children[0].children[0].children[0];
        heading.innerHTML = "Einschränkung "+(i+1)+":";
    }

     
}

function createSliderPair(value1, value2, sicherheit, sliderid) {
    let klasse = "sicher"+sicherheit;
    return `<div class="sliderBox">
                <input type = "range" min = "0" max = "24" value = "${value1}" class="slider ${klasse}"
                id = "slider1-${sliderid}" autocomplete = "off" oninput = "sliderInput(event)" >
            </div >
            <div class="sliderBox">
                <input type="range" min="0" max="24" value="${value2}" class="slider ${klasse}"
                id="slider2-${sliderid}" autocomplete="off" oninput="sliderInput(event)">
            </div>
            `;
}

function createSliderText(value1, value2, sicherheit, sliderid, nummer, createMode) {
    let selected1 = (sicherheit == 1) ? "selected" : "";
    let selected2 = (sicherheit == 2) ? "selected" : "";
    let selected3 = (sicherheit == 3) ? "selected" : "";
    let text = `<tr id="sliderText-${sliderid}"><td>
                <span><b>Einschränkung ${nummer}: </b></span>
                <span>start: <input type="number" min="0" max="24" value="${value1}" autocomplete="off"
                id="sliderText1-${sliderid}" onChange="sliderTextChange(event)"></span>
                <span>ende: <input type="number" min="0" max="24" value="${value2}" autocomplete="off"
                id="sliderText2-${sliderid}" onChange="sliderTextChange(event)"></span> 
                `;
    if(!createMode){
        text += `<span>sicherheit: 
                <select id="sliderSicherheit-${sliderid}" class="sel${sicherheit}" 
                 autocomplete="off" oninput="sliderSicherheitChange(event)">
                    <option class="sel3" value="3" ${selected3}>sicher</option>
                    <option class="sel2" value="2" ${selected2}>wahrscheinlich</option>
                    <option class="sel1" value="1" ${selected1}>unsicher</option>
                </select></span>
                `;
    }
    text +=     `<span><button type="button" id="sliderDelete-${sliderid}" onClick="sliderLoeschen(event)">Löschen</button>
                </span>
            </td></tr>`;
    return text;
}