$(document).ready(function () {
    toggleNotification();

    // TODO read localstorage and set proper state on toggles

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
});

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
        console.log('Notification permission granted.');
        console.log(messaging);

        messaging.getToken().then(function(currentToken) {
            if (currentToken) {
                store.set('notificationsAuth', true);
                store.set('token', currentToken);
                $('.no-auth').hide();
                $('.auth').show();
                sendEvent('notifications', 'allowed');
            } else {
                alert('Upps, Ocorreu um erro! Tente mais tarde. 1');
            }
        }).catch(function(err) {
            alert('Upps, Ocorreu um erro! Tente mais tarde. 2');
        });
    }).catch(function(err) {
        console.log('Unable to get permission to notify.', err);
    });
}

function toggleNotification() {
    $(".custom-control-input").click(function () {
        if ($(this).is(':checked')) {
            if ($(this).data("type") == "site") {

                const url = '/notifications/subscribe';

                const topic =  $(this).data('value');
                const data   = {
                    'token' : store.get('token'),
                    'topic' : topic
                };

                $.ajax({
                    type: "POST",
                    url: url,
                    data: data,
                    success: function(e){
                        toastr.success('Registado com sucesso');
                        store.set($(this).data('value'), true);
                        sendEvent('notifications', 'subscribed', topic );
                    },
                });

                console.log("registar no browser")
            } else {
                console.log("register por sms")
            }
        } else {
            //todo this! :)
            console.log("remover registo");
        }
    });
}