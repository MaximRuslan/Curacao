var bankReconcile = {
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
            pageLength: 50,
            ajax: {
                "url": adminAjaxURL + 'bank-reconciliation-data',
                data: function (d) {
                    d.reconcile_type = $('#reconcile_type').val();
                }
            },
            columns: [
                {data: 'reconcile_select', searchable: false, orderable: false},
                {data: 'date', name: 'date'},
                {data: 'fullname', name: 'users.firstname', searchable: false},
                {data: 'fullname', name: 'users.lastname', searchable: false, orderable: false, visible: false},
                {data: 'loan_id', name: 'loan_id', searchable: false},
                {data: 'amount', name: 'amount'},
                {data: 'type', name: 'loan_id', searchable: false, orderable: false},
                {data: 'status', name: 'status', searchable: false, orderable: false},
                {data: 'action', name: 'status', searchable: false, orderable: false},
            ],
            "drawCallback": function (settings) {
                initTooltip();
                $('.custom-buttons').remove();
                var str = '';
                str += '<button class="btn btn-primary reconcile_selected">Reconcile</button>&nbsp;&nbsp;';
                $('.reconcile_selected').remove();
                $('#datatable_filter').prepend(str);
            },
            order: [[1, 'desc']]
        });

        $(document).on('change', '#reconcile_type', function (e) {
            e.preventDefault();
            _this.data.datatable.draw();
            if ($(this).val() == 2) {
                $('#select_all').hide();
                $('#deselect_all').hide();
                $('.reconcile_selected').hide();
            } else {
                $('#select_all').show();
                $('#deselect_all').show();
                $('.reconcile_selected').show();
            }
        });
        $(document).on('change', '#select_all_checkbox', function (e) {
            e.preventDefault();
            if ($('#select_all_checkbox:checked').val() == 1) {
                $('.reconcileCheckbox').prop('checked', true);
            } else {
                $('.reconcileCheckbox').prop('checked', false);
            }
        });
        $(document).on('click', '#select_all', function () {
            $('.reconcileCheckbox').prop('checked', true);
        });
        $(document).on('click', '#deselect_all', function () {
            $('.reconcileCheckbox').prop('checked', false);
        });

        $(document).on('click', '.reconcileBank', function (e) {
            e.preventDefault();
            $('#reconcile_form').find('[name="id"]').val([$(this).data('id')]);
            $('#reconcile_form').find('[name="type"]').val([$(this).data('type')]);
            $('#reconcileModal').modal('show');
        });

        $(document).on('click', '.reconcile_selected', function (e) {
            e.preventDefault();
            var values = [];
            var types = [];
            $('.reconcileCheckbox:checked').each(function (key, item) {
                values.push($(item).val());
                types.push($(item).data('type'));
            });
            $('#reconcile_form').find('[name="id"]').val(values);
            $('#reconcile_form').find('[name="type"]').val(types);
            $('#reconcileModal').modal('show');
        });

        $(document).on('submit', '#reconcile_form', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'bank-reconcile',
                data: $('#reconcile_form').serialize(),
                success: function (data) {
                    _this.data.datatable.draw(false);
                    $('#reconcileModal').modal('hide');
                }
            });
        });
    },
};