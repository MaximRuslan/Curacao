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
                @lang('emails.web_loan_reason',['loan_id'=>$loan->id,'reason'=>$loan->reason,'status'=>$status],$user->lang)
                <Br><br>
                @lang('emails.web_loan_content',[],$user->lang)<br>
                <ul>
                    <li>@lang('emails.web_valid_id',[],$user->lang)</li>
                    <li>@lang('emails.web_proof_salary',[],$user->lang)</li>
                    <li>@lang('emails.web_paystubs',[],$user->lang)</li>
                    <li>@lang('emails.web_proof_address',[],$user->lang)</li>
                </ul>
            </td>
        </tr>
        {{--<tr>
            <td colspan="2" style="padding:10px;color:#666666">Thanks,<br/>-Team {!! config('app.name') !!}</td>
        </tr>--}}
        <tr>
            <td colspan="2" style="padding:10px;font-size: 12px;;color:#BDBDBD; background: #3C464F">
                @lang('emails.web_footer',['email'=>config('site.footer_mail_email_web')],$user->lang)<br/><br/>
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
