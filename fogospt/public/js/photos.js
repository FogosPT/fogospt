/**
 * Photo gallery for fire incident pages.
 *
 * Exposes window.photos(id) — fetches the public photo list from
 * api.fogos.pt and renders a thumbnail grid into #f-photos-gallery,
 * wiring up PhotoSwipe v5 as the lightbox. Hides the whole section
 * when there are no photos or the request fails.
 */
(function () {
    var API = 'https://api.fogos.pt/v2/incidents/';
    var PER_PAGE = 20;
    var state = { id: null, page: 1, total: 0, loaded: 0 };
    var lightbox = null;

    function getSection() {
        return document.querySelector('.photos-section');
    }

    function getGallery() {
        return document.getElementById('f-photos-gallery');
    }

    function getLoadMore() {
        return document.querySelector('.photos-load-more');
    }

    function fmtDate(iso) {
        if (!iso) return '';
        if (typeof moment !== 'undefined') {
            return moment(iso).format('DD-MM-YYYY HH:mm');
        }
        return iso;
    }

    function ensureLightbox() {
        if (lightbox || !window.PhotoSwipeLightbox) return;
        lightbox = new window.PhotoSwipeLightbox({
            gallery: '#f-photos-gallery',
            children: 'a',
            pswpModule: function () {
                return import('https://unpkg.com/photoswipe@5/dist/photoswipe.esm.js');
            }
        });
        lightbox.on('uiRegister', function () {
            lightbox.pswp.ui.registerElement({
                name: 'caption',
                order: 9,
                isButton: false,
                appendTo: 'root',
                onInit: function (el, pswp) {
                    el.className = 'pswp__custom-caption';
                    pswp.on('change', function () {
                        var slide = pswp.currSlide && pswp.currSlide.data && pswp.currSlide.data.element;
                        el.textContent = slide && slide.dataset ? (slide.dataset.caption || '') : '';
                    });
                }
            });
        });
        lightbox.init();
    }

    function escapeAttr(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
    }

    function renderItem(item) {
        var url = item.url;
        if (!url) return '';
        var w = parseInt(item.width, 10) || 1600;
        var h = parseInt(item.height, 10) || 1200;
        var taken = item.captured_at || item.taken_at || '';
        var label = (window.trans && window.trans.photos && window.trans.photos.takenAt) || '';
        var caption = taken ? ((label ? label + ' ' : '') + fmtDate(taken)) : '';
        return '<a class="photos-item" href="' + escapeAttr(url) +
            '" target="_blank" rel="noopener"' +
            ' data-pswp-width="' + w + '" data-pswp-height="' + h +
            '" data-caption="' + escapeAttr(caption) + '">' +
            '<img src="' + escapeAttr(url) + '" loading="lazy" width="' + w + '" height="' + h +
            '" alt="" onerror="this.parentElement.style.display=\'none\'">' +
            '</a>';
    }

    function load(id, append) {
        var url = API + encodeURIComponent(id) + '/photos?page=' + state.page + '&per_page=' + PER_PAGE;
        return fetch(url, { credentials: 'omit' })
            .then(function (r) {
                if (!r.ok) throw new Error('http_' + r.status);
                return r.json();
            })
            .then(function (data) {
                if (!data || !data.success) throw new Error('not_success');
                var items = (data.data || []).filter(function (i) { return i && i.url; });
                state.total = (data.meta && typeof data.meta.total === 'number') ? data.meta.total : items.length;
                state.loaded += items.length;

                var gallery = getGallery();
                var section = getSection();
                if (!gallery || !section) return;

                if (state.total === 0 && !append) {
                    section.style.display = 'none';
                    return;
                }

                section.style.display = '';
                var html = items.map(renderItem).join('');
                if (append) gallery.insertAdjacentHTML('beforeend', html);
                else gallery.innerHTML = html;

                var btn = getLoadMore();
                if (btn) btn.style.display = (state.loaded < state.total) ? '' : 'none';

                ensureLightbox();
            })
            .catch(function () {
                if (!append) {
                    var section = getSection();
                    if (section) section.style.display = 'none';
                }
            });
    }

    window.photos = function (id) {
        if (!id) return;
        state = { id: id, page: 1, total: 0, loaded: 0 };
        var gallery = getGallery();
        if (gallery) gallery.innerHTML = '';
        var section = getSection();
        if (section) section.style.display = 'none';
        load(id, false);
    };

    document.addEventListener('click', function (e) {
        var btn = e.target && e.target.closest && e.target.closest('.photos-load-more');
        if (!btn || !state.id) return;
        e.preventDefault();
        state.page += 1;
        load(state.id, true);
    });

    // Late-binding: if PhotoSwipe module loads after photos() already ran,
    // wire up the lightbox once it's available.
    window.addEventListener('photoswipe-ready', ensureLightbox);
})();
