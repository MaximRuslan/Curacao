const cockpit = {
    el: {},
    data: {
        datatable: ''
    },
    init() {
        let _this = this;
        _this.bindUiActions();
        datePickerInit();
        _this.getData();
        $('#js--user').select2();
    },
    bindUiActions() {
        let _this = this;
        $(document).on('click', '.js--search', function (e) {
            e.preventDefault();
            _this.getData();
        });
        $(document).on('change', '#start_date_below', function () {
            $('#end_date_below').datepicker('setStartDate', $(this).val());
        });
        $(document).on('change', '#end_date_below', function () {
            $('#start_date_below').datepicker('setEndDate', $(this).val());
        });
    },
    getData() {
        let _this = this;
        let start = $('#start_date_below').val();
        let end = $('#end_date_below').val();
        $.ajax({
            dataType: 'json',
            method: 'post',
            url: adminAjaxURL + 'cockpit',
            data: {
                start: start,
                end: end,
                user: $('#js--user').val()
            },
            success(data) {
                let str = ``;
                for (const employee of data.employees) {
                    str += `
                        <tr>
                            <td>${employee.name}</td>
                            <td>${employee.role}</td>
                            <td>${employee.current}</td>
                            <td>${employee.in_default}</td>
                            <td>${employee.debt_collector}</td>
                            <td>${employee.principal}</td>
                            <td>${employee.fees}</td>
                        </tr>
                    `;
                }
                $('#js--cockpit-tbody').html(str);
                let foot = `
                    <tr>
                        <td><b><a href="${adminURL}loan-applications/to-assign">Unassigned</a></b></td>
                        <td></td>
                        <td>${data.unassigned.current}</td>
                        <td>${data.unassigned.in_default}</td>
                        <td>${data.unassigned.debt_collector}</td>
                        <td>${data.unassigned.principal}</td>
                        <td>${data.unassigned.fees}</td>
                    </tr>
                    <tr>
                        <td><b>Total</b></td>
                        <td></td>
                        <td>${data.total.current}</td>
                        <td>${data.total.in_default}</td>
                        <td>${data.total.debt_collector}</td>
                        <td>${data.total.principal}</td>
                        <td>${data.total.fees}</td>
                    </tr>
                `;
                $('#js--cockpit-tfoot').html(foot);
                if (_this.data.datatable == '') {
                    _this.data.datatable = $('#datatable').dataTable({
                        paginate: false,
                        searching: false
                    });
                }
            }
        });
    }
};
cockpit.init();