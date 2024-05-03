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