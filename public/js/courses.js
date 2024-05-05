
const dropdowns = document.querySelectorAll('.dropdown-modules');




dropdowns.forEach(dropdown => {

    const select = dropdown.querySelector(".select-module");
    const caret = dropdown.querySelector(".caret");
    const menu = dropdown.querySelector(".menu-modules ");
    const options = dropdown.querySelectorAll(".menu-modules li");
    const selected = dropdown.querySelector(".lesson");
    select.addEventListener('click' , ()=> {
        select.classList.toggle('select-clicked');
        caret.classList.toggle('caret-rotate');
        menu.classList.toggle('menu-open');

    });
    options.forEach (option => {
        option.addEventListener('click' , ()=> {
             select.classList.remove('select-clicked');
             caret.classList.remove('caret-rotate');
             menu.classList.remove('menu-open');
        })
    })
})

