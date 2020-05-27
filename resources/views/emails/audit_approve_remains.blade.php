<html>
<body>
<div style="width:600px; border:15px #53966d solid;margin:0 auto;font-family: sans-serif; color: #000AAA;">
    <table border="0" cellspacing="0" style="font-size: 14px;width:600px">
        <tr style="background: #ffffff;">
            <td align="center" colspan="2" style="padding:10px">
                <img src="{!! \App\Library\Helper::getCountryLogo($user->country); !!}" style="max-width: 100%;height: 200px;background: #ffffff;">
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:10px;color:#666666">
                <b>@lang('emails.Hello',[],$user->lang) {!! ucwords(strtolower($user->firstname." ".$user->lastname)) !!},</b>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:10px;color:#666666">
                @lang('emails.audit_approve_remains_pending',[],$user->lang)
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:10px;color:#666666">
                <table>
                    <thead>
                    <tr>
                        <th>@lang('emails.Date',[],$user->lang)</th>
                        <th>@lang('emails.User',[],$user->lang)</th>
                        <th>@lang('emails.Branch',[],$user->lang)</th>
                        <th>@lang('emails.Country',[],$user->lang)</th>
                        <th>@lang('emails.Amount',[],$user->lang)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if(count($data)>0)
                        @foreach($data as $key=>$value)
                            <tr>
                                <td>{!! \App\Library\Helper::datebaseToFrontDate($value->date) !!}</td>
                                <td>{!! $value->user_name !!}</td>
                                <td>{!! $value->branch_name !!}</td>
                                <td>{!! $value->country_name !!}</td>
                                <td>{!! $value->total_amount !!}</td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">@lang('emails.no_data_found',[],$user->lang)</td>
                        </tr>
                    @endif
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:10px;font-size: 12px;;color:#BDBDBD; background: #3C464F">
                @lang('emails.receiving_email_because',['appname'=>config('app.name')],$user->lang)<br/>
                @lang('emails.noreply',[],$user->lang)<br/><br/>
                @lang('emails.copyrights',['appname'=>config('app.name')],$user->lang)<br/>
                @lang('emails.rights_reserved',[],$user->lang)
            </td>
        </tr>
    </table>
</div>
</body>
</html>
