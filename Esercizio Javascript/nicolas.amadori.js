var immagini; //Lista delle immagini

window.onload = function load(){
    immagini = document.querySelector(".slider-image").children;//Ottenimento delle immagini contenute all'interno del div delle immagini
    
    for(let i = 0; i < immagini.length; i++){
        immagini[i].addEventListener("click", function() {
            imgClick(i)//Aggiunta del metodo che gestisce l'evento click sulle immagini. Invece di passare event al metodo passo l'indice in modo da poter gestire l'immagine cliccata e le immagini a fianco di essa
        });
    }

    imgClick(0);//All'inizio l'immagine "current" deve essere la prima.
}

function imgClick(index){
    var clickedImg = immagini[index];
    if(!clickedImg.classList.contains("current")) { //Eseguo il codice solo se l'immagine cliccata non è la "current"
        for(let i = 0; i < immagini.length; i++) {
            immagini[i].classList.remove("current");
            immagini[i].style.display = (i >= index - 1 && i <= index + 1) ? "inline-block" : "none"; //Mostro solo l'immagine cliccata e le sue vicine
        }
        clickedImg.classList.add("current");
    }
}