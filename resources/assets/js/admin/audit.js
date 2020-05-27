var audit = {
    el: {
        modal: '#auditReportView',
    },
    data: {
        datatable: '',
        payment_types: '',
        type: '',
    },
    init() {
        var _this = this;
        _this.data.payment_types = window.payment_types;
        _this.data.type = window.type;
        _this.bindUiActions();
    },
    bindUiActions() {
        var _this = this;
        _this.data.datatable = $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'daily-turnover/datatable-audit-report',
                data: function (d) {
                    d.branch_id = $('#branch_id').val();
                    d.type = _this.data.type;
                }
            },
            columns: [
                {data: 'date', name: 'dayopens.date', visible: false},
                {data: 'username', name: 'users.firstname', searchable: false},
                {data: 'username', name: 'users.lastname', searchable: false, visible: false},
                {data: 'branch_name', name: 'branches.title', searchable: false},
                {data: 'date', name: 'dayopens.date', searchable: false},
                {data: 'completion_date', name: 'dayopens.completion_date', searchable: false},
                {data: 'verified_by_username', name: 'dayopens.completion_date', searchable: false},
                {data: 'status', name: 'status', searchable: false, orderable: false},
                {data: 'custom_created_at', name: 'dayopens.custom_created_at', searchable: false},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ],
            order: [[0, 'desc']],
            drawCallback(settings) {
                initTooltip();
            },
        });
        $(document).on('change', '#branch_id', function (e) {
            e.preventDefault();
            _this.data.datatable.draw();
        });
        $(document).on('click', '.viewAuditReport', function (e) {
            e.preventDefault();
            var date = $(this).data('date');
            var branch = $(this).data('branch');
            var user = $(this).data('user');
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'daily-turnover/audit-report/' + date + '/' + user + '/' + branch,
                data: {
                    type: _this.data.type,
                },
                success: function (data) {
                    if (data['end_date'] != '') {
                        $('#auditReportView').find('#date_title').html('<b style="font-weight: bold;">' + data['date'] + ' To ' + data['end_date'] + '</b>');
                    } else {
                        $('#auditReportView').find('#date_title').html('<b style="font-weight: bold;">' + data['date'] + ' To ' + data['date'] + '</b>');
                    }
                    $('#auditReportView').find('#total_day_in').text(data['total_in']);
                    $('#auditReportView').find('#total_day_out').text(data['total_out']);
                    $('#auditReportView').find('#total_dayopen_sum').text(data['dayopen_sum']);
                    $('#auditReportView').find('#total_next_dayopen_sum').text(data['next_dayopen_sum']);
                    $('#auditReportView').find('#total_diff').text(data['total_difference']);
                    $('#auditReportView').find('.approveTodayReport').data('date', date);
                    $('#auditReportView').find('.approveTodayReport').data('branch', branch);
                    $('#auditReportView').find('.approveTodayReport').data('user', user);
                    if (data['branch'] != undefined) {
                        $('#auditReportView').find('#branch_name_audit').html(' - ' + data['branch']['title']);
                    }

                    var str = '';
                    for (var index in payment_types) {
                        if (data['dayopens'][index] == undefined) {
                            data['dayopens'][index] = '0.00';
                        }
                        if (data['next_date_dayopens'][index] == undefined) {
                            data['next_date_dayopens'][index] = '0.00';
                        }
                        if (data['difference'][index] == null) {
                            data['difference'][index] = '0.00';
                        }
                        str += '<tr>';

                        str += '<td>';
                        str += payment_types[index];
                        str += '</td>';

                        str += '<td>';
                        str += data['dayopens'][index];
                        str += '</td>';

                        str += '<td>';
                        str += data['in_amount'][index];
                        str += '</td>';

                        str += '<td>';
                        str += data['out_amount'][index];
                        str += '</td>';

                        str += '<td>';
                        str += data['next_date_dayopens'][index];
                        str += '</td>';

                        str += '<td>';
                        str += data['difference'][index];
                        str += '</td>';

                        str += '</tr>';
                    }

                    $('#auditReportView').find('#auditReportTbody').html(str);
                    if (data['approved'] == true || data['is_eligible'] != true) {
                        $('.approveTodayReport').hide();
                    } else {
                        $('.approveTodayReport').show();
                    }
                    $('#auditReportView').modal('show');
                }
            })
        });
        $(document).on('click', '.approveTodayReport', function (e) {
            e.preventDefault();
            if (confirm('Are you sure you want to approve this report?')) {
                $.ajax({
                    dataType: 'json',
                    method: 'post',
                    url: adminAjaxURL + 'daily-turnover/audit-report/' + $('.approveTodayReport').data('date') + '/' + $('.approveTodayReport').data('user') + '/' + $('.approveTodayReport').data('branch') + '/approve',
                    data: {
                        type: _this.data.type,
                    },
                    success: function (data) {
                        $('.approveTodayReport').hide();
                        _this.data.datatable.draw();
                    }
                });
            }
        });
    },
};