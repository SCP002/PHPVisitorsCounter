NS_COUNTER = {};

NS_COUNTER.modalSelector = '#modal-counter';
NS_COUNTER.bodyElement = $('body');

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
        $('#table-counter-now').bootstrapTable('load', response['now']['users']);
        $('#table-counter-daily').bootstrapTable('load', response['daily']['users']);

        $(NS_COUNTER.modalSelector).modal();
    } else {
        $('span.visitors-now').html('Now: ' + response['now']);
        $('span.visitors-daily').html('Daily: ' + response['daily']);
        $('span.visitors-total').html('Total: ' + response['total']);
    }
};

/**
 * @param getFullData If false, just get visitors count as numbers.
 *                    If true, then ask for a password and send it to the server.
 *                    Then, if password is passed, get response (object) with all counter file contents.
 */
NS_COUNTER.requestCounterData = function (getFullData) {
    var password = null;

    if (getFullData) {
        password = window.sessionStorage.getItem('counterPassword');

        if (!password) {
            password = window.prompt('Enter password');
        }
    }

    $.ajax({
        url: './php/counter-ajax.php',
        method: 'POST',
        dataType: 'JSON',
        timeout: 10000,
        data: {
            'clientId': NS_COUNTER.getClientId(),
            'sessionId': NS_COUNTER.getSessionId(),
            'lastUrl': window.location.href,
            'password': password
        },
        success: function (response) {
            NS_COUNTER.displayCounterData(response);

            if (typeof (response['now']) === 'object') { // If password passed.
                window.sessionStorage.setItem('counterPassword', password);
            }
        },
        error: function (jqXHR) {
            window.console.error('Counter data request failed: ' + jqXHR.statusText);
            window.console.error('Response text: ' + jqXHR.responseText);
        }
    });
};

NS_COUNTER.urlFormatter = function (value) {
    return '<a href="' + value + '">' + value + '</a>';
};

NS_COUNTER.timeFormatter = function (value) {
    return new Date(value * 1000).toLocaleTimeString('en-GB');
};

NS_COUNTER.rowStyle = function (row) {
    if (row['clientId'] === NS_COUNTER.getClientId()) {
        return {
            classes: ['info']
        };
    }

    return {};
};


NS_COUNTER.bodyElement.on('dblclick', 'div.counter', function () {
    NS_COUNTER.requestCounterData(true);
});

NS_COUNTER.bodyElement.on('click', '#btn-counter-refresh', function () {
    NS_COUNTER.requestCounterData(true);
});

NS_COUNTER.bodyElement.on('show.bs.modal', NS_COUNTER.modalSelector, function () {
    $(this).find('.modal-body').css({
        'overflow-y': 'auto',
        'max-height': $(window).height() * 0.75
    });
});
