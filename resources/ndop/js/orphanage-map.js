// Carte des orphelinats : Leaflet + regroupement des points, chargée seulement
// quand la carte arrive à l'écran (import dynamique depuis app.js).
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster';
import 'leaflet.markercluster/dist/MarkerCluster.css';
import border from './cameroon-border.json';

const CAMEROON_BOUNDS = L.latLngBounds([1.6, 8.4], [13.1, 16.2]);
const nf = new Intl.NumberFormat('fr-FR');
const fmt = (n) => nf.format(n).replace(/ | /g, ' ');

const esc = (value) => String(value ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' })[c]);

function popupHtml(p) {
    const maxAge = Math.max(1, ...Object.values(p.ages));
    const ages = Object.entries(p.ages).map(([label, n]) => `
        <div class="om-age">
            <span>${esc(label)}</span>
            <span class="om-bar"><i style="width:${Math.round((n / maxAge) * 100)}%"></i></span>
            <b>${fmt(n)}</b>
        </div>`).join('');
    const needs = p.needs.length
        ? `<ul class="om-needs" aria-label="Besoins">${p.needs.map((n) => `<li>${esc(n)}</li>`).join('')}</ul>`
        : '';

    return `
        <article class="om-card">
            <img src="${esc(p.photo)}" alt="" loading="lazy" onerror="this.onerror=null;this.src='${esc(p.fallback)}'">
            <div class="om-body">
                <p class="om-place">${esc(p.place)}</p>
                <h3>${esc(p.name)}</h3>
                <dl class="om-stats">
                    <div><dt>Enfants</dt><dd>${fmt(p.children)}</dd></div>
                    <div><dt>Filles</dt><dd>${fmt(p.girls)}</dd></div>
                    <div><dt>Garçons</dt><dd>${fmt(p.boys)}</dd></div>
                </dl>
                <div class="om-ages">${ages}</div>
                ${needs}
                <p class="om-raised">Dons reçus : <b>${fmt(p.raised)} FCFA</b></p>
                <a href="${esc(p.url)}" class="om-link">Voir l'orphelinat →</a>
            </div>
        </article>`;
}

const pin = L.divIcon({
    className: 'om-pin',
    html: '<span></span>',
    iconSize: [22, 22],
    iconAnchor: [11, 11],
    popupAnchor: [0, -10],
});

export function mountOrphanageMap(el, points, fit = 'points') {
    const finePointer = window.matchMedia('(hover: hover) and (pointer: fine)').matches;

    const map = L.map(el, {
        zoomControl: true,
        closePopupOnClick: false, // géré plus bas pour ne pas refermer la fiche qu'on vient d'ouvrir
        scrollWheelZoom: false, // la page reste défilable ; zoom avec les boutons ou ctrl + molette
        minZoom: 5,
        maxBounds: CAMEROON_BOUNDS.pad(0.6),
        attributionControl: true,
    });

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        className: 'om-tiles',
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    // Voile sur les pays voisins, contour du Cameroun
    const ring = border.map(([lng, lat]) => [lat, lng]);
    L.polygon([[[-90, -180], [-90, 180], [90, 180], [90, -180]], ring], {
        stroke: false, fillColor: '#0b0f30', fillOpacity: 0.32, interactive: false,
    }).addTo(map);
    L.polygon(ring, { color: '#141a4a', weight: 1.5, fill: false, interactive: false }).addTo(map);

    // Ctrl/⌘ + molette pour zoomer sans bloquer le défilement de la page
    el.addEventListener('wheel', (e) => {
        if (e.ctrlKey || e.metaKey) {
            e.preventDefault();
            map.setZoom(map.getZoom() + (e.deltaY < 0 ? 1 : -1));
        }
    }, { passive: false });

    const cluster = L.markerClusterGroup({
        showCoverageOnHover: false,
        maxClusterRadius: (zoom) => (zoom < 10 ? 44 : 28),
        spiderfyOnMaxZoom: true,
        iconCreateFunction: (c) => L.divIcon({
            className: 'om-cluster',
            html: `<span>${c.getChildCount()}</span>`,
            iconSize: [40, 40],
        }),
    });

    // Fiche au clic (souris comme tactile). On n'utilise pas bindPopup : son clic
    // « bascule » la fiche, et un appui tactile qui produit deux clics l'ouvrait puis la refermait.
    // Un clic sur le fond de carte ferme la fiche, sauf juste après son ouverture.
    let openedAt = 0;
    map.on('click', () => {
        if (Date.now() - openedAt > 400) map.closePopup();
    });

    points.forEach((p) => {
        const marker = L.marker([p.lat, p.lng], { icon: pin, title: p.name, alt: p.name, riseOnHover: true, city: p.city });
        const popup = L.popup({
            className: 'om-popup',
            maxWidth: 300,
            minWidth: 260,
            offset: [0, -10],
            autoPanPadding: [24, 24],
        });

        marker.on('click keypress', (e) => {
            if (e.originalEvent) L.DomEvent.stopPropagation(e.originalEvent);
            openedAt = Date.now();
            if (popup.isOpen()) return;
            popup.setLatLng(marker.getLatLng()).setContent(popupHtml(p)).openOn(map);
        });

        cluster.addLayer(marker);
    });

    // Groupes (souris) : nombre d'orphelinats et villes au survol, clic pour zoomer
    if (finePointer) cluster.on('clustermouseover', (e) => {
        const markers = e.layer.getAllChildMarkers();
        const cities = [...new Set(markers.map((m) => m.options.city).filter(Boolean))].join(', ');
        e.layer.bindTooltip(
            `<b>${markers.length} orphelinats</b>${cities ? `<br>${esc(cities)}` : ''}<br><span>${finePointer ? 'Cliquez' : 'Touchez'} pour zoomer</span>`,
            { direction: 'top', offset: [0, -20], className: 'om-tip' },
        ).openTooltip();
    });
    cluster.on('clustermouseout', (e) => e.layer.closeTooltip?.());

    map.addLayer(cluster);

    if (fit === 'points' && points.length) {
        map.fitBounds(cluster.getBounds(), { padding: [40, 40], maxZoom: 14 });
    } else {
        map.fitBounds(CAMEROON_BOUNDS);
    }

    // Le conteneur peut changer de taille (onglet Liste / Carte, rotation)
    new ResizeObserver(() => map.invalidateSize()).observe(el);

    return map;
}
