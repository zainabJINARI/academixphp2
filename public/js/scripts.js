var modal = document.getElementById("myModal");
var deleteBtn = document.getElementById("deleteBtn");
var confirmBtn = document.getElementById("confirmBtn");
var cancelBtn = document.getElementById("cancelBtn");
var closeBtn = document.getElementsByClassName("close")[0];

deleteBtn.addEventListener('click',(e)=>{
e.preventDefault()
console.log('hhh')
modal.style.display = "block";
})
deleteBtn.onclick = function() {
    
   
}

cancelBtn.onclick = function() {
    modal.style.display = "none";
}

closeBtn.onclick = function() {
    modal.style.display = "none";
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

confirmBtn.onclick = function() {
    alert("Votre compte a été supprimé avec succès.");
    modal.style.display = "none";
}