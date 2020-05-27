var raffleWinner = {
    el: {},
    data: {
        datatable: '',
    },
    init() {
        var _this = this;
        _this.bindUiActions();
    },
    bindUiActions() {
        var _this = this;
        _this.data.datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-raffle-winners',
            },
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'date', name: 'date', searchable: false},
                {data: 'name', name: 'users.firstname'},
                {data: 'name', name: 'users.lastname', visible: false},
            ],
            order: [[0, 'desc']],
            drawCallback(settings) {
                initTooltip();
            },
        });

    },
};