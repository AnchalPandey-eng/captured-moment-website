document.addEventListener("DOMContentLoaded", function () {
    // Image Preview for the Image Upload Field
    const imageInput = document.querySelector("input[type='file'][name='image']");
    const imagePreview = document.createElement("img");
    imagePreview.style.maxWidth = "200px";
    imagePreview.style.maxHeight = "200px";
    imagePreview.style.marginTop = "10px";

    if (imageInput) {
        // Add the image preview element after the image input field
        imageInput.parentNode.appendChild(imagePreview);

        imageInput.addEventListener("change", function () {
            const file = imageInput.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    // Set the image source to the uploaded file
                    imagePreview.src = e.target.result;
                };

                // Read the file as a Data URL
                reader.readAsDataURL(file);
            } else {
                imagePreview.src = "";
            }
        });
    }

    // Confirmation Dialog for Deleting Team Members
    const deleteLinks = document.querySelectorAll("a[href*='delete_team']");

    deleteLinks.forEach(link => {
        link.addEventListener("click", function (event) {
            if (!confirm("Are you sure you want to delete this team member?")) {
                event.preventDefault(); // Prevent the default delete action if user cancels
            }
        });
    });
});
/* admin section */
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', (event) => {
        const requiredFields = form.querySelectorAll('[required]');
        for (const field of requiredFields) {
            if (!field.value.trim()) {
                alert('Please fill in all required fields.');
                event.preventDefault();
                break;
            }
        }
    });
});
