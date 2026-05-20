// Public API for other pages (e.g. fire detail) to subscribe to a single
// `incident-<id>` topic without duplicating the AJAX boilerplate.
window.Fogos = window.Fogos || {};
window.Fogos.notifications = window.Fogos.notifications || {};

(function (api) {
    function subscribe(topic, cb) {
        var token = store.get('token');
        if (!token) {
            if (cb) cb(new Error('no-token'));
            return;
        }
        $.ajax({
            type: 'POST',
            url: '/notifications/subscribe',
            data: { token: token, topic: topic },
            success: function (data) {
                if (data && data.success) {
                    store.set(topic, true);
                    if (cb) cb(null, data);
                } else {
                    if (cb) cb(new Error('server'));
                }
            },
            error: function () { if (cb) cb(new Error('http')); }
        });
    }

    function unsubscribe(topic, cb) {
        var token = store.get('token');
        if (!token) {
            if (cb) cb(new Error('no-token'));
            return;
        }
        $.ajax({
            type: 'POST',
            url: '/notifications/unsubscribe',
            data: { token: token, topic: topic },
            success: function (data) {
                if (data && data.success) {
                    store.set(topic, false);
                    if (cb) cb(null, data);
                } else {
                    if (cb) cb(new Error('server'));
                }
            },
            error: function () { if (cb) cb(new Error('http')); }
        });
    }

    function isSubscribed(topic) {
        return !!store.get(topic);
    }

    function hasAuth() {
        return !!store.get('notificationsAuth');
    }

    function requestAuthAsync() {
        return new Promise(function (resolve, reject) {
            var messaging = firebase.messaging();
            var vapidKey = window.__FIREBASE_VAPID_KEY__ || undefined;

            var getToken = function () {
                var opts = vapidKey ? { vapidKey: vapidKey } : undefined;
                messaging.getToken(opts).then(function (token) {
                    if (!token) return reject(new Error('no-token'));
                    store.set('notificationsAuth', true);
                    store.set('token', token);
                    resolve(token);
                }).catch(reject);
            };

            // Modern API: Notification.requestPermission returns a Promise of
            // 'granted' | 'denied' | 'default'. messaging.requestPermission was
            // removed in Firebase 7+.
            var permResult = Notification.requestPermission(function (p) {
                if (p === 'granted') getToken();
                else reject(new Error('permission-denied'));
            });
            if (permResult && typeof permResult.then === 'function') {
                permResult.then(function (p) {
                    if (p === 'granted') getToken();
                    else reject(new Error('permission-denied'));
                }).catch(reject);
            }
        });
    }

    api.subscribe = subscribe;
    api.unsubscribe = unsubscribe;
    api.isSubscribed = isSubscribed;
    api.hasAuth = hasAuth;
    api.requestAuth = requestAuthAsync;
    api.topicForIncident = function (id) { return 'incident-' + id; };
})(window.Fogos.notifications);

$(document).ready(function () {

    if(!window.PushManager || document.documentMode || /Edge/.test(navigator.userAgent)){
        $('.is-supported').hide();
        $('.is-not-supported').show();
    } else {
        $('.is-supported').show();
        $('.is-not-supported').hide();

        toggleNotification();
        setNotificationToggles();
        initConcelhos();

        $('.js-notifications-auth').on('click', requestAuth);

        const messaging = firebase.messaging();

        messaging.onMessage(function(payload) {
            var n = payload && payload.notification;
            if (n && n.body) {
                toastr.warning(n.body, n.title || '');
            }
        });

        notificationsAuth = store.get('notificationsAuth');
        if(notificationsAuth){
            $('.no-auth').hide();
            $('.auth').show();
        }
    }

    $('.js-notifications-reset').on('click', function () {
        localStorage.clear();
        location.reload();
    })

});

function setNotificationToggles() {
    // Restaura o estado de cada checkbox a partir do storage, sem manter
    // uma lista hardcoded — qualquer toggle renderizado pelo Blade é elegível.
    $('input.custom-control-input[data-value]').each(function () {
        var $cb = $(this);
        if (store.get($cb.data('value'))) {
            $cb.prop('checked', true);
        }
    });
}

