window.addEventListener('load', ()=> {

    const signUpForm = document.getElementById('signup-form');
    const loginForm = document.getElementById("login-form");

    signUpForm.addEventListener('submit', async (event) => {
        event.preventDefault(); 
        const formData = new FormData(signUpForm);

        try {
            const response = await fetch('/signup', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                console.log("You have a account now !!!")
                
            } else {
                throw new Error('Erreur lors de la requête POST : ' + response.status);
            }
        } catch (error) {
            console.error('Une erreur s\'est produite :', error);
        }
    });

    loginForm.addEventListener('submit' , async(event)=> {
        event.preventDefault(); 
        const formData = new FormData(loginForm);

        try {
            const response = await fetch('/login', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                alert("Welcome");
                
            } else {
                throw new Error('Erreur lors de la requête POST : ' + response.status);
            }
        } catch (error) {
            console.error('Une erreur s\'est produite :', error);
        }

    })


})

 
    
