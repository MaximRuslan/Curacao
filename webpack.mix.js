let mix = require('laravel-mix');

let common_css = 'resources/assets/css/common/';
let common_js = 'resources/assets/js/common/';

let client_css = 'resources/assets/css/client/';
let client_js = 'resources/assets/js/client/';

let admin_css = 'resources/assets/css/admin/';
let admin_js = 'resources/assets/js/admin/';

let merchant_css = 'resources/assets/css/merchant/';
let merchant_js = 'resources/assets/js/merchant/';

let client_img = 'resources/assets/img/client';
let admin_img = 'resources/assets/img/admin';
let admin_fonts = 'resources/assets/fonts/admin';

let public_client_css = 'public/resources/css/client/';
let public_client_img = 'public/resources/img/client';
let public_client_js = 'public/resources/js/client/';

let public_admin_css = 'public/resources/css/admin/';
let public_admin_img = 'public/resources/img/admin';
let public_admin_fonts = 'public/resources/fonts/admin';
let public_admin_js = 'public/resources/js/admin/';

let public_merchant_css = 'public/resources/css/merchant/';
let public_merchant_img = 'public/resources/img/merchant';
let public_merchant_fonts = 'public/resources/fonts/merchant';
let public_merchant_js = 'public/resources/js/merchant/';


