const show_lessons = document.querySelectorAll(".show-lessons");
const general_course = document.getElementById("general_course");
const course_ressources_btn = document.getElementById("course_ressources_btn");
const course_ressources = document.getElementById("course_ressources");
const courses_area_general = document.getElementById("courses-area");
const pop_up_module_create = document.getElementById("pop_up_module_create");
const add_module_course = document.querySelector(".add_module_course");
const pop_up_lesson_create = document.getElementById("pop_up_lesson_create");
const addLesson = document.querySelector(".add_lesson");

document
  .getElementById("btn-close-popup-create-module")
  .addEventListener("click", () => {
    pop_up_module_create.classList.add("hidden");
  });

pop_up_module_create.classList.add("hidden");
add_module_course.addEventListener("click", () => {
  pop_up_module_create.classList.toggle("hidden");
});

course_ressources.classList.add("hidden");

courses_area_general.classList.remove("hidden");

general_course.classList.add("selected");
course_ressources_btn.classList.remove("selected");

const tutor_course_module_lessons = document.getElementById(
  "tutor-course-module-lessons"
);

tutor_course_module_lessons?.classList.add("hidden");

show_lessons.forEach((btn) => {
  btn?.addEventListener("click", () => {
    tutor_course_module_lessons?.classList.toggle("hidden");
  });
});

general_course.addEventListener("click", () => {
  courses_area_general.classList.remove("hidden");
  course_ressources.classList.add("hidden");

  general_course.classList.add("selected");
  course_ressources_btn.classList.remove("selected");
});
course_ressources_btn.addEventListener("click", () => {
  courses_area_general.classList.add("hidden");
  course_ressources.classList.remove("hidden");

  general_course.classList.remove("selected");
  course_ressources_btn.classList.add("selected");
});

const showLessonsButtons = document.querySelectorAll(".show-lessons");

showLessonsButtons.forEach((button) => {
  button.addEventListener("click", () => {
    const moduleDetail = button.parentElement;
    moduleDetail.nextElementSibling.classList.toggle("hidden");
  });
});

// Add Button
let moduleid = "";
addLesson?.addEventListener("click", function () {
  let xhr = new XMLHttpRequest();
  moduleid = this.getAttribute("data-module-id");
  var pop_up_lesson_create = document.getElementById("pop_up_lesson_create");

  xhr.open("GET", `/course/add/lesson/${moduleid}`, true);
  xhr.onreadystatechange = function () {
    if (xhr.readyState === 4 && xhr.status === 200) {
      let formHtml = JSON.parse(xhr.responseText).formHtml;
      pop_up_lesson_create.innerHTML = formHtml;
      pop_up_lesson_create.style.display = "block";
    }
  };
  xhr.send();

  
});



document.querySelectorAll('.delete-module').forEach(function(button) {
    button.addEventListener('click', function(event) {
        event.preventDefault();
        let moduleId = this.getAttribute('data-id');
        let csrfToken = this.getAttribute('data-token');

        fetch(`/module/delete/${moduleId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Optionally, refresh the page or remove the module from the DOM
                location.reload();
            } else {
                alert('Error deleting module: ' + data.error);
            }
        });
    });
});