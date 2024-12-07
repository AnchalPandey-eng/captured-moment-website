// gallery.js

// Wait for the DOM to be fully loaded before applying hover effects
document.addEventListener("DOMContentLoaded", function() {
    // Select all gallery items that contain images
    const galleryItems = document.querySelectorAll('.gallery-item');

    // Loop through each gallery item and add hover event listeners
    galleryItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            // Add the 'hover' class when mouse enters the item
            this.classList.add('hover');
        });

        item.addEventListener('mouseleave', function() {
            // Remove the 'hover' class when mouse leaves the item
            this.classList.remove('hover');
        });
    });
});
