
const containerRequests = document.getElementById('requests')
const updateProfile= document.getElementById('update-profile')
const manageTutos = document.getElementById('manageTutos')
const requestsTutos = document.getElementById('requestsTutos')
const manageTutorsContainer = document.getElementById('manageTutorsContainer')
const btnshowinput = document.getElementById('btnshowinput')
const imageUpload = document.getElementById('image-upload')
const manageCourseContainer = document.getElementById('manageCourseContainer')
const managecourses = document.getElementById('managecourses')
const createNewCourse = document.getElementById('createNewCourse')
const createCourseForm = document.getElementById('createCourseForm')
const closeCourseCreateBtn = document.getElementById('closeCourseCreateBtn')
const acceptBtn = document.querySelector('accept-request');


manageCourseContainer.classList.add('hidden')
createCourseForm.classList.add('hidden')
createNewCourse.addEventListener('click',()=>{
    console.log('create new course ')
    createCourseForm.classList.remove('hidden')
})
closeCourseCreateBtn.addEventListener('click',()=>{
    createCourseForm.classList.add('hidden')
})


imageUpload.classList.add('hidden')
btnshowinput.addEventListener('click',()=>{
    imageUpload.classList.toggle('hidden')
})
managecourses.addEventListener('click',()=>{
    manageCourseContainer.classList.remove('hidden')
    updateProfile.classList.add('hidden')
    managecourses.classList.add('selected')
    requestsTutos.classList.remove('selected')
    manageTutos.classList.remove('selected')
    document.getElementById('updateBtn').classList.remove('selected')
    containerRequests.classList.add('hidden')
    manageTutorsContainer.classList.add('hidden')
})
var modal = document.getElementById("myModal");
updateProfile.classList.add('hidden')
manageTutorsContainer.classList.add('hidden')
requestsTutos.classList.toggle('selected')
modal.classList.add('hidden')
document.getElementById('updateBtn').addEventListener('click',(e)=>{
    e.preventDefault()
    manageCourseContainer.classList.add('hidden')
    updateProfile.classList.remove('hidden')
    document.getElementById('updateBtn').classList.add('selected')
    requestsTutos.classList.remove('selected')
    containerRequests.classList.add('hidden')
    requestsTutos.classList.remove('selected')
    manageTutos.classList.remove('selected')
    manageTutorsContainer.classList.add('hidden')

    managecourses.classList.remove('selected')

})


requestsTutos.addEventListener('click',(e)=>{
    e.preventDefault()
    manageCourseContainer.classList.add('hidden')
    updateProfile.classList.add('hidden')
    requestsTutos.classList.add('selected')
    manageTutos.classList.remove('selected')
    document.getElementById('updateBtn').classList.remove('selected')
    console.log('hhhh')
    containerRequests.classList.remove('hidden')
    manageTutorsContainer.classList.add('hidden')
    managecourses.classList.remove('selected')

    
})
manageTutos.addEventListener('click',(e)=>{
    e.preventDefault()
    manageCourseContainer.classList.add('hidden')
    updateProfile.classList.add('hidden')
    containerRequests.classList.add('hidden')
    manageTutorsContainer.classList.remove('hidden')
    manageTutos.classList.add('selected')
    requestsTutos.classList.remove('selected')
    document.getElementById('updateBtn').classList.remove('selected')
    managecourses.classList.remove('selected')

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



var deleteBtn = document.getElementById("deleteBtn");
var confirmBtn = document.getElementById("confirmBtn");
var cancelBtn = document.getElementById("cancelBtn");
var closeBtn = document.getElementsByClassName("close")[0];

deleteBtn.addEventListener('click',(e)=>{
e.preventDefault()
console.log('hhh')
modal.classList.remove('hidden')
})
deleteBtn.onclick = function() {
    
   
}

cancelBtn.onclick = function() {
    modal.classList.add('hidden')
}

closeBtn.onclick = function() {
    modal.classList.add('hidden')
}

window.onclick = function(event) {
    if (event.target == modal) {
        modal.classList.add('hidden')
    }
}

confirmBtn.onclick = function() {
    alert("Votre compte a été supprimé avec succès.");
    modal.classList.add('hidden')
}





//Afficher details section 
const showDetailsLinks = document.querySelectorAll('.show-request-details');
const requestDetails = document.querySelector('.request-details');
const closeDetails = document.querySelector('.close-details');

showDetailsLinks.forEach(link => {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            requestDetails.style.display = 'block';
        });
});

closeDetails.addEventListener('click', function() {
        requestDetails.style.display = 'none';
});



