<html>
<body>
<div style="width:600px; border:15px #53966d solid;margin:0 auto;font-family: sans-serif; color: #000AAA;">
    <table border="0" cellspacing="0" style="font-size: 14px;width:100%;">
        <tr style="background: #ffffff;">
            <td align="center" colspan="2" style="padding:10px">
                <img src="{!! \App\Library\Helper::getCountryLogo('user'); !!}" style="max-width: 100%;height: 200px;background: #ffffff;">
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:10px;color:#666666">
                <b>@lang('emails.Dear',[],$user->lang) {!! ucwords(strtolower($user->firstname." ".$user->lastname)) !!},</b>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:10px;color:#666666">
                @lang('emails.reminder_raffle_tomorrow',['appname' => config('app.name')],$user->lang) <Br> <br>
                <Br>
                <Br>
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
