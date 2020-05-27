<html>

<head>
    <title>PURA VIDA CASH</title>
</head>
<style>
    html, * {
        margin: 0;
    }

    body {
        margin: 0;
        padding: 0;
        background: #525659;
        font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
    }

    page {
        display: block;
        margin: 0px;
        background-color: #fff;
        -webkit-print-color-adjust: exact;
    }

    page[size="A4"] {
        padding: 20px;
        margin: 20px auto;
        max-width: 800px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    .table-header-top tr td {
        vertical-align: top;
    }

    .table-header-top tr td:last-child {
        width: 230px;
    }

    .table-header-top tr td:last-child a {
        display: block;
        text-align: right;
    }

    .company-info {
        font-size: 14px;
        color: #000;
        font-weight: 500;
    }

    .company-info a, .company-info span {
        display: block;
        text-decoration: none;
        margin-top: 5px;
        color: #000;
    }

    .company-info p {
        margin-top: 5px;
    }

    address {
        font-style: normal;
        margin-top: 5px;
        width: 230px;
        font-size: 14px;
        line-height: 16px;
    }

    .content {
        padding-top: 10px;
        padding: 50px;
    }

    .logo-box img {
        height: 100px;
    }

    .company-title {
        font-size: 18px;
        color: #6c8124;
        font-weight: 700;
        text-transform: uppercase;
    }

    .tax-info {
        padding: 0 20px;
    }

    .tax-info label {
        color: #82001d;
        font-weight: 600;
        font-size: 20px;
        display: block;
    }

    .tax-info span {
        color: #111;
        font-weight: 600;
        font-size: 20px;
        display: block;
        font-weight: 700;
    }

    .tax-box + .tax-box {
        margin-top: 15px;
    }

    .header {
        padding-bottom: 20px;
    }

    .client-name {
        text-transform: uppercase;
        display: block;
        margin-top: 5px;
    }

    .client-info tr td {
        max-width: 80%;
        vertical-align: top;
    }

    .client-info tr td:last-child {
        max-width: 20%;
    }

    .date-box {
        margin-top: 5px;
        display: inline-flex;
        align-items: center;
        text-align: center;
    }

    .date-box label {
        font-size: 14px;
        display: block;
    }

    .date-box span {
        font-size: 14px;
        font-weight: 700;
        margin-top: 0px;
        margin-left: 5px;
    }

    .invoice-table {
        border-bottom: 1px solid #000;
    }

    .invoice-table thead tr {
        border-top: 1px solid #000;
        border-bottom: 1px solid #000;
    }

    .invoice-table thead tr th {
        padding: 10px 0;
        font-size: 14px;
        text-align: left;
    }

    .invoice-table thead tr th:last-child {
        text-align: right;
    }

    .invoice-table tbody tr td {
        padding: 5px 0;
        font-size: 14px;
    }

    .invoice-table tbody tr td:last-child {
        text-align: right;
    }

    .invoice-table tbody tr:last-child td {
        padding-bottom: 10px;
    }

    .invoice-footer {
        margin-top: 100px;
        text-align: center;
        font-size: 13px;
        text-transform: uppercase;
        padding-top: 15px;
        border-top: 1px solid #000;
    }

    .invoice-footer a {
        color: #6c8124;
        font-weight: 600;
    }

    .additional-details {
        width: 300px;
    }

    .additional-details label {
        font-size: 14px;
        color: #000;
        font-weight: 700;
        display: block;
    }

    .additional-details span {
        font-size: 14px;
        line-height: 16px;
    }

    .total-block {
        border-top: 1px solid #000;
    }

    tr.total-block td {
        padding-top: 10px !important;
    }

    .invoice-disclaimer {
        margin-top: 50px;
        font-size: 14px;
    }

    .invoice-disclaimer label {
        color: #6c8124;
        font-weight: 600;
    }

    .total-amount {
        font-weight: 600;
    }

    @media print {
        body {
            background: #525659;
            font-family: -apple-system, system-ui, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Open Sans", "Helvetica Neue", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            margin: 0px;
        }

        page {
            display: block;
            margin: 20px auto;
            max-width: 800px;
            background-color: #fff;
            -webkit-print-color-adjust: exact;
        }

        .table-break {
            page-break-before: always;
        }

        tr {
            page-break-inside: avoid
        }

        .content {
            padding: 0px;
            padding-top: 10px;
        }
    }
</style>

<body>
<page size="A4">
    <div class="header">
        <table class="table-header-top">
            <tr>
                <td>
                    <div class="company-info">
                        <label class="company-title">{!! $name !!}</label>
                        <address>
                            {!! $country_name !!}
                        </address>
                        <a href="tel:{!! $telephone !!}">{!! $telephone !!}</a>
                        <a href="{!! $web !!}" target="_blank">{!! $web !!}</a>
                        <div class="date-box">
                            <label>@lang('keywords.Date',[],$lang): </label>
                            <span>{!! $date !!}</span>
                        </div>
                    </div>
                </td>
                <td>
                    <a href="#nogo" target="_blank" class="logo-box">
                        <img src="{!! $image !!}">
                    </a>
                </td>
            </tr>
        </table>
    </div>
    <div class="invoice-table-wrap">
        <table class="invoice-table">
            <thead>
            <th>@lang('keywords.Client',[],$lang)</th>
            <th>{!! $client_name !!}</th>
            </thead>
            <tbody>
            <tr>
                <td>
                    @lang('keywords.Receipt',[],$lang) #
                </td>
                <td>
                    {!! $receipt_id !!}
                </td>
            </tr>
            <tr>
                <td>
                    @lang('keywords.Principal',[],$lang)
                </td>
                <td>
                    {!! $principal !!}
                </td>
            </tr>
            <tr>
                <td>
                    @lang('keywords.Interest',[],$lang)
                </td>
                <td>
                    {!! $interest !!}
                </td>
            </tr>
            <tr>
                <td>
                    @lang('keywords.Origination Fee',[],$lang)
                </td>
                <td>
                    {!! $origination !!}
                </td>
            </tr>
            <tr>
                <td>
                    @lang('keywords.Renewal fee',[],$lang)
                </td>
                <td>
                    {!! $renewal !!}
                </td>
            </tr>
            <tr>
                <td>
                    @lang('keywords.Debt collection fee',[],$lang)
                </td>
                <td>
                    {!! $debt_collection_fee !!}
                </td>
            </tr>
            <tr>
                <td>
                    @lang('keywords.Administration fee',[],$lang)
                </td>
                <td>
                    {!! $admin_fee !!}
                </td>
            </tr>
            <tr>
                <td>
                    @lang('keywords.Taxes',[],$lang)
                </td>
                <td>
                    {!! $tax !!}
                </td>
            </tr>
            <tr>
                <td></td>
                <td></td>
            </tr>
            <tr class="total-block">
                <td>
                    @lang('keywords.Method of payment',[],$lang)
                </td>
                <td>{!! $loan_transaction !!}</td>
            </tr>
            <tr>
                <td>
                    @lang('keywords.Total payment',[],$lang)
                </td>
                <td>{!! $payment_amount !!}</td>
            </tr>
            <tr>
                <td>
                    @lang('keywords.Currency',[],$lang)
                </td>
                <td>{!! $currency !!}</td>
            </tr>
            <tr>
                <td>
                    @lang('keywords.Balance',[],$lang)
                </td>
                <td><span class="total-amount">{!! $total !!}</span></td>
            </tr>
            </tbody>
        </table>
    </div>
    <div class="invoice-disclaimer">
        <label>@lang('keywords.Disclaimer',[],$lang):</label>
        @lang('keywords.disclaimer_text',[],$lang)
    </div>
</page>
</body>

</html>
