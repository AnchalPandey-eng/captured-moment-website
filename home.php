<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Homepage</title>
    <link rel="stylesheet" href="home.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">Explore Moments</div>
            <ul class="nav-links">
                <li><a href="home.php">Home</a></li>
                <li><a href="team.php">Team members</a></li>
                <li><a href="gallery.php">Gallery</a></li>
                <li><a href="contact.php">Contact</a></li>    
                <li><a href="admin.php">Admin</a></li>  
                
            </ul>
        </nav>
    </header>

    <section class="hero-section">
        <div class="hero-content">
            <h1>The Perfect Choice to Capture Moments</h1>
        </div>
    </section>

    <section class="about-section">
        <div class="about-content">
            <h2> Best Team with highly Experience </h2>
            <p>Enjoy a special evening in our delightful dining spaces. Whether you're looking for a private dinner or a casual meal, our restaurant offers something for everyone.</p>
            <p><strong>Anchal Panday</strong><br>Manager/ founder of Capture Movement</p>
        </div>
        <div class="about-cards">
            <div class="card">
                <img src="uploads/photo1.jpeg" alt="Photo">
                <p><b>photo 1 of captured moment.</b> This image captures a beautiful moment during a sunset, where the golden hues of the sky are reflected in the tranquil waters. It tells a story of serenity and natural beauty, encapsulating the essence of a peaceful evening. The photograph draws the viewer in with its rich colors and dramatic lighting, evoking a sense of calm and awe.
                </p>
            </div>
            <div class="card">
                <img src="uploads/photo2.jpeg" alt="photo">
                <p><b>photo 2 of captured moment.</b> In this stunning black-and-white photograph, a candid moment between a couple is beautifully captured. The emotional intensity of the moment is enhanced by the play of light and shadow, allowing the viewer to feel the depth of connection between the subjects. This image emphasizes authenticity and raw emotion, making it a perfect example of visual storytelling.

</p>
            </div>
            <div class="card">
                <img src="uploads/photo3.jpeg" alt="photo">
                <p><b>photo 3 of captured moment.</b> A lively street scene filled with vibrant energy, this image showcases the hustle and bustle of a local market. The photographer’s skillful use of framing and perspective highlights the chaotic yet harmonious atmosphere of the setting. The dynamic motion in the image conveys a sense of everyday life, inviting the viewer to step into the moment and experience it firsthand.

</b> 
</p>
            </div>
        </div>
    </section>

    <section class="about-section">
        <div class="about-content">
            <h2> About Us </h2>
            <div class="menu-cards">
                <div class="menu-card">
                    <h3>Why you Choose Us</h3>
                    <br>
                    <p> <b> i. Embracing the Art of Photography </b> <br> <br> Captured Moments is not just another photography guide; it’s an invitation to view the world through a lens of creativity, artistic discovery, and emotive storytelling. The book emphasizes that photography is about more than just capturing what we see—its about conveying how we feel.  </p>
                    <p> <b> ii. Visual Storytelling and Emotion </b> <br> <br> Photography freezes moments in time, telling stories and evoking emotions through images. The book delves into the heart of visual storytelling, guiding readers on how to harness light, shadow, composition, and perspective to craft compelling images. </p>
                    <p> <b> iii. Authenticity and Intimate Narrative </b> <br> <br> Captured Moments emphasizes authenticity. It suggests that capturing authentic moments in photography is about being in the moment yourself and letting your natural reactions show through. When photographers can let go and enjoy the experience, magic happens in front of their lens. </p>
                </div>
                <div class="menu-card">
                    <h3>Our  Features</h3>
                    <br>
                    <p> <b> i. Unique Candid Moments </b> <br> <br> In Unique Candid Moments, “Captured Moments” focuses on capturing candid and adorable moments in life. </p>
                    <p> <b> ii.Symbolic and Eye-Catching Shots </b> In Symbolic and Eye-Catching Shots, A great photograph, according to “Captured Moments,” goes beyond technical correctness. It carries a message to the audience and is both technically and symbolically meaningful. These shots tell visual stories, evoke emotions, and represent our collective thinking. </p>
                    <p> <b> iii. Emotional Connection </b> In Emotional Connection, Beyond technical aspects, “Captured Moments” emphasizes the emotional connection between the photographer and the subject. It’s about understanding the moment, identifying essential elements, and conveying the vibe through facial expressions, body language, and timing. </p>
                </div>
            </div>
            
        </div>
    </section>

    <section class="featured-dishes">
        <h2>Featured Dishes</h2>
        <div class="dishes">
            <div class="dish-card">
                <img src="uploads/Dish1.jpeg" alt="Dish 1">
                <p> This dish presents a beautifully plated gourmet meal, featuring seared scallops served over a creamy risotto with a garnish of fresh herbs. The dish is visually striking, with rich textures and vibrant colors that make it both a feast for the eyes and the taste buds. The delicate balance of flavors and artful presentation make this a standout dish on the menu.
</p>
            </div>
            <div class="dish-card">
                <img src="uploads/Dish2.jpeg" alt="Dish 2">
                <p> A vibrant salad bursting with fresh, seasonal vegetables, this dish highlights the beauty of simplicity. Crisp greens, juicy tomatoes, and a tangy vinaigrette come together to create a dish that’s both light and satisfying. The use of colorful ingredients adds a playful touch to the plate, making it as appealing to the eyes as it is to the palate.

</p>
            </div>
            <div class="dish-card">
                <img src="uploads/Dish3.jpeg" alt="Dish 3">
                <p> A decadent dessert of molten chocolate cake with a scoop of vanilla ice cream, drizzled with rich caramel sauce. This indulgent treat offers a perfect combination of warmth and coolness, with the gooey center of the cake melting in the mouth. The smooth ice cream and sweet sauce enhance the chocolate’s deep flavor, making it a delightful end to any meal.

</p>
            </div>
        </div>
    </section>

    <footer>
        <p>Capture Moments, By Anchal_Pandey.</p>
    </footer>
    <script>
       // Get all image cards and set up click events to open the modal
const imageCards = document.querySelectorAll('.card, .dish-card'); 
const modal = document.createElement('div');
modal.classList.add('modal');
document.body.appendChild(modal);

// Create close button for modal
const closeBtn = document.createElement('span');
closeBtn.classList.add('close');
closeBtn.innerHTML = '&times;';
modal.appendChild(closeBtn);

// Event listener for closing the modal
closeBtn.addEventListener('click', function () {
    modal.style.display = 'none';
});

// Open modal and show larger image with related description
imageCards.forEach(card => {
    card.addEventListener('click', function () {
        const imgSrc = card.querySelector('img').src; // Get image source
        const caption = card.querySelector('p').textContent; // Get the caption or description text

        modal.innerHTML = ''; // Clear previous content in modal

        // Create image element in modal and set its source
        const img = document.createElement('img');
        img.src = imgSrc;
        modal.appendChild(img); // Append the image to modal

        // Create description paragraph and add the caption text
        const description = document.createElement('div');
        description.classList.add('modal-content');
        description.innerHTML = `<p>${caption}</p>`; // Add description text
        modal.appendChild(description); // Append the description below the image

        // Display modal (make it visible)
        modal.style.display = 'flex';
    });
});

// Close modal when clicking outside of the image
modal.addEventListener('click', function (e) {
    if (e.target === modal) {
        modal.style.display = 'none';
    }
});
 </script>
</body>
</html>
