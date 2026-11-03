import Alpine from 'alpinejs';
import intersect from '@alpinejs/intersect';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';

Alpine.plugin(intersect);
Alpine.plugin(collapse);
Alpine.plugin(focus);

const reducedMotion = () => window.matchMedia('(prefers-reduced-motion: reduce)').matches;

// Compteur animé pour les chiffres clés
Alpine.data('countUp', (target) => ({
    value: target,
    run() {
        if (reducedMotion() || target < 10) return;
        const start = performance.now();
        const step = (now) => {
            const t = Math.min((now - start) / 1300, 1);
            this.value = Math.round(target * (1 - Math.pow(1 - t, 3)));
            if (t < 1) requestAnimationFrame(step);
        };
        this.value = 0;
        requestAnimationFrame(step);
    },
    get formatted() {
        return new Intl.NumberFormat('fr-FR').format(this.value).replace(/ /g, ' ');
    },
}));

// Carrousel horizontal (partenaires, témoignages) : flèches + défilement auto
Alpine.data('carousel', (interval = 3500) => ({
    scrollable: false,
    paused: false,
    timer: null,
    init() {
        const track = this.$refs.track;
        const update = () => { this.scrollable = track.scrollWidth > track.clientWidth + 4; };
        update();
        new ResizeObserver(update).observe(track);
        if (!reducedMotion() && interval) {
            this.timer = setInterval(() => { if (this.scrollable && !this.paused) this.go(1); }, interval);
        }
    },
    step() {
        const item = this.$refs.track.firstElementChild;
        return item ? item.getBoundingClientRect().width + parseFloat(getComputedStyle(this.$refs.track).columnGap || 0) : 0;
    },
    go(dir) {
        const track = this.$refs.track;
        const max = track.scrollWidth - track.clientWidth;
        if (dir > 0 && track.scrollLeft >= max - 4) track.scrollTo({ left: 0, behavior: reducedMotion() ? 'auto' : 'smooth' });
        else if (dir < 0 && track.scrollLeft <= 4) track.scrollTo({ left: max, behavior: reducedMotion() ? 'auto' : 'smooth' });
        else track.scrollBy({ left: dir * this.step(), behavior: reducedMotion() ? 'auto' : 'smooth' });
    },
    destroy() {
        clearInterval(this.timer);
    },
}));

// Bannière principale : l'image avance à la fin du remplissage de la pilule active (animationend),
// ce qui synchronise naturellement la pause au survol et désactive l'auto-défilement si les animations sont réduites.
Alpine.data('heroSlider', (count) => ({
    current: 0,
    count,
    paused: false,
    next() {
        this.current = (this.current + 1) % this.count;
    },
    go(index) {
        this.current = index;
    },
}));

window.Alpine = Alpine;
Alpine.start();

// Apparition au défilement
if ('IntersectionObserver' in window && !reducedMotion()) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-in');
                observer.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -6% 0px', threshold: 0.06 });

    document.documentElement.classList.add('reveal-ready');
    document.querySelectorAll('[data-reveal]').forEach((el) => observer.observe(el));
}
