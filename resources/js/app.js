import Alpine from 'alpinejs';
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';

window.Alpine = Alpine;
Alpine.start();

// NProgress Configuration
NProgress.configure({ showSpinner: false, speed: 400, minimum: 0.1 });

// Start progress bar on link clicks
document.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (link && link.href && !link.href.startsWith('#') && !link.target && link.host === window.location.host) {
        NProgress.start();
    }
});

// Start progress bar on form submissions
document.addEventListener('submit', () => {
    NProgress.start();
});

// Finish progress bar on page load (in case of full reloads or going back)
window.addEventListener('pageshow', () => {
    NProgress.done();
});
