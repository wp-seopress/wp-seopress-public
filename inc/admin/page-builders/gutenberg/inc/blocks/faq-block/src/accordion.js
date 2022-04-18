const accordions = document.querySelectorAll(".wpseopress-wrap-faq-question");
for (let i = 0; i < accordions.length; i++) {
    accordions[i].addEventListener('click', function (e) {
        var btn = this.nextElementSibling;
        var aria = this.querySelector('.wpseopress-accordion-button');
        btn.classList.toggle('wpseopress-hide');
        aria.getAttribute('aria-expanded') === 'true' ? aria.setAttribute('aria-expanded', 'false') : aria.setAttribute('aria-expanded', 'true');
    });
}
