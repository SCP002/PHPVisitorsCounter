NS_COUNTER = {};

NS_COUNTER.tableNowSelector = '#table-counter-now';
NS_COUNTER.tableDailySelector = '#table-counter-daily';
NS_COUNTER.refreshSelector = '#btn-counter-refresh';
NS_COUNTER.modalSelector = '#modal-counter';
NS_COUNTER.divSelector = 'div.counter';
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

NS_COUNTER.highlightClientRows = function (tableSelector) {
    var clientIdCellIndex = null;

    $(tableSelector).find('thead tr th').each(function () {
        if (this.dataset.field === 'clientId') {
            clientIdCellIndex = this.cellIndex;

            return false;
        }
    });

    $(tableSelector).find('tbody tr td').each(function () {
        if (this.cellIndex === clientIdCellIndex && this.innerHTML === NS_COUNTER.getClientId()) {
            $(this).parent().css('background-color', '#dcf4ff');
        }
    });
};

NS_COUNTER.unixTimeToFormatStr = function (users, fields) {
    users.forEach(function (user, index) {
        fields.forEach(function (field) {
            var date = new Date(user[field] * 1000);

            users[index][field] = date.toLocaleTimeString('en-GB');
        });
    });

    return users;
};

NS_COUNTER.displayCounterData = function (response) {
    if (typeof (response['now']) === 'object') {
        // Convert unix timestamps to readable form.
        var nowUsers = NS_COUNTER.unixTimeToFormatStr(response['now']['users'], ['expires', 'visitTime']);
        var dailyUsers = NS_COUNTER.unixTimeToFormatStr(response['daily']['users'], ['visitTime']);

        // Load data to tables.
        $(NS_COUNTER.tableNowSelector).bootstrapTable('load', nowUsers);
        $(NS_COUNTER.tableDailySelector).bootstrapTable('load', dailyUsers);

        // Highlight rows with current client ID.
        NS_COUNTER.highlightClientRows(NS_COUNTER.tableNowSelector);
        NS_COUNTER.highlightClientRows(NS_COUNTER.tableDailySelector);

        // Show modal.
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


NS_COUNTER.bodyElement.on('dblclick', NS_COUNTER.divSelector, function () {
    NS_COUNTER.requestCounterData(true);
});

NS_COUNTER.bodyElement.on('click', NS_COUNTER.refreshSelector, function () {
    NS_COUNTER.requestCounterData(true);
});

NS_COUNTER.bodyElement.on('show.bs.modal', NS_COUNTER.modalSelector, function () {
    $(this).find('.modal-body').css({
        'overflow-y': 'auto',
        'max-height': $(window).height() * 0.75
    });
});

NS_COUNTER.bodyElement.on('search.bs.table', NS_COUNTER.modalSelector + ' table', function () {
    NS_COUNTER.highlightClientRows('#' + this.id);
});
