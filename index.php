<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['logged_in'])) {
    header("location: login.php");
    exit;
}

?>




<?php include('layouts/header.php')?>


<style>
    /* CSS styles */
    .sliding-container {
        position: relative;
        width: 100%;
        overflow: hidden;
    }

    .box-container {
        margin-top: 25px;
        display: flex;
        transition: transform 0.5s ease;
    }

    .box {
        flex: 0 0 90%;
        /* Adjust the width of the box */
        height: 300px;
        position: relative;
        overflow: hidden;
        padding: 20px;
        margin: 0 5%;
        /* Added margin on both sides */
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        border: 1px solid #ddd;
        transition: all 0.5s ease;
        /* Added transition for smooth animation */
        animation: stretchAnimation 0.5s ease forwards;
        /* Add animation */
    }

    .sliding-container.animate .box {
        animation: none;
        /* Disable animation during sliding animation */
    }

    /* Define animation keyframes */
    @keyframes stretchAnimation {
        from {
            transform: scale(0.8);
            /* Start scale */
        }

        to {
            transform: scale(1);
            /* End scale */
        }
    }

    /* Add this for animation when card is hovered */
    .box:hover {
        transform: scale(1.05);
        /* Increase size slightly on hover */
        box-shadow: 0 0 20px rgba(33, 33, 33, 0.3);
        /* Adjust shadow */
    }

    /* Add this for animation when switching between cards */
    .box:not(.active) {
        opacity: 0.7;
        /* Reduce opacity for inactive cards */
        transform: scale(0.95);
        /* Slightly shrink inactive cards */
    }

    .box1-content {
        text-align: center;
        color: #222;
    }

    .box1-content h1 {
        font-family: 'Arial', sans-serif;
        color: #222;
        font-size: 36px;
        font-weight: bold;
        margin-top: 50px;
        margin-bottom: 10px;
    }

    .box1-content p {
        font-family: 'Arial', sans-serif;
        color: #222;
        font-size: 18px;
        line-height: 1.5;
        margin-bottom: 20px;
    }

    .circles {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .circle {
        width: 10px;
        height: 10px;
        background-color: #222;
        border-radius: 50%;
        margin: 0 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .active {
        background-color: orange;
    }

    .decoration {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0.3;
        z-index: 0;
    }

    .image-category-card {
        display: flex;
        margin: 10px 3vw;
    }

    @media (max-width: 768px) {

        .sliding-container {
            height: 30vh;
            margin-top: 50px;
        }

        .box {
            font-size: 1rem;
        }

        .box {
            padding: 10px;
            /* Smaller padding */
        }

        .box h1 {
            font-size: 1.5rem;
            margin-top: 20px;
            /* Smaller margin */

            .box p {
                font-size: 1rem;
                /* Smaller text size */
            }

        }
</style>

<div class="sliding-container">
    <div class="box-container">
        <div class="box active">
            <div class="decoration"></div>
            <div class="box1-content">
                <h1>Welcome to Our Website</h1>
                <p>It's a Step to Revolutionize</p>
                <p>Taking Shop Online </p>
            </div>
        </div>
        <div class="box">Box 2</div>
        <div class="box">Box 3</div>
    </div>
</div>

<div class="circles">
    <div class="circle active"></div>
    <div class="circle"></div>
    <div class="circle"></div>
</div>

<div style="margin: auto;margin: 20px auto" class="separator"></div>
<!-- Updated HTML Structure -->
<section class="image-category">
    <div class="image-category-card">
        <a href="categories_shop.php?category_id=1" style="text-decoration: none; color: inherit;">
            <div class="image-card">
                <img src="img/home-page-categories/dry-fruits-index-page.jpeg" alt="Dry Fruits Image">
            </div>
        </a>
        <a href="monthly_grocery.php?monthly_grocery=1" style="text-decoration: none; color: inherit;">
            <div class="image-card">
                <img src="img/home-page-categories/monthly-grocery.jpeg" alt="Dry Fruits Image">
            </div>
        </a>
    </div>
    <div class="image-category-card">
        <a href="list_grocery.php?protein_id=1" style="text-decoration: none; color: inherit;">
            <div class="image-card">
                <img src="img/home-page-categories/protein-index.jpeg" alt="Dry Fruits Image">
            </div>
        </a>
        <a href="categories_shop.php?category_id=4" style="text-decoration: none; color: inherit;">
            <div class="image-card">
                <img src="img/home-page-categories/namkeen&sweets.jpeg" alt="Dry Fruits Image">
            </div>
        </a>
    </div>
    <div class="image-category-card">
        <a href="categories_shop.php?category_id=14" style="text-decoration: none; color: inherit;">
            <div class="image-card">
                <img src="img/home-page-categories/clothing-surf.jpeg" alt="Dry Fruits Image">
            </div>
        </a>
        <a href="categories_shop.php?category_id=13" style="text-decoration: none; color: inherit;">
            <div class="image-card">
                <img src="img/home-page-categories/bathing-soap.jpeg" alt="Dry Fruits Image">
            </div>
        </a>

    </div>


</section>

<!-- Updated CSS Styles -->
<style>
    .image-card {
        /* Adjust as needed */
        margin: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
    }

    .image-card img {
        width: 100%;
        height: auto;
        display: block;
    }

    .image-category {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-top: 20px;
    }

    .image-category-card a {
        text-decoration: none;
        color: inherit;
        width: 100%;
    }

    @media (max-width: 768px) {
        .image-card {
            width: 89%;
            height: auto;
            display: block;
            margin: 3px auto;
            border-radius: 20px;
        }

        .image-category-card {
            display: flex;
            flex-direction: column;
            width: 90%;
            margin: 10px auto;
        }
    }
</style>



<div style="margin: auto;margin: 20px auto" class="separator"></div>


<script>
    // JavaScript code
    const boxContainer = document.querySelector('.box-container');
    const circles = document.querySelectorAll('.circle');
    let activeIndex = 0;

    circles.forEach((circle, index) => {
        circle.addEventListener('click', () => {
            setActive(index);
        });
    });

    function setActive(index) {
        activeIndex = index;
        updateSlider();
    }

    function updateSlider() {
        const offset = -activeIndex * 100;
        boxContainer.style.transform = `translateX(${offset}%)`;
        circles.forEach((circle, index) => {
            if (index === activeIndex) {
                circle.classList.add('active');
            } else {
                circle.classList.remove('active');
            }
        });
        // Add a delay to let the transform finish before triggering the animation
        setTimeout(() => {
            boxContainer.classList.add('animate');
        }, 100);
    }

    // Function to remove animation class after animation ends
    boxContainer.addEventListener('transitionend', () => {
        boxContainer.classList.remove('animate');
    });

    // Function to automatically change slides
    function autoSlide() {
        activeIndex = (activeIndex + 1) % circles.length;
        updateSlider();
    }

    // Change slide every 3 seconds
    setInterval(autoSlide, 3000);
</script>

<?php include('layouts/footer.php') ?>