var mycoursesContainer = document.getElementById("mycourses");
var updateprofile = document.getElementById("updateprofile");
var myachievements = document.getElementById("myachievements");
var cancelBtn = document.getElementById("cancelBtn");
var closeBtn = document.getElementById('closepopUp');
const myModal = document.getElementById("myModal");
const btnshowinput = document.getElementById('btnshowinput')
const imageUpload = document.getElementById('image-upload')
imageUpload.classList.add('hidden')
btnshowinput.addEventListener('click',()=>{
    imageUpload.classList.toggle('hidden')
})

const mycoursesBtn = document.getElementById('mycoursesBtn')
const myachievementsBtn = document.getElementById('myachievementsBtn')
const updateprofileBtn = document.getElementById('updateprofileBtn')
const deleteprofileBtn = document.getElementById('deleteprofileBtn')


updateprofile.classList.add('hidden')
myachievements.classList.add('hidden')
myModal.classList.add('hidden')

mycoursesBtn.addEventListener('click', (e) => {
    e.preventDefault()
    mycoursesContainer.classList.remove('hidden')
    updateprofile.classList.add('hidden')
myachievements.classList.add('hidden')
myModal.classList.add('hidden')
})
myachievementsBtn.addEventListener('click', (e) => {
    e.preventDefault()
    myachievements.classList.remove('hidden')
    updateprofile.classList.add('hidden')
    mycoursesContainer.classList.add('hidden')
myModal.classList.add('hidden')
})
updateprofileBtn.addEventListener('click', (e) => {
    e.preventDefault()
    updateprofile.classList.remove('hidden')
    mycoursesContainer.classList.add('hidden')
    myachievements.classList.add('hidden')
myModal.classList.add('hidden')
})
deleteprofileBtn.addEventListener('click', (e) => {
    e.preventDefault()
    myModal.classList.remove('hidden')
  
})

closeBtn.addEventListener('click',()=>{
    myModal.classList.add('hidden')
})