let searchForm = document.querySelector('.search-form');

document.querySelector('#search-btn').onclick = () => {
    searchForm.classList.toggle('active');
    shoppingCart.classList.remove('active');
    navbar.classList.remove('active');
}

let shoppingCart = document.querySelector('.shopping-cart');

document.querySelector('#cart-btn').onclick = () => {
    shoppingCart.classList.toggle('active');
    searchForm.classList.remove('active');
    navbar.classList.remove('active');
}

// User login link
document.querySelector('#login-btn').onclick = () => {
    window.location.href = '../Register/login.html';
}

let navbar = document.querySelector('.navbar');

// Menu bar
document.querySelector('#menu-btn').onclick = () => {
    navbar.classList.toggle('active');
    searchForm.classList.remove('active');
    shoppingCart.classList.remove('active');
}

window.onscroll = () => {
    searchForm.classList.remove('active');
    shoppingCart.classList.remove('active');
    navbar.classList.remove('active');
}

// Initialize Swiper for product slider
var swiper = new Swiper(".products-slider", {
    loop: true,
    spaceBetween: 10,
    autoplay: {
        delay: 7500,
        disableOnInteraction: false, // Keeps autoplay going even if user interacts
    },
    centerSlides: true,  // Ensures the active slide is centered
    grabCursor: true,  // Enables hand cursor when hovering over the slider for swipe indication
    
    // Enable navigation arrows (for manual control)
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },

    // Responsive breakpoints
    breakpoints: {
        0: {
            slidesPerView: 1,  // 1 slide per view on small screens
        },
        768: {
            slidesPerView: 2,  // 2 slides per view on medium screens
        },
        1020: {
            slidesPerView: 3,  // 3 slides per view on large screens
        },
    },
});
