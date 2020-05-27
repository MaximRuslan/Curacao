var loanView = {
    el: {
        deleteNote: '.deleteNote',
        deleteNoteModal: '#deleteNoteModal',
        confirmDeleteNoteButton: '.confirmDeleteNoteButton',
    },
    data: {
        loan_id: "",
        client_id: "",
        admin: "",
    },
    init() {
        var _this = this;
        _this.data.loan_id = window.loan_id;
        _this.data.client_id = window.client_id;
        _this.data.admin = window.admin;
        datePickerInit();
        _this.getNotes(_this.data.loan_id);
        _this.bindUiActions();
    },
    bindUiActions() {
        var _this = this;

        oTable = $('#loan-history-table').DataTable({
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: {
                "url": adminAjaxURL + 'datatable-loans',
                data: function (d) {
                    d.user_id = _this.data.client_id;
                    d.not_id = _this.data.loan_id;
                    d.history = 1;
                }
            },
            columns: [
                {data: 'id', name: 'loan_applications.id'},
                {data: 'user_id_number', name: 'users.id', visible: false},
                {data: 'reason_title', name: 'loan_reasons.title'},
                {data: 'amount', name: 'loan_applications.amount'},
                {data: 'loan_type_title', name: 'loan_types.title'},
                {data: 'loan_status_title', name: 'loan_status.title'},
                {data: 'created_at', name: 'created_at', searchable: false},
                {data: 'start_date', name: 'start_date', searchable: false},
                {data: 'end_date', name: 'end_date', searchable: false},
            ],
            order: [[0, 'desc']],
            pageLength: '50'
        });
        transactionDatatable = $('#loan-transaction-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'loans/' + loan_id + '/transactions',
            },
            columns: [
                {data: 'id', name: 'loan_transactions.id', visible: false},
                {data: 'loan_id', name: 'loan_transactions.loan_id'},
                {data: 'created_user_name', name: 'users.firstname'},
                {data: 'created_user_name', name: 'users.lastname', visible: false},
                {data: 'transaction_type_name', name: 'transaction_types.title'},
                {data: 'payment_type', name: 'loan_transactions.type', searchable: false},
                {data: 'notes', name: 'notes'},
                {data: 'amount', name: 'amount'},
                {data: 'cash_back_amount', name: 'cash_back_amount'},
                {data: 'payment_date', name: 'loan_transactions.payment_date'},
                {data: 'created_at', name: 'loan_transactions.created_at'},
            ],
            'order': [[0, 'desc']],
            "pageLength": 50
        });
        notesDatatable = $('#notes-datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                "url": adminAjaxURL + 'loans/' + loan_id + '/datatable-notes',
            },
            columns: [
                {data: 'id', name: 'id', visible: false},
                {data: 'date', name: 'date'},
                {data: 'follow_up', name: 'follow_up'},
                {data: 'user_name', name: 'users.firstname'},
                {data: 'user_name', name: 'users.lastname', visible: false},
                {data: 'details', name: 'details'},
                {data: 'action', name: 'action', visible: _this.data.admin},
            ],
            'order': [[0, 'desc']],
            "pageLength": 50
        });

        $(document).on('click', ".more", function () {
            if ($(this).data('type') == 'more') {
                $(this).data('type', 'less');
                $(this).text("less..").siblings(".complete").show();
            } else {
                $(this).data('type', 'more');
                $(this).text("more..").siblings(".complete").hide();
            }
        });

        $(document).on('click', '.addNewFollowup', function (e) {
            e.preventDefault();
            $('#addNewFollowupModal').find('#add_new_note_form')[0].reset();
            $('#addNewFollowupModal').find('[name="id"]').val('');
            $('#addNewFollowupModal').find('.error').html('');
            $('#addNewFollowupModal').find('.modal-title').html('Add Note');
            $('#addNewFollowupModal').find('input .error').removeClass('error');
            $('#addNewFollowupModal').modal('show');
        });
        $(document).on('click', '.editNote', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'get',
                url: adminAjaxURL + 'loans/' + _this.data.loan_id + '/notes/' + $(this).data('id'),
                success: function (data) {
                    setForm('#add_new_note_form', data.inputs);
                    $('#addNewFollowupModal').find('.error').html('');
                    $('#addNewFollowupModal').find('.modal-title').html('Edit Note');
                    $('#addNewFollowupModal').find('input .error').removeClass('error');
                    $('#addNewFollowupModal').modal('show');
                    $('#allNotesModal').modal('hide');
                }
            })
        });
        $(document).on('submit', '#add_new_note_form', function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'post',
                url: adminAjaxURL + 'loans/' + _this.data.loan_id + '/notes',
                data: $(this).serialize(),
                success: function (data) {
                    _this.getNotes(_this.data.loan_id);
                    $('#addNewFollowupModal').modal('hide');
                    notesDatatable.draw();
                },
                error: function (jqXHR) {
                    var Response = jqXHR.responseText;
                    Response = $.parseJSON(Response);
                    displayErrorMessages(Response, $('#add_new_note_form'), 'input');
                }
            });
        });

        $(document).on('click', _this.el.deleteNote, function (e) {
            e.preventDefault();
            $(_this.el.deleteNoteModal).find(_this.el.confirmDeleteNoteButton).data('id', $(this).data('id'));
            $(_this.el.deleteNoteModal).modal('show');
        });

        $(document).on('click', _this.el.deleteNoteModal + ' ' + _this.el.confirmDeleteNoteButton, function (e) {
            e.preventDefault();
            $.ajax({
                dataType: 'json',
                method: 'delete',
                url: adminAjaxURL + 'loans/' + _this.data.loan_id + '/notes/' + $(this).data('id'),
                success: function (data) {
                    _this.getNotes(_this.data.loan_id);
                    $(_this.el.deleteNoteModal).modal('hide');
                    notesDatatable.draw();
                }
            });
        });
        $(document).on('click', '.allNotes', function (e) {
            e.preventDefault();

        });
    },
    getNotes(loan_id) {
        $.ajax({
            dataType: 'json',
            method: 'get',
            url: adminAjaxURL + 'loans/' + loan_id + '/notes',
            success: function (data) {
                var str = '';
                for (var index in data['notes']) {
                    var note = data['notes'][index];
                    if (note['details'] == null) {
                        note['details'] = '';
                    }
                    str += '<tr>' +
                        '<td>' + note['date'] + '</td>' +
                        '<td>' + note['follow_up'] + '</td>' +
                        '<td>' + note['user_name'] + '</td>' +
                        '<td>' + note['details'] + '</td>';
                    if (admin == '1') {
                        str += '<td>' +
                            '<button class="btn btn-primary editNote" title="Edit" data-id="' + note['id'] + '"><i class="fa fa-pencil"></i></button>' +
                            '<button class="btn btn-danger deleteNote" title="Delete" data-id="' + note['id'] + '"><i class="fa fa-trash"></i></button>' +
                            '</td>';
                    }
                    str += '</tr>';
                }
                $('#notes_tbody').html(str);
            }
        })
    }

};