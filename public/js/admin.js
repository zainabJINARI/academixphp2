
const containerRequests = document.getElementById('requests')
const updateProfile= document.getElementById('update-profile')
const manageTutos = document.getElementById('manageTutos')
const requestsTutos = document.getElementById('requestsTutos')
const manageTutorsContainer = document.getElementById('manageTutorsContainer')
updateProfile.classList.add('hidden')
manageTutorsContainer.classList.add('hidden')
requestsTutos.classList.toggle('selected')

document.getElementById('updateBtn').addEventListener('click',(e)=>{
    e.preventDefault()
    updateProfile.classList.remove('hidden')
    document.getElementById('updateBtn').classList.add('selected')
    requestsTutos.classList.remove('selected')
    containerRequests.classList.add('hidden')
    requestsTutos.classList.remove('selected')
    manageTutos.classList.remove('selected')
    manageTutorsContainer.classList.add('hidden')


})

requestsTutos.addEventListener('click',(e)=>{
    e.preventDefault()
    updateProfile.classList.add('hidden')
    requestsTutos.classList.add('selected')
    manageTutos.classList.remove('selected')
    document.getElementById('updateBtn').classList.remove('selected')
    console.log('hhhh')
    containerRequests.classList.remove('hidden')
    manageTutorsContainer.classList.add('hidden')

    
})
manageTutos.addEventListener('click',(e)=>{
    e.preventDefault()
    updateProfile.classList.add('hidden')
    containerRequests.classList.add('hidden')
    manageTutorsContainer.classList.remove('hidden')
    manageTutos.classList.add('selected')
    requestsTutos.classList.remove('selected')
    document.getElementById('updateBtn').classList.remove('selected')

})


// handling form modal

const formCreateTutor = document.getElementById('form-create-tutor')
const dashboard = document.querySelector('.dashboard')
formCreateTutor.classList.add('hidden')
const closebtn = document.getElementById('close')
const addNewTutorbtn = document.getElementById('addNewTutor')
addNewTutorbtn.addEventListener('click',()=>{
    console.log('hhhhhh am clicked')
formCreateTutor.classList.remove('hidden')
dashboard.style.opacity='0.5'
})
closebtn.addEventListener('click',()=>{
    formCreateTutor.classList.add('hidden')
    dashboard.style.opacity='1'
})



document.addEventListener('DOMContentLoaded', function() {
    const imageUpload = document.getElementById('image-upload');
    const profilePicture = document.querySelector('.profile-picture img');

    imageUpload.addEventListener('change', function(event) {
        const file = event.target.files[0];
        const reader = new FileReader();

        reader.onload = function(event) {
            profilePicture.src = event.target.result;
        };

        reader.readAsDataURL(file);
    });
});


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