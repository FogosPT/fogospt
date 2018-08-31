$(document).ready(function () {

    if(!window.PushManager || document.documentMode || /Edge/.test(navigator.userAgent)){
        $('.is-supported').hide();
        $('.is-not-supported').show();
    } else {
        $('.is-supported').show();
        $('.is-not-supported').hide();

        toggleNotification();
        setNotificationToggles();

        $('.js-notifications-auth').on('click', requestAuth);

        const messaging = firebase.messaging();

        messaging.onMessage(function(payload) {
            toastr.warning(payload.notification.body);
        });

        notificationsAuth = store.get('notificationsAuth');
        if(notificationsAuth){
            $('.no-auth').hide();
            $('.auth').show();
        }
    }

});

function setNotificationToggles() {
    let list = [
        'important',
        'alerts',
        'Aveiro',
        'Beja',
        'Braga',
        'Braganca',
        'CasteloBranco',
        'Coimbra',
        'Evora',
        'Faro',
        'Guarda',
        'Leiria',
        'Lisboa',
        'Portalegre',
        'Porto',
        'Santarem',
        'Setubal',
        'VianadoCastelo',
        'VilaReal',
        'Viseu',
        'Madeira',
    ];

    for( i in list){
        if(store.get(list[i])){
            console.log(list[i]);
            console.log($('checkbox[data-value="' + list[i] + '"]'));
            $('input[data-value="' + list[i] + '"]').prop('checked', true);
        }
    }

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
    const messaging = firebase.messaging();

    sendEvent('notifications', 'allow');
    messaging.requestPermission().then(function() {
        messaging.getToken().then(function(currentToken) {
            if (currentToken) {
                store.set('notificationsAuth', true);
                store.set('token', currentToken);
                $('.no-auth').hide();
                $('.auth').show();
                sendEvent('notifications', 'allowed');
            } else {
                toastr.error('Upps, Ocorreu um erro! Tente mais tarde. 1');
            }
        }).catch(function(err) {
            toastr.error('Upps, Ocorreu um erro! Tente mais tarde. 2');
        });
    }).catch(function(err) {
        toastr.error('Upps, Ocorreu um erro! Tente mais tarde. 2');
    });
}

function toggleNotification() {
    $(".custom-control-input").click(function () {
        let $that = $(this);
        if ($that.is(':checked')) {
            if ($that.data("type") == "site") {
                const url = '/notifications/subscribe';

                const topic =  $that.data('value');
                const data   = {
                    'token' : store.get('token'),
                    'topic' : topic
                };

                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    success: function(data){
                        if(data.success){
                            toastr.success('Registado com sucesso');
                            store.set($that.data('value'), true);
                            // sendEvent('notifications', 'subscribed', topic );
                        } else {
                            toastr.error('Ocorreu um erro');
                            store.set($that.data('value'), false);
                            // sendEvent('notifications', 'subscribed error', topic );
                        }
                    },
                });
                console.log("registar no browser");
            } else {
                console.log("register someday...");
            }
        } else {
            //todo this! :)
            console.log("remover registo");
            if ($that.data("type") == "site") {
                const url = '/notifications/unsubscribe';

                const topic =  $that.data('value');
                const data   = {
                    'token' : store.get('token'),
                    'topic' : topic
                };

                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    success: function(data){
                        if(data.success){
                            toastr.success('Removido com sucesso');
                            store.set($that.data('value'), false);
                            sendEvent('notifications', 'unsubscribed', topic );
                        } else {
                            toastr.error('Ocorreu um erro');
                            sendEvent('notifications', 'unsubscribed error', topic );
                        }
                    },
                });
            } else {
                console.log("unregister someday...");
            }
        }
    });
}