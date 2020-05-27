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
                @lang('emails.your_referral_code_is',['appname' => config('app.name'), 'referral_code' => $user->referral_code],$user->lang) <Br> <br>
                <Br>
                @lang('emails.referral_code_desc',[],$user->lang)

                <table>
                    <thead>
                    <tr>
                        <th>@lang('keywords.title',[],$user->lang)</th>
                        <th>@lang('keywords.referrals',[],$user->lang)</th>
                        <th>@lang('keywords.pay_per_loan_start',[],$user->lang)</th>
                        <th>@lang('keywords.pay_per_loan_pif',[],$user->lang)</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($benefits as $key=>$value)
                        <tr>
                            <td>{!! $value->title !!}</td>
                            <td>{!! $value->min_referrals !!} - {!! $value->max_referrals !!}</td>
                            <td>{!! $value->loan_start !!} @lang('keywords.colones',[],$user->lang)</td>
                            <td>{!! $value->loan_pif !!} @lang('keywords.colones',[],$user->lang)</td>
                        </tr>
                    @endforeach
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
