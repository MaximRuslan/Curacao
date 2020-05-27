<div id="loanReasonsModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <form id="loan_reason_form">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title mt-0">Loan reason</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('title','Title *')!!}
                                {!!Form::text('title', old('title'), ['class'=>'form-control','placeholder'=>'Title'])!!}
                                <span class="error" for="title"></span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('title_es','Title ESP *')!!}
                                {!!Form::text('title_es', old('title_es'), ['class'=>'form-control','placeholder'=>'Title ESP'])!!}
                                <span class="error" for="title_es"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!!Form::label('title_nl','Title PAP *')!!}
                                {!!Form::text('title_nl', old('title_nl'), ['class'=>'form-control','placeholder'=>'Title PAP'])!!}
                                <span class="error" for="title_nl"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="id">
                    <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info waves-effect waves-light">Save changes</button>
                </div>
            </div>
        </form>
    </div>
</div>