function sendEvent(category, what, value = null) {
    if (window.ga) {
        if ("ga" in window) {
            var tracker = window.ga.getAll()[0];
            if (tracker)
                tracker.send("event", category, what, "click", value);
        }
    }
}

function requestAuth() {
    sendEvent('notifications', 'allow');
    window.Fogos.notifications.requestAuth().then(function () {
        $('.no-auth').hide();
        $('.auth').show();
        sendEvent('notifications', 'allowed');
    }).catch(function () {
        toastr.error('Upps, Ocorreu um erro! Tente mais tarde.');
    });
}

function toggleNotification() {
    $(document).on('click', '.custom-control-input[data-type="site"]', function () {
        let $that = $(this);
        const topic = $that.data('value');
        if (!topic) return;

        if ($that.is(':checked')) {
            window.Fogos.notifications.subscribe(topic, function (err) {
                if (err) {
                    toastr.error('Ocorreu um erro');
                    $that.prop('checked', false);
                } else {
                    toastr.success('Registado com sucesso');
                }
            });
        } else {
            window.Fogos.notifications.unsubscribe(topic, function (err) {
                if (err) {
                    toastr.error('Ocorreu um erro');
                    $that.prop('checked', true);
                } else {
                    toastr.success('Removido com sucesso');
                    sendEvent('notifications', 'unsubscribed', topic);
                }
            });
        }
    });
}

/* ------------------------------------------------------------------ */
/* Concelhos                                                           */
/* ------------------------------------------------------------------ */

function concelhoTopic(key, allIncidents) {
    // dico.json keys are 6-digit codes ending in "00" → district-<key> or
    // district-all-<key>, matching the unified catalogue.
    return (allIncidents ? 'district-all-' : 'district-') + key;
}

function initConcelhos() {
    var $list = $('.js-concelho-list');
    if ($list.length === 0) return;

    var allIncidents = !!store.get('concelho-all-incidents');
    $('.js-concelho-all-incidents').prop('checked', allIncidents);

    $.getJSON('/js/dico.json').done(function (data) {
        var rows = (data && data.rows) || [];
        rows.sort(function (a, b) {
            return a.value.name.localeCompare(b.value.name, 'pt');
        });
        renderConcelhos(rows, allIncidents);
    }).fail(function () {
        $list.html('<div class="text-danger">Não foi possível carregar a lista de concelhos.</div>');
    });

    $('.js-concelho-filter').on('input', function () {
        var q = normalize($(this).val());
        $list.find('.js-concelho-item').each(function () {
            var name = normalize($(this).data('name'));
            $(this).toggle(q === '' || name.indexOf(q) !== -1);
        });
    });

    $('.js-concelho-all-incidents').on('change', function () {
        var val = $(this).is(':checked');
        store.set('concelho-all-incidents', val);
        // Re-render so checkbox state reflects the chosen variant.
        var rows = [];
        $list.find('.js-concelho-item').each(function () {
            rows.push({ key: $(this).data('key'), value: { name: $(this).data('name') } });
        });
        renderConcelhos(rows, val);
    });
}

function renderConcelhos(rows, allIncidents) {
    var $list = $('.js-concelho-list');
    var html = '';
    rows.forEach(function (row) {
        var key = row.key;
        var name = row.value.name;
        var topic = concelhoTopic(key, allIncidents);
        var checked = store.get(topic) ? 'checked' : '';
        html += '<div class="row justify-content-start js-concelho-item" data-key="' + key + '" data-name="' + escapeAttr(name) + '">' +
                  '<div class="col-sm"><strong>' + escapeHtml(name) + '</strong></div>' +
                  '<div class="col-sm">' +
                    '<label class="custom-control custom-checkbox">' +
                      '<input type="checkbox" class="custom-control-input"' +
                             ' data-value="' + topic + '"' +
                             ' data-type="site" ' + checked + '>' +
                      '<span class="custom-control-indicator"></span>' +
                    '</label>' +
                  '</div>' +
                '</div>';
    });
    $list.html(html);
}

function normalize(s) {
    return (s || '').toString().normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
}

function escapeHtml(s) {
    return (s || '').replace(/[&<>"']/g, function (c) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
    });
}

function escapeAttr(s) { return escapeHtml(s); }
