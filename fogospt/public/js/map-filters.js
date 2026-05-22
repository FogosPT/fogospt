/**
 * Advanced filters panel — numeric inputs (active >= N hours, total
 * resources >= N) applied client-side to the cached fire list. Calls
 * window.fogosApplyFilters() (defined in main.js) to re-render markers
 * after a short debounce.
 *
 * State persists in localStorage under "fogos:map-filters".
 */
(function () {
    var STORE_KEY = 'fogos:map-filters';

    function loadState() {
        try {
            if (typeof store !== 'undefined' && store.get) {
                return store.get(STORE_KEY) || {};
            }
            var raw = localStorage.getItem(STORE_KEY);
            return raw ? JSON.parse(raw) : {};
        } catch (e) { return {}; }
    }
    function saveState(s) {
        try {
            if (typeof store !== 'undefined' && store.set) store.set(STORE_KEY, s);
            else localStorage.setItem(STORE_KEY, JSON.stringify(s));
        } catch (e) {}
    }

    function injectStyles() {
        if (document.getElementById('fogos-filters-styles')) return;
        var css = '' +
            '.fogos-filters{width:240px;background:#fff;border-radius:8px;box-shadow:0 2px 12px rgba(0,0,0,.18);font-family:"Roboto",sans-serif;user-select:none;overflow:hidden}' +
            '.fogos-filters.is-collapsed{width:44px}' +
            '.fogos-filters.is-collapsed .fogos-filters__body,.fogos-filters.is-collapsed .fogos-filters__title{display:none}' +
            '.fogos-filters.is-collapsed .fogos-filters__header{cursor:pointer;justify-content:center;padding:10px}' +
            '.fogos-filters.is-collapsed .fogos-filters__collapse i{transform:rotate(180deg)}' +
            '.fogos-filters__header{display:flex;align-items:center;justify-content:space-between;padding:10px 12px;background:linear-gradient(135deg,#3b6db5,#1f3f73);color:#fff;font-weight:500;font-size:13px;text-transform:uppercase;letter-spacing:.4px}' +
            '.fogos-filters__title{flex:1}' +
            '.fogos-filters__collapse{background:transparent;border:0;color:#fff;cursor:pointer;padding:4px 6px;font-size:14px;line-height:1}' +
            '.fogos-filters__collapse i{transition:transform .2s}' +
            '.fogos-filters__body{padding:10px 12px}' +
            '.fogos-filters__row{display:flex;align-items:center;justify-content:space-between;margin-bottom:8px}' +
            '.fogos-filters__row:last-child{margin-bottom:0}' +
            '.fogos-filters__label{font-size:12px;color:#333;flex:1;margin-right:8px}' +
            '.fogos-filters__input{width:70px;padding:4px 6px;border:1px solid #ccc;border-radius:4px;font-size:13px;text-align:right}' +
            '.fogos-filters__input:focus{outline:none;border-color:#3b6db5}' +
            '.fogos-filters__footer{margin-top:8px;display:flex;justify-content:space-between;align-items:center;font-size:11px;color:#666}' +
            '.fogos-filters__clear{background:transparent;border:0;color:#3b6db5;cursor:pointer;font-size:11px;padding:0;text-decoration:underline}';
        var s = document.createElement('style');
        s.id = 'fogos-filters-styles';
        s.appendChild(document.createTextNode(css));
        document.head.appendChild(s);
    }

    L.Control.FogosFilters = L.Control.extend({
        options: { position: 'topleft' },

        onAdd: function (map) {
            injectStyles();
            this._map = map;
            this._state = loadState();
            window.fogosFilters = {
                minHours: parseFloat(this._state.minHours) || 0,
                minMeios: parseFloat(this._state.minMeios) || 0
            };

            var c = this._container = L.DomUtil.create('div', 'fogos-filters');
            if (this._state.__collapsed !== false) {
                L.DomUtil.addClass(c, 'is-collapsed');
            }
            L.DomEvent.disableClickPropagation(c);
            L.DomEvent.disableScrollPropagation(c);

            this._render();
            return c;
        },

        _render: function () {
            var tf = (window.trans && window.trans.filtersPanel) || {};
            var f = window.fogosFilters;
            var html = '';
            html += '<div class="fogos-filters__header" data-role="header">';
            html += '  <span class="fogos-filters__title">' + this._escape(tf.title || 'Filtros avançados') + '</span>';
            html += '  <button type="button" class="fogos-filters__collapse" aria-label="Toggle"><i class="fas fa-chevron-left"></i></button>';
            html += '</div>';
            html += '<div class="fogos-filters__body">';
            html += '  <div class="fogos-filters__row">';
            html += '    <label class="fogos-filters__label" for="fogos-f-hours">' + this._escape(tf.minHours || 'Ativo há mais de (h)') + '</label>';
            html += '    <input id="fogos-f-hours" class="fogos-filters__input" type="number" min="0" step="1" data-filter="minHours" value="' + (f.minHours || '') + '">';
            html += '  </div>';
            html += '  <div class="fogos-filters__row">';
            html += '    <label class="fogos-filters__label" for="fogos-f-meios">' + this._escape(tf.minMeios || 'Total de meios ≥') + '</label>';
            html += '    <input id="fogos-f-meios" class="fogos-filters__input" type="number" min="0" step="1" data-filter="minMeios" value="' + (f.minMeios || '') + '">';
            html += '  </div>';
            html += '  <div class="fogos-filters__footer">';
            html += '    <span data-role="count"></span>';
            html += '    <button type="button" class="fogos-filters__clear" data-role="clear">' + this._escape(tf.clear || 'Limpar') + '</button>';
            html += '  </div>';
            html += '</div>';
            this._container.innerHTML = html;
            this._bind();
            this._renderCount();
        },

        _renderCount: function () {
            if (!this._container) return;
            var el = this._container.querySelector('[data-role="count"]');
            if (!el) return;
            var c = window.fogosFilterCount;
            if (!c) { el.textContent = ''; return; }
            var tf = (window.trans && window.trans.filtersPanel) || {};
            var tpl = tf.showing || '{shown}/{total}';
            el.textContent = tpl.replace('{shown}', c.shown).replace('{total}', c.total);
        },

        _bind: function () {
            var self = this;
            var collapseBtn = this._container.querySelector('.fogos-filters__collapse');
            if (collapseBtn) {
                L.DomEvent.on(collapseBtn, 'click', function (e) {
                    L.DomEvent.stop(e);
                    self._toggleCollapse();
                });
            }
            var header = this._container.querySelector('[data-role="header"]');
            if (header) {
                L.DomEvent.on(header, 'click', function (e) {
                    if (L.DomUtil.hasClass(self._container, 'is-collapsed')) {
                        L.DomEvent.stop(e);
                        self._toggleCollapse();
                    }
                });
            }
            var inputs = this._container.querySelectorAll('.fogos-filters__input');
            Array.prototype.forEach.call(inputs, function (input) {
                L.DomEvent.on(input, 'input', function () {
                    var key = input.getAttribute('data-filter');
                    var val = parseFloat(input.value);
                    val = (isNaN(val) || val < 0) ? 0 : val;
                    window.fogosFilters[key] = val;
                    self._state[key] = val;
                    saveState(self._state);
                    self._scheduleApply();
                });
            });
            var clearBtn = this._container.querySelector('[data-role="clear"]');
            if (clearBtn) {
                L.DomEvent.on(clearBtn, 'click', function (e) {
                    L.DomEvent.stop(e);
                    window.fogosFilters = { minHours: 0, minMeios: 0 };
                    self._state.minHours = 0;
                    self._state.minMeios = 0;
                    saveState(self._state);
                    self._render();
                    self._scheduleApply();
                });
            }
        },

        _toggleCollapse: function () {
            var collapsed = !L.DomUtil.hasClass(this._container, 'is-collapsed');
            if (collapsed) L.DomUtil.addClass(this._container, 'is-collapsed');
            else L.DomUtil.removeClass(this._container, 'is-collapsed');
            this._state.__collapsed = collapsed;
            saveState(this._state);
        },

        _scheduleApply: function () {
            if (this._debounce) clearTimeout(this._debounce);
            this._debounce = setTimeout(function () {
                if (typeof window.fogosApplyFilters === 'function') {
                    window.fogosApplyFilters();
                }
            }, 250);
        },

        refreshCount: function () { this._renderCount(); },

        _escape: function (s) {
            return String(s == null ? '' : s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }
    });

    L.control.fogosFilters = function (options) {
        return new L.Control.FogosFilters(options);
    };
})();
