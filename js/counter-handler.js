NS_COUNTER = {};

NS_COUNTER.modalSelector = '#modal-counter';

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
        $('#table-counter-now')
            .bootstrapTable()
            .bootstrapTable('load', response['now']['users']);

        $('#table-counter-daily')
            .bootstrapTable()
            .bootstrapTable('load', response['daily']['users']);

        $(NS_COUNTER.modalSelector).find('.modal-body')
            .css('overflow-y', 'auto')
            .css('max-height', $(window).height() * 0.7);

        $(NS_COUNTER.modalSelector).modal();
    } else {
        $('span.visitors-now').html('Now: ' + response['now']);
        $('span.visitors-daily').html('Daily: ' + response['daily']);
        $('span.visitors-total').html('Total: ' + response['total']);
    }
};

/**
 * @param password Optional parameter. If null, get visitors count.
 *                 Otherwise, if password is passed, get all counter file contents.
 */
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


$('body').on('dblclick', 'div.counter', function () {
    var password = window.sessionStorage.getItem('counterPassword');

    if (!password) {
        password = window.prompt('Enter password');
    }

    NS_COUNTER.requestCounterData(password);
});
