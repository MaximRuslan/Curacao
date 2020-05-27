<div id="loanApplicationModal" class="modal fade" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg" style="max-width: 1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title mt-0">@lang('keywords.Apply for loan')</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
                @include('client1.partials.loans_form')
            </div>
        </div>
    </div>
</div>
