

var modal = document.getElementById("myModal");
var deleteBtn = document.getElementById("deleteBtn");
var cancelBtn = document.getElementById("cancelBtn");
var closeBtn = document.getElementsByClassName("close")[0];


// update profile section

const updateProfile= document.getElementById('updateprofile')
const updateProfileBtn = document.getElementById('updateBtn')
const courseAreaBtn = document.getElementById('requestsTutos')
const courseArea = document.getElementById('courses-area')

updateProfile.classList.add('hidden')

updateProfileBtn.addEventListener('click',()=>{
  updateProfile.classList.remove('hidden')
  courseArea.classList.add('hidden')
  courseAreaBtn.classList.remove('selected')
  updateProfileBtn.classList.add('selected')

})

// courses sectionbtnshowinput



courseAreaBtn.classList.add('selected')

courseAreaBtn.addEventListener('click',()=>{
  updateProfile.classList.add('hidden')
  courseArea.classList.remove('hidden')
  courseAreaBtn.classList.add('selected')
  updateProfileBtn.classList.remove('selected')
})

document.getElementById('btnshowinput').addEventListener('click',()=>{
  document.getElementById('image-upload').classList.toggle('hidden')
})



modal.classList.add('hidden')
const deletionItems = document.querySelectorAll(".deleteCourse");
const deletionForm = document.querySelector("#delete-course-form-container");

document.getElementById("addNewCourse").addEventListener("click", function () {
  document.getElementById("popup").style.display = "block";
});

deleteBtn.addEventListener('click', (e) => {
    e.preventDefault()
    modal.classList.remove('hidden')
})

cancelBtn.onclick = function () {
    modal.classList.add('hidden')
}
closeBtn.onclick = function () {
    modal.classList.add('hidden')
}



deletionItems.forEach((deletionItem) => {
    deletionItem.addEventListener("click", (event) => {
      event.preventDefault();
      const courseId = event.currentTarget.getAttribute("data-course-id");
      const url = `/course/delete/${courseId}`;
      const formHTML = `
              <form class="delete-course-form" id="delete-course-form" action="${url}" method="POST">
              <label for="description-content">Why do you want to delete this course?</label>
              <textarea id="description-content" name="description-content" rows="4" cols="50" required></textarea>
              <button type="submit" id="sendBtn">Send</button>
            </form>
             `;
      deletionForm.innerHTML = formHTML;
      deletionForm.style.display = "block";
    });
  });

