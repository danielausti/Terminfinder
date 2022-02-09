var selectedDates = [];
var mousedown = false;

document.addEventListener("mousedown", function(event){
    if (event.button == 0){
        mousedown = true;
    }  
});
document.addEventListener("mouseup", function(event) {
    if (event.button == 0) {
        mousedown = false;
    }
});

function select(element) {
    if (element.getAttribute("selectable") == "true"){
        let date = element.getAttribute("day");
        if (!element.classList.contains("selected")) {
            element.classList.add("selected");
            selectedDates.push(date);
        } else {
            element.classList.remove("selected");
            selectedDates.splice(selectedDates.indexOf(date),1);
        }
        document.getElementById("calendarvalues").value = selectedDates.join();
    }
}  

function enter(element){
    if(mousedown){
        select(element);
    }
}
