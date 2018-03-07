NS_COUNTER = {};

NS_COUNTER.getClientId = function () {
    // Not using window.localStorage due not full IE8 compatibility.
    var clientId = store.get('clientId');

    if (!clientId) {
        clientId = Math.random().toString(36).substr(2, 9);

        store.set('clientId', clientId);
    }

    return clientId;
};

NS_COUNTER.getSessionId = function () {
    var sessionId = window.sessionStorage.getItem('sessionId');

    if (!sessionId) {
        sessionId = Math.random().toString(36).substr(2, 9);

        window.sessionStorage.setItem('sessionId', sessionId);
    }

    return sessionId;
};

NS_COUNTER.displayCounterData = function (response) {
    if (typeof (response['now']) === 'object') {
        // TODO: This.
        window.console.log(response);
    } else {
        $('span.visitors-now').html('Now: ' + response['now']);
        $('span.visitors-daily').html('Daily: ' + response['daily']);
        $('span.visitors-total').html('Total: ' + response['total']);
    }
};

NS_COUNTER.requestCounterData = function (password) {
    $.ajax({
        url: './php/counter-ajax.php',
        method: 'POST',
        dataType: 'JSON',
        timeout: 10000,
        data: {
            'clientId': NS_COUNTER.getClientId(),
            'sessionId': NS_COUNTER.getSessionId(),
            'password': password
        },
        success: function (response) {
            NS_COUNTER.displayCounterData(response);
        },
        error: function (jqXHR) {
            window.console.error('Counter data request failed: ' + jqXHR.statusText);
            window.console.error('Response text: ' + jqXHR.responseText);
        }
    });
};


$('body').on('dblclick', 'div.counter', function () {
    var password = window.prompt('Enter password');

    NS_COUNTER.requestCounterData(password);
});
