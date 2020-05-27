<html>
<body>
<div style="width:600px; border:15px #53966d solid;margin:0 auto;font-family: sans-serif; color: #000AAA;">
    <table border="0" cellspacing="0" style="font-size: 14px;width:600px">
        <tr style="background: #ffffff;">
            <td align="center" colspan="2" style="padding:10px">
                <img src="{!! \App\Library\Helper::getCountryLogo('user'); !!}" style="max-width: 100%;height: 200px;background: #ffffff;">
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding:10px;color:#666666"><b>@lang('emails.Welcome',[],$user->lang),</b></td>
        </tr>
        <tr>
            <td colspan="2" style="padding:10px;color:#666666">
                @lang('emails.verify_by_click',[],$user->lang) <Br> <br>
                @if(isset($password) && $password!=false && $password!='')
                    @lang('emails.account_password',['password'=>$password],$user->lang) <Br> <br>
                @endif
                <a href="{{route('verify.client', ['id' => encrypt($user->id),'info'=>encrypt($id),'email'=>$email])}}" class="btn-primary" itemprop="url"
                   style="font-family: 'Helvetica Neue',Helvetica,Arial,sans-serif; box-sizing: border-box; font-size: 14px; color: #FFF; text-decoration: none; line-height: 2em; font-weight: bold; text-align: center; cursor: pointer; display: inline-block; border-radius: 5px; text-transform: capitalize; background-color: #5fbeaa; margin: 0; border-color: #5fbeaa; border-style: solid; border-width: 10px 20px;">
                    @lang('emails.confirm_email',[],$user->lang)
                </a>
                <Br><br>
            </td>
        </tr>
        {{--<tr>
            <td colspan="2" style="padding:10px;color:#666666">Thanks,<br/>-Team {!! config('app.name') !!}</td>
        </tr>--}}
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
