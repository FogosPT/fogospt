/**
 * Unified map control panel — groups base layer, fire status filters,
 * fire risk, satellite hotspots and weather overlays in a single
 * collapsible side panel. Replaces multiple stacked L.control.layers.
 *
 * Public API:
 *   panel = new L.Control.FogosPanel().addTo(map)
 *   panel.registerSection(key, label, type)         // type: 'checkbox' | 'radio'
 *   panel.addItem(sectionKey, id, label, layer, defaultOn)
 *   panel.updateLayer(sectionKey, id, newLayer)     // swap layer reference (refresh)
 *   panel.clearSection(sectionKey)                  // remove all items in a section
 *
 * State (which items are on, which sections collapsed, whole panel collapsed)
 * persists in localStorage under "fogos:map-panel".
 */
L.Control.FogosPanel = L.Control.extend({
    options: { position: 'topright' },

    onAdd: function (map) {
        this._map = map;
        this._sections = {};
        this._order = [];
        this._oneWay = {};
        this._state = this._load();

        var c = this._container = L.DomUtil.create('div', 'fogos-panel');
        // Collapsed by default. Only stay open once the user has explicitly opened it.
        if (this._state.__panel_collapsed !== false) {
            L.DomUtil.addClass(c, 'is-collapsed');
        }
        L.DomEvent.disableClickPropagation(c);
        L.DomEvent.disableScrollPropagation(c);

        this._render();
        return c;
    },

    registerSection: function (key, label, type) {
        if (!this._sections[key]) {
            this._sections[key] = { label: label, type: type || 'checkbox', items: [], order: [] };
            this._order.push(key);
        } else {
            this._sections[key].label = label;
            this._sections[key].type = type || this._sections[key].type;
        }
        this._render();
        return this;
    },

    addItem: function (sectionKey, id, label, layer, defaultOn, force) {
        var section = this._sections[sectionKey];
        if (!section) return this;

        var existing = this._findItem(sectionKey, id);
        if (existing) {
            existing.label = label;
            existing.layer = layer;
        } else {
            section.items.push({ id: id, label: label, layer: layer });
            section.order.push(id);
        }

        var stateKey = this._stateKey(sectionKey, id);
        var on = (force) ? !!defaultOn : ((stateKey in this._state) ? !!this._state[stateKey] : !!defaultOn);

        // For radio sections, enforce single-selection on first registration.
        if (section.type === 'radio' && on) {
            section.items.forEach(function (other) {
                if (other.id !== id) {
                    var oKey = this._stateKey(sectionKey, other.id);
                    if (this._state[oKey]) {
                        this._removeLayer(other.layer);
                        this._state[oKey] = false;
                    }
                }
            }, this);
        }

        this._state[stateKey] = on;
        if (on) this._addLayer(layer);
        else this._removeLayer(layer);

        this._render();
        this._save();
        return this;
    },

    updateLayer: function (sectionKey, id, newLayer) {
        var item = this._findItem(sectionKey, id);
        if (!item) return this;
        var on = !!this._state[this._stateKey(sectionKey, id)];
        if (this._map.hasLayer(item.layer)) this._map.removeLayer(item.layer);
        item.layer = newLayer;
        if (on) this._addLayer(newLayer);
        return this;
    },

    clearSection: function (sectionKey) {
        var section = this._sections[sectionKey];
        if (!section) return this;
        section.items.forEach(function (item) {
            if (this._map.hasLayer(item.layer)) this._map.removeLayer(item.layer);
        }, this);
        section.items = [];
        section.order = [];
        this._render();
        return this;
    },

    isOn: function (sectionKey, id) {
        return !!this._state[this._stateKey(sectionKey, id)];
    },

    // Mark an item as one-way: once it is on, clicking it again is a no-op
    // instead of toggling it off. Avoids re-triggering expensive side effects
    // (e.g. refetching markers) on repeated clicks.
    markOneWay: function (sectionKey, id) {
        this._oneWay[this._stateKey(sectionKey, id)] = true;
        return this;
    },

    // ----- internals -----

    _stateKey: function (sectionKey, id) {
        return sectionKey + ':' + id;
    },

    _findItem: function (sectionKey, id) {
        var section = this._sections[sectionKey];
        if (!section) return null;
        for (var i = 0; i < section.items.length; i++) {
            if (section.items[i].id === id) return section.items[i];
        }
        return null;
    },

    _addLayer: function (layer) {
        if (!layer) return;
        if (!this._map.hasLayer(layer)) layer.addTo(this._map);
    },

    _removeLayer: function (layer) {
        if (!layer) return;
        if (this._map.hasLayer(layer)) this._map.removeLayer(layer);
    },

    _toggleItem: function (sectionKey, id) {
        var section = this._sections[sectionKey];
        if (!section) return;
        var stateKey = this._stateKey(sectionKey, id);
        var item = this._findItem(sectionKey, id);
        if (!item) return;

        if (section.type === 'radio') {
            var wasOn = !!this._state[stateKey];
            section.items.forEach(function (other) {
                var oKey = this._stateKey(sectionKey, other.id);
                if (this._state[oKey]) {
                    this._removeLayer(other.layer);
                    this._state[oKey] = false;
                }
            }, this);
            if (!wasOn) {
                this._addLayer(item.layer);
                this._state[stateKey] = true;
            }
        } else {
            if (this._state[stateKey]) {
                if (this._oneWay[stateKey]) return;
                this._removeLayer(item.layer);
                this._state[stateKey] = false;
            } else {
                this._addLayer(item.layer);
                this._state[stateKey] = true;
            }
        }
        this._render();
        this._save();
    },

    _toggleSectionCollapse: function (key) {
        var stateKey = '__section_' + key + '_collapsed';
        this._state[stateKey] = !this._state[stateKey];
        this._render();
        this._save();
    },

    _togglePanelCollapse: function () {
        var collapsed = !L.DomUtil.hasClass(this._container, 'is-collapsed');
        if (collapsed) L.DomUtil.addClass(this._container, 'is-collapsed');
        else L.DomUtil.removeClass(this._container, 'is-collapsed');
        this._state.__panel_collapsed = collapsed;
        this._save();
    },

    _render: function () {
        if (!this._container) return;
        var t = (window.trans && window.trans.panel) || {};
        var html = '';
        html += '<div class="fogos-panel__header" data-role="header">';
        html += '  <span class="fogos-panel__title">' + this._escape(t.title || 'Mapa') + '</span>';
        html += '  <button type="button" class="fogos-panel__collapse" aria-label="Toggle panel">';
        html += '    <i class="fas fa-chevron-right"></i>';
        html += '  </button>';
        html += '</div>';
        html += '<div class="fogos-panel__body">';

        var self = this;
        this._order.forEach(function (key) {
            var s = self._sections[key];
            if (!s.items.length) return;
            var collapsed = !!self._state['__section_' + key + '_collapsed'];
            html += '<div class="fogos-panel__section' + (collapsed ? ' is-collapsed' : '') + '" data-section="' + self._escape(key) + '">';
            html += '  <h4 class="fogos-panel__section-title" data-role="section-toggle">';
            html += '    <span>' + self._escape(s.label) + '</span>';
            html += '    <i class="fas fa-chevron-' + (collapsed ? 'down' : 'up') + '"></i>';
            html += '  </h4>';
            html += '  <ul class="fogos-panel__items">';
            s.items.forEach(function (item) {
                var on = !!self._state[self._stateKey(key, item.id)];
                var input = (s.type === 'radio')
                    ? '<input type="radio" name="fogos-panel-' + self._escape(key) + '"' + (on ? ' checked' : '') + ' tabindex="-1">'
                    : '<input type="checkbox"' + (on ? ' checked' : '') + ' tabindex="-1">';
                html += '<li class="fogos-panel__item' + (on ? ' is-on' : '') + '"><label data-id="' + self._escape(item.id) + '">';
                html += input;
                html += '<span class="fogos-panel__label">' + self._escape(item.label) + '</span>';
                html += '</label></li>';
            });
            html += '  </ul>';
            html += '</div>';
        });

        html += '</div>';
        var prevBody = this._container.querySelector('.fogos-panel__body');
        var prevScroll = prevBody ? prevBody.scrollTop : 0;
        this._container.innerHTML = html;
        var newBody = this._container.querySelector('.fogos-panel__body');
        if (newBody && prevScroll) newBody.scrollTop = prevScroll;
        this._bind();
    },

    _bind: function () {
        var self = this;
        var collapseBtn = this._container.querySelector('.fogos-panel__collapse');
        if (collapseBtn) {
            L.DomEvent.on(collapseBtn, 'click', function (e) {
                L.DomEvent.stop(e);
                self._togglePanelCollapse();
            });
        }
        // Tap on header (when collapsed) re-opens
        var header = this._container.querySelector('[data-role="header"]');
        if (header) {
            L.DomEvent.on(header, 'click', function (e) {
                if (L.DomUtil.hasClass(self._container, 'is-collapsed')) {
                    L.DomEvent.stop(e);
                    self._togglePanelCollapse();
                }
            });
        }
        var titles = this._container.querySelectorAll('[data-role="section-toggle"]');
        Array.prototype.forEach.call(titles, function (el) {
            L.DomEvent.on(el, 'click', function (e) {
                L.DomEvent.stop(e);
                var key = el.parentElement.getAttribute('data-section');
                self._toggleSectionCollapse(key);
            });
        });
        var labels = this._container.querySelectorAll('.fogos-panel__item label');
        Array.prototype.forEach.call(labels, function (el) {
            L.DomEvent.on(el, 'click', function (e) {
                L.DomEvent.stop(e);
                var section = el.closest('[data-section]').getAttribute('data-section');
                var id = el.getAttribute('data-id');
                self._toggleItem(section, id);
            });
        });
    },

    _escape: function (s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    },

    _load: function () {
        try {
            if (typeof store !== 'undefined' && store.get) {
                return store.get('fogos:map-panel') || {};
            }
            var raw = localStorage.getItem('fogos:map-panel');
            return raw ? JSON.parse(raw) : {};
        } catch (e) {
            return {};
        }
    },

    _save: function () {
        try {
            if (typeof store !== 'undefined' && store.set) {
                store.set('fogos:map-panel', this._state);
            } else {
                localStorage.setItem('fogos:map-panel', JSON.stringify(this._state));
            }
        } catch (e) {}
    }
});

L.control.fogosPanel = function (options) {
    return new L.Control.FogosPanel(options);
};
