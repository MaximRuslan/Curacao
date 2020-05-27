@extends('admin.layouts.app')

@section('extra-styles')
@endsection


@section('content')
    <div class="row">
        <div class="col-sm-12">
            <h4 class="page-title">{!! $cms->name !!}</h4>
        </div>
    </div>
    <div class="card-box row">
        <div class="col-12">
            {!!Form::open(['route'=>['cms.update',$cms->type]])!!}
            <div class="form-group">
                {!!Form::label('value','HTML')!!}
                {!!Form::textarea('value', $cms->value, ['class'=>'form-control','placeholder'=>'HTML','id'=>'cms_textarea','style'=>'height:500px;'])!!}
                @if($errors->has('value'))
                    <p class="help-block">{!!$errors->first('value')!!}</p>
                @endif
            </div>
            <div class="form-group">
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
            {!!Form::close()!!}
        </div>
    </div>
@endsection

@section('extra-js')
    <script src="{!! asset('admin/js/plugins/tinymce.min.js') !!}"></script>
@endsection

@section('custom-js')
    <script>
        if ($("#cms_textarea").length > 0) {
            tinymce.init({
                selector: "textarea#cms_textarea",
                theme: "modern",
                height: 300,
                plugins: [
                    "advlist autolink link lists charmap print preview hr anchor pagebreak spellchecker",
                    "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime nonbreaking",
                    "save table contextmenu directionality emoticons template paste textcolor"
                ],
                toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | l      ink | print preview fullpage | forecolor backcolor emoticons",
                style_formats: [
                    {title: 'Bold text', inline: 'b'},
                    {title: 'Red text', inline: 'span', styles: {color: '#ff0000'}},
                    {title: 'Red header', block: 'h1', styles: {color: '#ff0000'}},
                    {title: 'Example 1', inline: 'span', classes: 'example1'},
                    {title: 'Example 2', inline: 'span', classes: 'example2'},
                    {title: 'Table styles'},
                    {title: 'Table row 1', selector: 'tr', classes: 'tablerow1'}
                ]
            });
        }
    </script>
@endsection
