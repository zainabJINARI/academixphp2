const filterBtn = document.getElementById("filter-btn");
const hiddenSection = document.querySelector(".filter-section");
const resetBtn = document.querySelector(".reset-btn");
const searchBtn = document.querySelector(".search-course-btn")



const removeHide = ()=> {
    hiddenSection.classList.remove("hidden-section");
}

filterBtn.addEventListener('click' , ()=> {
    removeHide();
})

resetBtn.addEventListener('click' , ()=> {
    hiddenSection.classList.add("hidden-section")
})


searchBtn.addEventListener('click', () => {
    const selectedLevel = document.getElementById('level').value.toLowerCase(); 
    const selectedTeacher = document.getElementById('teacher').value.toLowerCase(); 
    const selectedDuration = document.getElementById('duration').value.toLowerCase(); 
    let url = window.location.href;
    let filter_path = `/filter?level=${selectedLevel}&tut=${selectedTeacher}&dur=${selectedDuration}`;
    window.location.href = filter_path;
    removeHide();
});