<script type="text/javascript" src="{!! asset(mix('resources/js/client/app.js')) !!}"></script>
<script>
    var active_loan_error = '@lang('validation.active_loan')';
</script>
<script type="text/javascript">
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    profile.init();
</script>
<script>
    $(document).on('click', '.js--has-active-loan', function (e) {
        e.preventDefault();
        @if(auth()->check() && \App\Models\LoanApplication::hasActiveLoan(auth()->user()))
        swal({
            type: 'warning',
            title: active_loan_error,
        })
        @else
            window.location.href="{!! route('client1.loans.create') !!}";
        @endif
    });
</script>

