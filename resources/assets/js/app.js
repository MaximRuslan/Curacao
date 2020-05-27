import Echo from "laravel-echo";

window.io = require('socket.io-client');
try {
    window.Echo = new Echo({
        broadcaster: 'socket.io',
        host: window.location.hostname + ':6001',
        reconnectionAttempts: 5
    });

    // window.Echo.channel('survey').listen('MessagePushed', (e) => {
    //     console.log('sakdbsd');
    //     console.log(e);
    //     console.log(this);
    // });

    // window.Echo.channel('TimeInfo').listen('TimeReceive', (e) => {
    //     console.log(e);
    // });

    if (window.user_id != undefined && window.branch_id != undefined) {
        window.Echo.channel('LateCashPayApprovalChannel.' + window.user_id).listen('LateCashPayApproval', (data) => {
            if (data['branch_id'] == window.branch) {
                $('#late_cash_payout_approval_modal').modal('toggle');
            }
        });
    }
    window.Echo.channel('message_datatable_refresh').listen('MessageRefresh', (data) => {
        console.log('sajkdsadsad');
        if ($('#message-datatable').length > 0) {
            messages.data.datatable.draw(false);
        }
    });
} catch (e) {
    console.log(e);
}