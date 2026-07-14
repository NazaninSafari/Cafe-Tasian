document.addEventListener('DOMContentLoaded', () => {
   
});


function revealOnScroll() {
    const reveals = document.querySelectorAll('.reveal'); 

    reveals.forEach((section, index) => {
        const elementTop = section.getBoundingClientRect().top;
        const viewportHeight = window.innerHeight;

       
        if (elementTop < viewportHeight - 100) {
            section.classList.add('visible');
        } else {
            
        }
    });
}

window.addEventListener('scroll', revealOnScroll);
revealOnScroll(); 