mix
//clients css changes
    .styles([
        client_css + 'style.css',
        common_css + 'dataTables.bootstrap4.min.css',
        common_css + 'buttons.bootstrap4.min.css',
        common_css + 'responsive.bootstrap4.min.css',
        common_css + 'select2.min.css',
        common_css + 'sweetalert2.min.css',
        common_css + 'bootstrap-datepicker.min.css',
    ], public_client_css + 'style.css')

    //admin login css changes
    .styles([
        admin_css + 'bootstrap.min.css',
        admin_css + 'login_style.css',
        common_css + 'select2.min.css',
    ], public_admin_css + 'login_style.css')

    //admin css changes
    .styles([
        admin_css + 'bootstrap.min.css',
        admin_css + 'icons.css',
        admin_css + 'style.css',
        admin_css + 'custom.css',
        common_css + 'dataTables.bootstrap4.min.css',
        common_css + 'buttons.bootstrap4.min.css',
        common_css + 'responsive.bootstrap4.min.css',
        common_css + 'jquery.steps.css',
        common_css + 'select2.min.css',
        common_css + 'bootstrap-datepicker.min.css',
        common_css + 'sweetalert2.min.css',
        common_css + 'trumbowyg.min.css',
        common_css + 'switchery.min.css',
    ], public_admin_css + 'style.css')

    //merchant css changes
    .styles([
        merchant_css + 'bootstrap.min.css',
        merchant_css + 'icons.css',
        merchant_css + 'style.css',
        merchant_css + 'custom.css',
        common_css + 'dataTables.bootstrap4.min.css',
        common_css + 'buttons.bootstrap4.min.css',
        common_css + 'responsive.bootstrap4.min.css',
        common_css + 'select2.min.css',
        common_css + 'sweetalert2.min.css',
    ], public_merchant_css + 'style.css')


    //image copy code
    .copy(client_img, public_client_img)
    .copy(admin_img, public_admin_img)
    .copy(admin_fonts, public_admin_fonts)


    //client js changes
    .scripts([
        client_js + 'app.js',
        common_js + 'jquery.dataTables.min.js',
        common_js + 'dataTables.bootstrap4.min.js',
        common_js + 'dataTables.responsive.min.js',
        common_js + 'responsive.bootstrap4.min.js',
        common_js + 'select2.full.min.js',
        common_js + 'sweetalert2.min.js',
        common_js + 'moment.js',
        common_js + 'bootstrap-datepicker.min.js',
        common_js + 'signature.js',
        common_js + 'jquery.form.js',
        common_js + 'common.js',
        client_js + 'profile.js',
    ], public_client_js + 'app.js')

    .scripts(client_js + 'loansIndex.js', public_client_js + 'loansIndex.js')
    .babel(client_js + 'loansCreate.js', public_client_js + 'loansCreate.js')
    .babel(client_js + 'loanCalculation.js', public_client_js + 'loanCalculation.js')
    .scripts(client_js + 'loansShow.js', public_client_js + 'loansShow.js')
    .scripts(client_js + 'credit.js', public_client_js + 'credit.js')

    //merchant js
    .scripts([
        merchant_js + 'modernizr.min.js',
        merchant_js + 'jquery.min.js',
        merchant_js + 'popper.min.js',
        merchant_js + 'bootstrap.min.js',
        merchant_js + 'detect.js',
        merchant_js + 'fastclick.js',
        merchant_js + 'jquery.slimscroll.js',
        merchant_js + 'jquery.blockUI.js',
        merchant_js + 'waves.js',
        merchant_js + 'wow.min.js',
        merchant_js + 'jquery.nicescroll.js',
        merchant_js + 'jquery.scrollTo.min.js',
        merchant_js + 'jquery.core.js',
        merchant_js + 'jquery.app.js',
        common_js + 'jquery.validate.min.js',
        common_js + 'jquery.dataTables.min.js',
        common_js + 'dataTables.bootstrap4.min.js',
        common_js + 'dataTables.responsive.min.js',
        common_js + 'select2.full.min.js',
        common_js + 'sweetalert2.min.js',
    ], public_merchant_js + 'app.js')

    .babel(merchant_js + 'common.js', public_merchant_js + 'common.js')
    .babel(merchant_js + 'login.js', public_merchant_js + 'login.js')
    .babel(merchant_js + 'payments.js', public_merchant_js + 'payments.js')
    .babel(merchant_js + 'reconciliations.js', public_merchant_js + 'reconciliations.js')

    //admin login js changes
    .scripts([
        admin_js + 'jquery.min.js',
        admin_js + 'popper.min.js',
        admin_js + 'bootstrap.min.js',
        common_js + 'select2.full.min.js',
        common_js + 'common.js',
    ], public_admin_js + 'login_app.js')

    //admin js changes
    .scripts([
        admin_js + 'jquery.min.js',
        admin_js + 'popper.min.js',
        admin_js + 'bootstrap.min.js',
        admin_js + 'detect.js',
        admin_js + 'fastclick.js',
        admin_js + 'jquery.slimscroll.js',
        admin_js + 'jquery.blockUI.js',
        admin_js + 'waves.js',
        admin_js + 'wow.min.js',
        admin_js + 'jquery.nicescroll.js',
        admin_js + 'jquery.scrollTo.min.js',
        common_js + 'switchery.min.js',
        admin_js + 'jquery.core.js',
        admin_js + 'custom.js',
        admin_js + 'jquery.app.js',
        common_js + 'jquery.dataTables.min.js',
        common_js + 'dataTables.bootstrap4.min.js',
        common_js + 'dataTables.responsive.min.js',
        common_js + 'responsive.bootstrap4.min.js',
        common_js + 'jquery.steps.min.js',
        common_js + 'select2.full.min.js',
        common_js + 'moment.js',
        common_js + 'bootstrap-datepicker.min.js',
        common_js + 'jquery.validate.min.js',
        common_js + 'sweetalert2.min.js',
        common_js + 'signature.js',
        common_js + 'trumbowyg.min.js',
        common_js + 'common.js',
        admin_js + 'profile.js',
    ], public_admin_js + 'app.js')

    .babel(admin_js + 'cockpit.js', public_admin_js + 'cockpit.js')
    .babel(admin_js + 'usersIndex.js', public_admin_js + 'usersIndex.js')
    .babel(admin_js + 'usersCreate.js', public_admin_js + 'usersCreate.js')
    .babel(admin_js + 'merchantsIndex.js', public_admin_js + 'merchantsIndex.js')
    .babel(admin_js + 'merchantsCreate.js', public_admin_js + 'merchantsCreate.js')
    .babel(admin_js + 'merchantsPayment.js', public_admin_js + 'merchantsPayment.js')
    .babel(admin_js + 'merchantsReconciliation.js', public_admin_js + 'merchantsReconciliation.js')

    .babel(admin_js + 'paybillsIndex.js', public_admin_js + 'paybillsIndex.js')
    .babel(admin_js + 'paybillsCreate.js', public_admin_js + 'paybillsCreate.js')

    .babel(admin_js + 'loans.js', public_admin_js + 'loans.js')
    .babel(admin_js + 'loanView.js', public_admin_js + 'loanView.js')
    .babel(admin_js + 'loanCalculation.js', public_admin_js + 'loanCalculation.js')
    .babel(admin_js + 'relationship.js', public_admin_js + 'relationship.js')
    .babel(admin_js + 'loanReason.js', public_admin_js + 'loanReason.js')
    .babel(admin_js + 'loanDeclineReason.js', public_admin_js + 'loanDeclineReason.js')
    .babel(admin_js + 'loanOnholdReason.js', public_admin_js + 'loanOnholdReason.js')
    .babel(admin_js + 'loanType.js', public_admin_js + 'loanType.js')
    .babel(admin_js + 'existingLoanType.js', public_admin_js + 'existingLoanType.js')
    .babel(admin_js + 'country.js', public_admin_js + 'country.js')
    .babel(admin_js + 'district.js', public_admin_js + 'district.js')
    .babel(admin_js + 'branch.js', public_admin_js + 'branch.js')
    .babel(admin_js + 'referral_category.js', public_admin_js + 'referral_category.js')
    .babel(admin_js + 'bank.js', public_admin_js + 'bank.js')
    .babel(admin_js + 'bankReconcile.js', public_admin_js + 'bankReconcile.js')
    .babel(admin_js + 'dayopen.js', public_admin_js + 'dayopen.js')
    .babel(admin_js + 'audit.js', public_admin_js + 'audit.js')
    .babel(admin_js + 'credit.js', public_admin_js + 'credit.js')
    .babel(admin_js + 'messages.js', public_admin_js + 'messages.js')
    .babel(admin_js + 'push_notifications.js', public_admin_js + 'push_notifications.js')
    .babel(admin_js + 'nlbReason.js', public_admin_js + 'nlbReason.js')
    .babel(admin_js + 'nlb.js', public_admin_js + 'nlb.js')
    .babel(admin_js + 'dashboard.js', public_admin_js + 'dashboard.js')
    .babel(admin_js + 'raffleWinner.js', public_admin_js + 'raffleWinner.js')
    .babel(admin_js + 'referralHistory.js', public_admin_js + 'referralHistory.js')
    .babel(admin_js + 'template.js', public_admin_js + 'template.js')
    .babel(admin_js + 'wallet.js', public_admin_js + 'wallet.js')

    //socket js
    .js('resources/assets/js/app.js', 'public/js')

    .version();
