<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Country;
use App\Models\UserTerritory;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CountryController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin')->only([
            'index',
            'store',
            'show',
            'indexDatatable',
            'destroy'
        ]);
    }

    public function index()
    {
        $data = [];
        $data['default_terms'] = '<ol>
<ol>
<li><label class="cus--label">Definitions<br /></label>
<p>The following additional defined terms appear in these Terms of Service.</p>
<ul class="">
<li><strong>&ldquo;ApplicableLaw&rdquo;</strong> Any and all federal, state and local laws, rules and regulations applicable to the Services</li>
<li><strong>&ldquo;FundingAccount&rdquo;</strong> A checking or savings account that is registered or used by you to fund payments made byyou to your Mobile Wallet Account.</li>
<li><strong>&ldquo;Issuer&rdquo;</strong> or <strong>&ldquo;PartnerBank&rdquo;</strong> The bank used by Frog to hold Users funds.</li>
<li><strong>&ldquo;Froggy&rdquo;</strong> or <strong>&ldquo;Froggy" Payment&rdquo; </strong> Payment initiated to another Wallet holder through the &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Payment Service that debits or charges a &ldquo;Froggy&rdquo; Balance of the Sender and credits the funds to the Recipient&rsquo;s Wallet Balance.</li>
<li><strong>&ldquo;PaymentTransaction&rdquo;</strong> The processing of a payment that results in the debiting, charging, or other related transaction, of the Purchase Amount to a Buyer&rsquo;s Wallet Account.</li>
<li><strong>&ldquo;Product&rdquo;</strong> Any merchandise, good or service that a Buyer may purchase using a Service.</li>
<li><strong>&ldquo;PurchaseAmount&rdquo;</strong> The dollar or guilder amount of anyPayment Transaction,as applicable.</li>
<li><strong>&ldquo;Recipient&rdquo;</strong> Customer who receives a payment from the Sender as the result of aWallet-to-Wallet Payment.</li>
<li><strong>&ldquo;Sender&rdquo;</strong> Customer who uses the Wallet-to-Wallet Service to initiate a Payment to send a payment to a Recipient.</li>
<li><strong>&ldquo;FroggyMobileWalletAccount&rdquo;</strong> or <strong>&ldquo;WalletAccount&rdquo;</strong> The account you are assigned upon your initial acceptance of theseTerms of Service and the Issuer Terms of Use.</li>
<li><strong>&ldquo;WalletBalance&rdquo;</strong>Funds that you may maintain in your Wallet Account. Wallet Balances are held in a deposit account at the Issuer and are the same funds available on your Wallet Account.</li>
<li><strong>&ldquo;IssuerTermsofUse&rdquo;</strong> The terms and conditions between you and the Issuer which are applicable to use of your Wallet Account.</li>
<li><strong>&ldquo;The Company&rdquo;, &ldquo;Ourselves&rdquo;, &ldquo;We&rdquo;, &ldquo;Our&rdquo; and &ldquo;Us&rdquo;,</strong> Caribbean Cash Services N.V.</li>
<li><strong>&ldquo;Client&rdquo;, &ldquo;You&rdquo; and &ldquo;Your&rdquo;</strong> refers to you, the person accessing this website and accepting the Company&rsquo;s terms and conditions.</li>
<li><strong>&ldquo;Party&rdquo;, &ldquo;Parties&rdquo;, or &ldquo;Us&rdquo;</strong> ,refers to both the Client and ourselves, or either the Client or ourselves.</li>
</ul>
<p>All terms refer to the offer, acceptance and consideration of payment necessary to undertake the process of our assistance to the Client in the most appropriate manner, whether by formal meetings or any other means, for the express purpose of meeting the Client&rsquo;s needs in respect of provision of the Company&rsquo;s stated services/products, in accordance with and subject to, prevailing law of Cura&ccedil;ao. Any use of the above terminology or other words in the singular, plural, capitalisation and/or he/she or they, are taken as interchangeable and therefore as referring to same.</p>
</li>
<li><label class="cus--label">OpeningyourFroggyMobileWalletAccount</label>
<ol class="">
<li><label class="cus--label">GettingstartedwithFroggyMobileWallet</label>
<p>When you sign up for &ldquo;Froggy&rdquo; , the Issuer will issue you a virtual account, which you will load using your funds from your personal checking or savings account or by taking a loan with Caribbean Cash. The virtual account holds the funds that are in your Froggy Mobile &ldquo;Froggy&rdquo; Account (your &ldquo;Wallet Balance&rdquo;). These funds will be held in a custodial sub-account maintained by the Issuer (your funds are not maintained by us or in our control at any time). You may be asked to provide information such as your name, contact information, bank name, routing number and account number, date of birth, and/or your identificationnumber. We may verify your registration information with a third party verification vendor. In some cases, we may ask you to send us additional information, such as a copy ofyour driver&rsquo;s license or passport, or to answer additional questions to help us verify your identity.</p>
<p>We, to determine if you are eligible to begin and/or continue to use the Services will use the information you provide. Provision and use of such data issubject to our Privacy Policy, as well as the privacy policy of the Issuer.</p>
<p>Subject to the Terms and Conditions of the Issuer, you can use your Froggy Mobile Walletto make payments or purchases from any Frog Wallet affiliate merchant, transfer funds to other Froggy Mobile Walletholders on &ldquo;Froggy&rdquo; platform.</p>
</li>
<li><label class="cus--label">USAPATRIOTACTNOTICE</label>
<p>To help the government fight the funding of terrorism and money laundering activities, federal law requires all financial institutions to obtain, verify, and record information that identifies each individual or business that opens an account or requests credit.</p>
</li>
<li><label class="cus--label">AccuracyofRegistrationInformation</label>
<p>You are responsible for providing accurate registration information and for keeping your registration information up to date, or notifying us in the event of changes.</p>
</li>
<li><label class="cus--label">RelationtoyourAccountandLoanswiththeLoanIssuer.</label>
<p>In order to use the Services, you must bein good standing with the Loan Issuer. If your Loan accountwith the Loan issuer is closed/blockedfor any reason, your &ldquo;Froggy&rdquo; Account mayalso be closed and you will no longer be able to access the Services, and any remaining account funds maybe distributed to the outstanding Loan at the Loan Issuer.</p>
</li>
</ol>
</li>
<li><label class="cus--label">Using&ldquo;Froggy&rdquo;toMakeMerchantPayments</label>
<ol class="">
<li><label class="cus--label">GeneralDescription</label>
<p>&ldquo;Froggy&rdquo; may allow you to make purchase transactions from merchants&rsquo;in-store using the Froggy Mobile &ldquo;Froggy&rdquo; Account which resides on your mobile device.</p>
</li>
<li><label class="cus--label">GettingStartedwithFroggyMobileWallet</label>
<ul class="a">
<li>In order to use &ldquo;Froggy&rdquo; ,you must be a resident of Cura&ccedil;ao or Sint Maarten.</li>
<li>You agree to notify Caribbean Cashimmediately of any unauthorized use of &ldquo;Froggy&rdquo; or any other breach of security regarding &ldquo;Froggy&rdquo; of which you have knowledge.</li>
<li>Caribbean Cashdoes not make any representation or verify that your &ldquo;Froggy&rdquo; Account is in good standing or that we will authorize or approve any purchases from a merchant when you use &ldquo;Froggy&rdquo; in connection with that purchase.</li>
</ul>
</li>
<li><label class="cus--label">UsingtheMobileWalletService</label>
<p>You can use the Mobile Wallet Service as follows:</p>
<ul class="no">
<ul class="no">
<li>Bill Payments. By logging into your Froggy Mobile Walletyou can choose from our list of affiliate merchants to process a bill payment. Choose the merchant, enter the amount and click agree to process the payment. By using your &ldquo;Froggy&rdquo; Account with this method to make Payment Transactions, you authorize Caribbean Cash to charge your &ldquo;Froggy&rdquo; Account for such Payment Transaction in coordination with any third-party payment processors.</li>
<li>Transfer funds to other Froggy Mobile Walletuser account. By logging into your Froggy Mobile Walletyou can choose to Transfer Funds to other Froggy Mobile Walletuser account if. Enter the Frog User Account identification Number, enter the amount you wish to transfer and click agree to transfer the funds.</li>
<li>Transfer funds to a bank account. By logging into your Froggy Mobile Walletyou can choose to Transfer Funds to a Local Bank Account.</li>
<li>In store using your wallet stored on your mobile device by logging in to your Wallet Account, displaying your personal QR Code, and holding the mobile device under the <strong>merchant&rsquo;s reader, if this functionality is enabled and available. By using your &ldquo;Froggy&rdquo;</strong> Account with this method to make Payment Transactions, you authorize Caribbean Cashto charge your &ldquo;Froggy&rdquo; Account for such Payment Transaction in coordination with any third-party payment processors.</li>
</ul>
</ul>
<br />
<p>By using your &ldquo;Froggy&rdquo; Account using the methods described above to make Payment Transactions, you authorize Caribbean Cashto charge your &ldquo;Froggy&rdquo; Account for such Payment Transaction in coordination with the Issuer and any third-party payment processors.</p>
</li>
<li><label class="cus--label">TransactionLimits</label>
<p>Payment Transaction Limits. There is a maximum limit on purchase payments that you may make using your Wallet. Maximum purchase payments may not exceed the $5,000/ Nafl. 5000, -per day or any other daily purchase transaction limit imposed by the Issuer. Caribbean Cashmay, at its discretion, increase this maximum upon verification with the Issuer. In addition, Wallet purchase transactions below this amount willbe declined if you have insufficient Wallet Balance. In addition, you may be subject to limitations on the amount or type of transaction or merchant as per the Issuer&rsquo;s Terms and Conditions. You are responsible for any charges and related fees that may be imposed under the Issuer&rsquo;s Terms and Conditions. The Wallet is not a credit card, and Caribbean Cashas the issuer, is notextending you credit in connection with your use of the Mobile Wallet Service.</p>
</li>
<li><label class="cus--label">GeneralTermsrelatingtotheuseoftheMobileWalletService</label>
<ul class="a">
<li>Caribbean Cashwill instruct the Issuer to deny a requested Mobile Wallet Service purchase if Caribbean Cashhas reason to believe that it will not be able to initiate a charge to your Wallet, or if Caribbean Cashotherwise believes that Caribbean Cashwill not be able to obtain funds from you to complete the requested purchase payment. We reserve the right to decline any Froggy Mobile Walletinitiated Payment Transaction. We reserve the right to suspend your use of any Mobile Wallet Service for any reason.</li>
<li>You acknowledge and agree that your purchases through the Mobile Wallet Service are transactions between you and the merchant and not with Caribbean Cashor any of its affiliates.</li>
<li>The Wallet Mobile Service may only be used for dollaror Antillean Guildertransactions within Cura&ccedil;ao &amp; Sint Maarten.</li>
<li>Your Authorization for Mobile Wallet Service Billing. By using the Mobile Wallet Services to make a purchase payment, you authorize the use of the &ldquo;Froggy&rdquo;Account to complete a payment to the merchant, and you authorize Caribbean Cashor its third party payment processors to charge the &ldquo;Froggy&rdquo; Account for the Purchase Transaction.</li>
<li>Receipts at Merchant Locations. You may receive a transaction receipt froma merchant when you use the Mobile Wallet Service. Caribbean Cashis under no obligation to provide you with a receipt or other written confirmation in connection with the charge made at a merchant location or with an online merchant.</li>
<li>Limits on Merchants and Purchases. We may impose limits on merchants where you can use the Mobile Wallet Service.</li>
<li>Periodic Statements. You agree that we will not provide you with a separate periodic statement for your use of the Wallet Service. An electronic transaction history showing all transactions with the &ldquo;Froggy&rdquo; Account is available at the Site and on the App. You are responsible for reviewing the &ldquo;Froggy&rdquo; Account transaction history for accuracy.</li>
<li>Customer Service. If you have an inquiry regarding a payment made withthe Mobile Wallet Service, or you believe there has been an error or unauthorized transaction regarding a payment transaction, please contact Caribbean Cashat the address or phone number set forth below.</li>
</ul>
</li>
</ol>
</li>
<li><label class="cus--label">&ldquo;Froggy&rdquo;Balanceand&ldquo;Froggy&rdquo;to&ldquo;Froggy&rdquo;Payments</label>
<ol class="">
<li><label class="cus--label">WalletBalance</label>
<ul class="a">
<li>Creating Wallet Balance. You may maintain funds in your Wallet Account, and such funds will be known as your Wallet Balance. Funds can be accumulated in your &ldquo;Froggy&rdquo; Balance by means of:
<ul class="i">
<ul class="i">
<li>a transfer of funds from your Funding Account;</li>
<li>funds received from a Sender; and/or,</li>
<li>a credit/ loanissued to youby Caribbean Cash.</li>
</ul>
</ul>
<br />
<p>We currently don&rsquo;tpermit funding your &ldquo;Froggy&rdquo; Balance from a credit card.</p>
</li>
<li>
<p>Withdrawal of Wallet Balances. You may withdraw funds from your mobile &ldquo;Froggy&rdquo; Balance to your registered checking or savings account in Froggy Mobile Wallet.</p>
<p>You can also withdraw funds from your mobile &ldquo;Froggy&rdquo; Balance at any Caribbean Cash Branch. You are solely responsible for any fees that may be charged to you for processing a withdrawal. Caribbean Cashwill not be responsible for withdrawals provided to the wrong party or account, or where you provided incorrect withdrawal account details to Caribbean Cash.</p>
</li>
<li>Use of Wallet Balances. You may use your available &ldquo;Froggy&rdquo; Balance for Wallet-to-Wallet Payments, in-store payments, point of sale/ bill paymenttransactions or to fund or contribute to a campaign or charity.</li>
<li>Limits. The maximum amount of your &ldquo;Froggy&rdquo; Balance at any time is limited to Ten Thousand Dollars ($10,000) or Nafl. 10000,-Antillean Guilders. Amounts received in excess of this amount will be rejected. No transaction using your &ldquo;Froggy&rdquo; Balance may exceed Five Thousand Dollars ($5,000) or Nafl. 5000,-Antillean Guilder as an individual transaction or as a total within a 24-hour period.</li>
<li>No Interest on Wallet Balances. Caribbean Cashdoesnot pay interest to you on Wallet Balances or any other funds because we do not use your money asa financial intermediary does (eg banks) to give credits to third parties. The fund is completely of the depositor and CaribbeanCash cannot dispose of that fund and is exclusively to cover the transactions of the depositor.</li>
<li>Negative Wallet Balances. If for any reason, your &ldquo;Froggy&rdquo; Balance becomes negative, you authorize Caribbean Cashtousea third party to recover funds from you to bring your &ldquo;Froggy&rdquo; Balance to $0 / Nafl. 0,00.</li>
<li>Liability for Failure to Make Transactions. We may restrict access to your Wallet Balance, including transactions using your Wallet, temporarily or permanently, if we notice suspicious activity in connection with your &ldquo;Froggy&rdquo; Balance account. We have no liability for restricting access to the &ldquo;Froggy&rdquo; Balance beware of suspected suspicious activity. We will not be liable:
<ul class="a">
<li>If through no fault of ours, a merchant refuses to honor a transaction using your Wallet Balance;</li>
<li>If through no fault of ours, you do not have enough money available in your &ldquo;Froggy&rdquo; Balance to make a purchase;</li>
<li>If the terminal or system was not working properly;</li>
<li>If the transaction information supplied by you or a third party is incorrect or untimely;</li>
<li>If circumstances beyond our control (such as flood, fireor hurricane) prevent a transaction, despite reasonable precautions that we have taken; or</li>
<li>Themerchant authorizes an amount greater than the purchase amount.</li>
<li>There may be other applicable exceptions as otherwise provided by Cura&ccedil;ao laws.</li>
</ul>
</li>
</ul>
</li>
<li><label class="cus--label">Wallet-to-WalletPayments</label>
<ul class="a">
<li><label class="cus--label">Requirements for Registration</label>
<p>In order to use the Wallet-to-Wallet Service to make Wallet-to-Wallet Payments from your Wallet Account, you must have a <strong>&ldquo;Froggy&rdquo; Account</strong> in good standing and be transferring funds to another <strong>&ldquo;Froggy&rdquo; Account</strong> in good standing.</p>
<p>The Wallet-to-Wallet Service is currently available only toSenders and Recipients who are Cura&ccedil;ao or Sint Maartenresidents and who otherwise meet the requirements of these Terms of Service. All users of the Wallet-to-Wallet Service agree to the restrictions contained in this policy.</p>
</li>
<li><label class="cus--label">Your Authorization for a Wallet-to-Wallet Payment</label>
<ul class="no">
<li>When you request a Wallet-to-Wallet Payment to be effected through the Wallet-to-Wallet Service, you authorize Issuer to debit funds in that amount from your Wallet Balance;</li>
<li>Your authorizations underthis Section shall remain in effect while you are a Customer and for a period of ninety (90) days following termination or cancellation of the Services.</li>
</ul>
</li>
</ul>
</li>
<li><label class="cus--label">Processingof&ldquo;Froggy&rdquo;to&ldquo;Froggy&rdquo;Payments</label>
<ul class="a">
<li>You may send a &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Payment to any other person who is a resident of Cura&ccedil;ao or Sint Maarten with a valid &ldquo;Froggy&rdquo; Account and is authorized to receive &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; funds. You must select the &ldquo;Froggy&rdquo; Balance from which your &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Payment will be sent at the time you initiate the &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Payment. You will also have the option to enter a &ldquo;memo&rdquo; or note for the &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Payment, which may be retained by Caribbean Cashas part of the &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Payment record and/or monitored by Caribbean Cashin accordance with its financial regulatory obligations.</li>
<li>In the event you initiate a &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Payment from your &ldquo;Froggy&rdquo; Balance and there are insufficient funds for the &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Payment, the payment willbe declined.</li>
<li>Issuer will hold funds debited from a Sender&rsquo;s &ldquo;Froggy&rdquo; Balance pending transfer to the Recipient. You will not have access to funds in the process of transmission to the Recipient.</li>
<li>The Recipient will receive an email from the Sender of a &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Payment advising the Recipient that funds have been received for him or her. Such funds may not be available to the Recipient at the time he or she receives the email, as certain funds may be subject to a 3-day holding period. The Recipient may also receive notification of the transfer from within the App.</li>
<li>The Recipient must have a &ldquo;Froggy&rdquo; Account in order to receivea &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Payment.</li>
<li>Notwithstanding the foregoing, Issuer will use commercially reasonable efforts to make funds available to a Recipient no later than three (3) business days after Issuer receives final settlement of the Sender&rsquo;s funding transaction. Issuer has no liability to you or any other person for any delay in making funds available to the Recipient.</li>
</ul>
</li>
<li><label class="cus--label">TransactionRecords,CustomerServiceandErrorResolutionPolicy</label>
<p>Records of your &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Payments through the &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Service, and your other &ldquo;Froggy&rdquo; Balance transactions (together, your &ldquo;&ldquo;Froggy&rdquo; Balance Transactions&rdquo;) will be reflected in your transaction history in your Wallet Account. You are responsible for reviewing your transaction activity to determine if there are any errors or unauthorized transactions, and for alerting Caribbean Cashof such events.</p>
<p>It is your obligation to save or print a copy of your Wallet Electronic Transaction History. If your access to the Service is canceled or terminated by you or by us for any reason, you will not be permitted to access your statements stored by Caribbean Cash. You may request paper copies of certain electronic records about your account and stored by Caribbean Cash, subject to reasonable limits. Caribbean Cashreserves the right to charge you fees for such paper copies.</p>
</li>
<li><label class="cus--label">Fees</label>
<p>Caribbean Cashmay charge fees in accordance with the fee schedule. For information on applicable fees, consult our website <a href="#nogo">www.caribbeancash.cw.</a></p>
</li>
</ol>
</li>
<li><label class="cus--label">UseofServices</label>
<ol class="">
<li><label class="cus--label">Limitations</label>
<p>Notwithstanding any limitations described elsewhere in this Agreement, we may establish general practices and limits concerning use of the Services, including without limitation individual or aggregate transaction limits on the amount or number of transactions during any specified time period(s). We reserve the right to change, suspend or discontinue any aspect of the Services at any time, including hours of operation or availability of the Services or any Service feature, without notice and without liability. We also reserve the right to impose limits on certain Service features or restrict access to some or all of the Services without notice and without liability. We may decline to process any transaction without prior notice to you.</p>
<p>Caribbean Cashmay delay, hold, cancel or reverse processing of any transaction if:</p>
<ul class="a">
<li>a Sender makes a claim to Caribbean Cashfor a refund or other reversal, or</li>
<li>Caribbean Cash, in its sole discretion, believes that the transaction is invalid, suspicious, involves misconduct or fraud, or otherwise violates Applicable Law,this Agreement, or any applicable Caribbean Cashpolicies.</li>
</ul>
<p>We may limit or suspend your use of one or more Services at any time, in our sole and absolute discretion. If we suspend your use of a Service, we will attempt to notify you by electronic mail. Suspension of your use of a Service will not affect your rights and obligations pursuant to these Terms of Service arising before or after such suspension or with respect to any non-terminated Services.</p>
<p>&ldquo;Froggy&rdquo; is intended for use on mobile devices, Android operating systems, Apple iOS or other devices or operating systems approved by Caribbean Cash, as provided to you directly by your mobile carrier. You are strictly prohibited from using &ldquo;Froggy&rdquo; on a mobile device or Android or Apple operating system, or other device or operating system approved by Caribbean Cash,that has been modified or customized in any way. You bear sole responsibility for such unauthorized use of &ldquo;Froggy&rdquo; on a modified mobile device, Android operating system, Apple operating system or other device or operating system approved by Caribbean Cash.</p>
</li>
<li><label class="cus--label">Fraud</label>
<p>If you believe your &ldquo;Froggy&rdquo; Accounthas been opened or used in an unauthorized manner, please contact the Issuer immediately as set forthin the Issuer&rsquo;s Terms and Conditions. You are cautioned to only make payments to person&rsquo;s you already know and trust. You should not send money to purchase product or donate to campaigns if you don&rsquo;t know and trust the person you are sending money to. There is a risk that the person is acting dishonestly and may defraud you out of your money. Be careful.</p>
</li>
<li><label class="cus--label">CaribbeanCashIsNotaBankingInstitution</label>
<p>Caribbean Cashis not a bank or other chartered depository institution. With respect to merchant payment transactions, solely the merchant handles all payment processing, and, other than releasing the funds under a properly made transaction, Caribbean Cashis not involved in the merchant&rsquo;s processing of the payment.</p>
<p>These Terms of Service do not amend or otherwise modify your agreement with the issuer of your Funding Account. In the event of any inconsistency between these Terms of Service and your agreement with the issuer of your Funding Account or the Issuer&rsquo;s Terms and Conditions, these Terms of Service govern the relationship between you and Caribbean Cashsolely with respect to Caribbean Cash&rsquo;s Site and the App, and your agreement with the Issuer of your Funding Account governs the relationship between you and the Issuerof such item. You acknowledge and agree that you are solely responsible for the Funding Account, and any other information you enter or otherwise store in &ldquo;Froggy&rdquo; . Caribbean Cashis not responsible for the accuracy or availability of any information youenter or otherwise store in &ldquo;Froggy&rdquo; , including, without limitation, whether such information is current and up-to-date.</p>
</li>
<li><label class="cus--label">CommunicationwithIssuers</label>
<p>By electing to use &ldquo;Froggy&rdquo; , you authorize Caribbean Cash, directly or through &ldquo;Froggy&rdquo; App to communicate with the issuer of your Funding Account to provide or obtain any information required by that issuer.</p>
</li>
<li><label class="cus--label">ThirdPartyProviders</label>
<p>Caribbean Cashmay have arranged for third party providers to provide products or services to you through &ldquo;Froggy&rdquo; (&ldquo;Third Party Providers&ldquo;). In order to use these products or services, you may be required to agree to additional terms and conditions from those Third Party Providers, and may be subject to additional requirements of the Third Party Provider. By agreeing to these Terms of Service or continuing to use &ldquo;Froggy&rdquo; , you hereby agree to any Third Party Provider terms that apply to your use of such products and services through Froggy Mobile Walletthat may be updated from time to time. For avoidance of doubt, these Third Party Provider terms are between you and the applicable Third Party Provider.</p>
</li>
<li><label class="cus--label">Advertising</label>
<p>Some of the features of Froggy Mobile Walletmay be supported by advertising revenue and may display advertisements and promotions. In consideration for Caribbean Cashgranting you access to and use of Froggy Mobile Wallet, you agree that Caribbean Cashmay place such advertising. In addition, youherebyacceptto allowinformationfrom Froggy Mobile Walletto be used by Caribbean Cash, in order to presentyou with relevant advertising.</p>
</li>
<li><label class="cus--label">ThirdPartyFees</label>
<p>You are responsible for any fees charged by your telecommunications provider, the Issuer, merchant, or any other third party in connection with your use of Froggy Mobile Wallet.</p>
</li>
<li><label class="cus--label">ExtensionofCredit</label>
<p>Caribbean Cashoffers additional services such as extension of credits in form of Payday Loans or Micro Loans. Itis not a condition nor obligationto apply or make use ofsaid extensions in connection with your use of &ldquo;Froggy&rdquo;.</p>
</li>
<li><label class="cus--label">Privacy</label>
<p>You understand and agree that personal information provided to Caribbean Cashin connection with the Services is subject to our Privacy Policy. By agreeing to these Terms of Service you hereby agree to the Privacy Policy, which can be found atwww.caribbeancash.cw, which may be updated by Caribbean Cashfrom time to time. You understand and agree that, to the extent permitted by Applicable Law, any data you provide to Caribbean Cashin connection with the Services may be shared with Caribbean Cashaffiliates and the Issuer, as well as any tax authorities, if necessary.</p>
<p>You may opt-in to providing location data through your mobile device so that the App can provide you with more relevant advertising, payment information, or other services based onyour location. If you opt-in to providing location data, you consent to the collection, use, sharing, and onward transfer of location data, as further set forth in the Privacy Policy.</p>
</li>
</ol>
</li>
</ol>
</ol>
<h5>Part2&ndash;GeneralProvisions</h5>
<ol>
<li><label class="cus--label">IntellectualPropertyRights</label>
<p>Unless otherwise stated, Caribbean Cash Services N.V. and/or it&rsquo;s licensors own the intellectual property rights for all material on Caribbean Cash Services N.V.. All intellectual property rights are reserved. You may view and/or print pages from <a href="#nogo">http://www.caribbeancash.cw</a>for your own personal use subject to restrictions set in these terms and conditions.</p>
<p><strong>You must not:</strong></p>
<ul>
<li>Republish material from <a href="#nogo">http://www.caribbeancash.cw</a></li>
<li>Sell, rent or sub-license material from <a href="#nogo">http://www.caribbeancash.cw</a></li>
<li>Reproduce, duplicate or copy material from <a href="#nogo">http://www.caribbeancash.cw</a></li>
<li>Redistribute content from Caribbean Cash Services N.V. (unless content is specifically made for redistribution).</li>
</ul>
<p>If you submit comments or ideas about our services, including without limitation about how to improve &ldquo;Froggy&rdquo; or our other products (&ldquo;Ideas&rdquo;), you agree that your submission is gratuitous, unsolicited, and withoutrestriction, that it will not place Caribbean Cash under any fiduciary or other obligation, and that we are free to use the Idea without any additional compensation to you, and/or to disclose the Idea on a non-confidential basis or otherwise to anyone. You further acknowledge that, by acceptance of your submission, Caribbean Cash does not waive any rights to use similar or related ideas previously known to Caribbean Cash, or developed by its employees, or obtained from sources other than you.</p>
<p>Caribbean Cash makes no claim or representation regarding, and accepts no responsibility for, the quality, content, nature or reliability of third-party Web sites accessible by hyperlink from our website or application, or Web sites linking to our website or application, including those of our partners. Such sites are not under our control and Caribbean Cash is not responsible for the contents of any linked site or any link contained in a linked site, or any review, changes or updates to such sites. Caribbean Cash provides these links to you only as a convenience, and the inclusion of any link does not imply affiliation, endorsement or adoption by Caribbean Cash of any site or any information contained therein. You should review the applicable terms and policies, including privacy and data gathering practices, of any site.</p>
</li>
<li><label class="cus--label">UserGeneratedContent</label>
<p>Certain parts of this website offer the opportunity for users to post and exchange opinions, information, material and data (\'Comments\') in areas of the website. Caribbean CashServices N.V. does not screen, edit, publish or review Comments prior to their appearance on the website and Comments do not reflect the views or opinions of Caribbean Cash Services N.V., its agents or affiliates. Comments reflect the view and opinion of the person who posts such view or opinion. To the extent permitted by applicable laws Caribbean Cash Services N.V. shall not be responsible or liable for the Comments or for any loss cost, liability, damages or expenses caused and or suffered as a result of any use of and/or posting of and/or appearance of the Comments on this website.</p>
<p>Caribbean Cash Services N.V. reserves the right to monitor all Comments and to remove any Comments which it considers in its absolute discretion to be inappropriate, offensive or otherwise in breach of these Terms and Conditions.</p>
<p>You warrant and represent that:</p>
<ul>
<li>You are entitled to post the Comments on our website and have all necessary licenses and consents to do so;</li>
<li>The Comments do not infringe any intellectual property right, including without limitation copyright, patent or trademark, or other proprietary right of any third party;</li>
<li>The Comments do not contain any defamatory, libelous, offensive, indecent or otherwise unlawful material or material which is an invasion of privacy</li>
<li>The Comments will not be used to solicit or promote business or custom or present commercial activities or unlawful activity.</li>
<li>You hereby grant to Caribbean Cash Services N.V. a non-exclusive royalty-free license to use, reproduce, edit and authorize others to use, reproduce and edit any of your Comments in any and all forms, formats or media.</li>
</ul>
<p>You are fully responsible for any content you post as a User. You agree by becoming a User that you will not:</p>
<ul class="no">
<li>Use sexually explicit content, obscenities, copyrighted material, or abusive/hateful language in any area of the site;</li>
<li>Use your Caribbean Cash account for any illegal purposes;</li>
<li>Provide information that is not complete and accurate;</li>
<li>Attempt to bypass the designated method of payment as provided by Caribbean Cash.</li>
</ul>
<p>By visiting Caribbean Cash&rsquo;s website or by usingthe mobile application, you are responsible for protecting yourself from content that is offensive or harmful that may have been posted on the website by another user.</p>
</li>
<li><label class="cus--label">Cookies</label>
<p>We employ the use of cookies. By using Caribbean Cash Services N.V.\'s website you consent to the use of cookies in accordance with Caribbean Cash Services N.V.&rsquo;s privacy policy. Most of the modern day interactive web sites use cookies to enable us to retrieve user details for each visit. Cookies are used in some areas of our site to enable the functionality of this area and ease of use for those people visiting. Some of our affiliate / advertising partners may also use cookies.</p>
</li>
<li><label class="cus--label">Hyperlinking to our Content</label>
<p>Thefollowing organizations may link to our Web site without prior written approval:</p>
<ul>
<li>Search engines;</li>
<li>News organizations;</li>
<li>Online directory distributors when they list us in the directory may link to our Web site in the same manner as they hyperlink to the Web sites of other listed businesses; and Systemwide Accredited Businesses except soliciting non-profit organizations, charity shopping malls, and charity fundraising groups which may not hyperlink to our Web site.</li>
</ul>
<p>These organizations may link to our home page, to publications or to other Web site information so long as the link:</p>
<ul class="a">
<li>is not in any way misleading</li>
<li>does not falsely imply sponsorship, endorsement or approval of the linking party and its products or services; and</li>
<li>fits within the context of the linking party\'s site.</li>
</ul>
<p>We may consider and approve in our sole discretion other link requests from the following types of organizations:</p>
<ul>
<li>commonly-known consumer and/or business information sources such as Chambers of Commerce;</li>
<li>dot.com community sites; associations or other groups representing charities, including charity giving sites</li>
<li>online directory distributors;</li>
<li>internet portals;</li>
<li>accounting, law and consulting firms whose primary clients are businesses;</li>
<li>and educational institutions and trade associations.</li>
</ul>
<p>We will approve link requests from these organizations if we determine that:</p>
<ul class="a">
<li>the link would not reflect unfavorably on us or our accredited businesses (for example, trade associations or other organizations representing inherently suspect types of business, such as work-at-home opportunities, shall not be allowed to link);</li>
<li>the organization does not have an unsatisfactory record with us;</li>
<li>the benefit to us from the visibility associated with the hyperlink outweighs the absence of ; and</li>
<li>where the link is in the context of general resource information or is otherwise consistent with editorial content in a newsletter or similar product furthering the mission of the organization.</li>
</ul>
<p>These organizations may link to our home page, to publications or to other Web site information so long asthe link:</p>
<ul class="a">
<li>is not in any way misleading;</li>
<li>does not falsely imply sponsorship, endorsement or approval of the linking party and it products or services; and</li>
<li>fits within the context of the linking party\'s site.</li>
</ul>
<p>If you are among the organizations listed in paragraph 2 above and are interested in linking to our website, you must notify us by sending an e-mail to info@caribbeancash.cw.</p>
<p>Please include your name, your organization name, contact information (such as a phone number and/or e-mail address) as well as the URL of your site, a list of any URLs from which you intend to link to our Web site, and a list of the URL(s) on our site to which you would like to link. Allow 2-3 weeks for a response.</p>
<p>Approved organizations may hyperlink to our Web site as follows:</p>
<ul>
<li>Approved organizations may hyperlink to our Web site as follows:</li>
<li>By use of the uniform resource locator (Web address) being linked to; or</li>
<li>By use of any other description of our Web site or material being linked to that makes sense within the context and format of content on the linking party\'s site.</li>
</ul>
<p>No use of Caribbean Cash Services N.V.&rsquo;s logo or other artwork will be allowed for linking absent a trademark license agreement.</p>
<p><strong>Iframes</strong><br /> Without prior approval and express written permission, you may not create frames around our Web pages or use other techniques that alter in any way the visual presentation or appearance of our Web site.</p>
<p><strong>Reservation of Rights</strong><br /> We reserve the right at any time and in its sole discretion to request that you remove all links or any particular link to our Web site. You agree to immediately remove all links to our Web site upon such request. We also reserve the right to amend these terms and conditions and its linking policy at any time. By continuing to link to our Web site, you agree to be bound to and abide by these linking terms and conditions.</p>
<p><strong>Removal of links from our website</strong><br /> If you find any link on our Web site or any linked web site objectionable for any reason, you may contact us about this. We will consider requests to remove links but will have no obligation to do so or to respond directly to you.</p>
<p>Whilst we endeavor to ensure that the information on this website is correct, we do not warrant its completeness or accuracy; nor do we commit to ensuring that the website remains available or that the material on the website is kept up to date.</p>
<p><strong>Content Liability</strong><br />We shall have no responsibility or liability for any content appearing on your Web site. You agree to indemnify and defend us against all claims arising out of or based upon your Website. No link(s) may appear on any page on your Web site or within any context containing content or materials that may be interpreted as libelous, obscene or criminal, or which infringes, otherwise violates, or advocates the infringement or other violation of, any third party rights.</p>
</li>
<li><label class="cus--label">Security</label>
<p>We have implemented technical and organizational measures designed to secure your personal information from accidental loss and from unauthorized access, use, alteration or disclosure. However, we cannot guarantee that unauthorized third parties will never be able to defeat those measures or use your personal information for improper purposes. You acknowledge that you provide your personal information at your own risk.</p>
</li>
<li><label class="cus--label">Termination</label>
<p>if your Froggy account is canceled for any reason or no reason, you agree:</p>
<ul class="a">
<li>that you will be responsible for all Payment Transactions, Froggy to Froggy Payments and any other obligation that you have incurred under these Terms of Service prior to closing</li>
<li>that it does not imply that your obligations established in a loan agreement with Pura Vida Cash have been canceled</li>
<li>that we reserve the right (but have no obligation) to remove all of your information and account data stored on our servers, and</li>
<li>that Pura Vida Cash will not be liable to you or a third party for termination of access to Froggy and will not be required to provide a refund of amounts previously paid. If your account remains inactive for a period of 60 days, Pura Vida Cash reserves the right to close your account without notifying you.
<p>We may, in our sole and absolute discretion, hold harmless to you or a third party, terminate your use of Froggy for any reason.</p>
<p>Upon termination, we have the right to prohibit your accessto the Services, including without limitation the deactivation of your username and password, and to refuse future access to the Services by you or a commercial entity, its parent, subsidiaries or subsidiaries or its/his successors.</p>
</li>
<li>If your Froggy Mobile&ldquo;Froggy&rdquo; Accountis terminated for any reason or no reason, you agree:
<ul class="a">
<li>to continue to be bound by this Agreement,</li>
<li>to immediately stop using Caribbean Cash Service,</li>
<li>that the license provided under this Agreement shall end,</li>
<li>that we reserve the right (but have no obligation) to delete all of your information and account data stored on our servers, and</li>
<li>that Caribbean Cash shall not be liable to you or any third party for termination of access to Caribbean Cash Service or for deletion of your information or account data. You may terminate this Agreement at any time by closing your Caribbean Cash Service Account and ceasing to use Caribbean Cash Service. Caribbean Cash reserves the right to terminate this agreement and/or suspend or delete your User or Donoraccounts at our discretion, for any reason or no reason upon notice to you. Caribbean Cash will have no obligation to provide a refund of any amounts previously paid. If your account remains inactive for any 60 day period, Caribbean Cash reserves the right to close your account without notice to you.</li>
</ul>
<p>We may, in our sole and absolute discretion without liability to you or any third party, terminate your use of one or more Services for any reason, including without limitation inactivity or violation of theseTerms of Service or other policies we may establish from time to time.</p>
<p>Upon termination of your use of the Services, you remain liable for all Payment Transactions, &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Payments and any other obligations you have incurred under these Terms of Service. Upon termination, we have the right to prohibit your access to the Services, including without limitation by deactivating your username and password, and to refuse future access to the Services by you or if a business entity, its parent, affiliates or subsidiaries or its or their successors.</p>
</li>
</ul>
</li>
<li><label class="cus--label">WarrantyDisclaimer</label>
<p>The App and the Site are provided to you &ldquo;as is&rdquo; and &ldquo;as available&rdquo; and without warranty of any kind, express or implied. WE SPECIFICALLY DISCLAIM ANY AND ALL WARRANTIES AND CONDITIONS OF MERCHANTABILITY, NON-INFRINGEMENT, AND FITNESS FOR ANY PARTICULAR PURPOSE, AND ANY WARRANTIES IMPLIED BY ANY COURSE OF DEALING, COURSE OF PERFORMANCE, OR USAGE OF TRADE. NO ADVICE OR INFORMATION (ORAL OR WRITTEN) OBTAINED BY YOU FROM US SHALL CREATE ANY WARRANTY. TO THE FULLEST EXTENT PERMISSIBLE BY LAW, CARIBBEAN CASHAND ITS SUBSIDIARIES AND OTHER AFFILIATES, AND THEIR AGENTS, CO-BRANDERS OR OTHER PARTNERS, INCLUDING BUT NOT LIMITED TO, DEVICE MANUFACTURERS, MAKE NO REPRESENTATION OR WARRANTY OF ANY KIND WHATSOEVER FOR THE SERVICES OR THE CONTENT, MATERIALS, INFORMATION AND FUNCTIONS MADE ACCESSIBLE BY THE SOFTWARE USED ON OR ACCESSED THROUGH THE SERVICES, OR FOR ANY BREACH OF SECURITY ASSOCIATED WITH THE TRANSMISSION OF SENSITIVE INFORMATION THROUGH THE SERVICES. EACH CARIBBEAN CASHPARTY DISCLAIMS WITHOUT LIMITATION, ANY WARRANTY OF ANY KIND WITH RESPECT TO THE SERVICES, NONINFRINGEMENT, MERCHANTABILITY, OR FITNESS FOR A PARTICULAR PURPOSE. THE CARIBBEAN CASHPARTIES DO NOT WARRANT THAT THE FUNCTIONS CONTAINED IN THE SERVICES WILL BE UNINTERRUPTED OR ERROR FREE. THE CARIBBEAN CASHPARTIES SHALL NOT BE RESPONSIBLE FOR ANY SERVICE INTERRUPTIONS, INCLUDING, BUT NOT LIMITED TO, SYSTEM FAILURES OR OTHER INTERRUPTIONS THAT MAY AFFECT THE RECEIPT, PROCESSING, ACCEPTANCE, COMPLETION OR SETTLEMENT OF PAYMENT TRANSACTIONS, &ldquo;FROGGY&rdquo; TO &ldquo;FROGGY&rdquo; PAYMENTS OR THE SERVICES.</p>
<p>THE CARIBBEAN CASHPARTIES ARE NOT RESPONSIBLE FOR THE ACCURACY OF ANY WALLET BALANCE, INCLUDING, WITHOUT LIMITATION, WHETHER SUCH INFORMATION IS CURRENT AND UP-TO-DATE. WITHOUT LIMITING THE GENERALITY OF THE PRECEDING SENTENCE, YOU EXPRESSLY ACKNOWLEDGE AND AGREE THAT SUCH INFORMATION IS REPORTED BY THE ISSUER AS OF A PARTICULAR TIME ESTABLISHED BY THE ISSUER AND MAY NOT ACCURATELY REFLECT YOUR CURRENT TRANSACTIONS, AVAILABLE BALANCE, OR OTHER ACCOUNT OR PROGRAM DETAILS AT THE TIME THEY ARE DISPLAYED TO YOU THROUGH THE SERVICES OR AT THE TIME YOU MAKE A PURCHASE OR REDEMPTION. YOU MAY INCUR FEES, SUCH AS OVERDRAFT FEES OR OTHER CHARGES AS A RESULT OF SUCH TRANSACTIONS, PER YOUR AGREEMENT WITH THE ISSUER.</p>
</li>
<li><label class="cus--label">Indemnity</label>
<p>ou agree to indemnify, defend and hold harmless Caribbean Cashand other affiliates, and its and their directors, officers, owners, agents, co-branders or other partners, employees, information providers, licensors, licensees, consultants, contractors and other applicable third parties (including without limitation the Issuer and other Customers) (collectively &ldquo;Indemnified Parties&rdquo;) from and against any and all claims, demands, causesof action, debt or liability, including reasonable attorney&rsquo;sfees, including without limitation attorney&rsquo;sfees and costs incurred by the Indemnified Parties arising out of, related to, or which may arise from:</p>
<ul class="a">
<li>your use or misuse of the App, the Site, or the Services;</li>
<li>any breach or non-compliance by you of any term of these Terms of Service or any Caribbean Cashpolicies;</li>
<li>any dispute or litigation caused by your actions or omissions; or</li>
<li>your negligence or violation or alleged violation of any Applicable Law or rights of a third party.</li>
</ul>
<p>In the event of such a claim, we may elect to settle with the party/parties making the claim, and you shall be liable for the damages as though we had proceeded with a trial or arbitration. We reserve the right to assume the exclusive defense and control of any matter otherwise subject to this indemnification clause, in which case you agree that you&rsquo;ll cooperate and help us in asserting any defenses.</p>
</li>
<li><label class="cus--label">LimitationofLiability</label>
<p>To the fullest extent permitted by law, in no event will any Caribbean CashParty be liable for any indirect, incidental, punitive, consequential, special, or exemplary damages of any kind, including but not limited to damages (i)</p>
<ul class="no">
<li>resulting from your access to, use of, or inability to access or use the Services;</li>
<li>for any lost profits, data loss, or cost of procurement or substitute goods or services; (</li>
<li>for any conduct of content of any third party on the Site; or</li>
<li>any goods, services, or information purchased, received, sold, or paid for by way of the Services. In no event shall our liability for direct damages be in excess of (in the aggregate) one hundred U.S. dollars ($100.00)or its equivalent in other currencies. WE ARE NOT LIABLE FOR ANY LOSS OR DAMAGE DUE TO ANY USER&rsquo;S FAILURE TO COMPLY WITH THE TERMS OF THIS AGREEMENT. WE ARE NOT RESPONSIBLE FOR ANY THIRD PARTY CHARITABLE OFFERS THAT MAY TURN OUT TO BE FRAUDULENT, IRRESPONSIBLE, OR OTHERWISE ADVERTISED IN BAD FAITH.WE ARE NOT RESPONSIBLE FOR ANY FAILURES ON THE PART OF OUR THIRD PARTY PAYMENT PROCESSORS.THE PROVISION OF OUR SERVICE TO YOU IS CONTINGENT ON YOUR AGREEMENT WITH THIS AND ALL OTHER SECTIONS OF THIS AGREEMENT.</li>
</ul>
</li>
<li><label class="cus--label">DisputeResolutionandGoverningLaw</label>
<p>We are located in Cura&ccedil;ao, Willemstad. We encourage you to contact us if you&rsquo;re having an issue, before resortingto the courts. In the unfortunate situation where legal action does arise, these Terms (and all other rules, policies, or guidelines incorporated by reference) will be governed by and construed in accordance with the Cura&ccedil;ao laws, without giving effect toany principles of conflicts of law. You agree that &ldquo;Froggy&rdquo; App and Site and its Services are deemed a passive website that does not give rise to jurisdiction over Caribbean Cash or its parents, subsidiaries, affiliates, assigns, employees, agents, directors, officers, or shareholders, either specific or general, in any jurisdiction other than Dutch Caribbean (Cura&ccedil;ao &amp; Sint Maarten).</p>
<p>You agree that any action at law or in equity arising out of or relating to these Terms, or your use or non-use of &ldquo;Froggy&rdquo;, shall be filed only in Cura&ccedil;ao, and you hereby consent and submit to the personal jurisdiction of these courts for the purposes of litigating any such action. You hereby irrevocably waive any right you may have to trial by jury in any dispute, action, or proceeding.</p>
</li>
<li><label class="cus--label">Communications.</label>
<p>Electronic communications from Caribbean Cash may be sent to you to inform you about the Services and campaigns you are associated with. You agree to allow Caribbean Cash to send these communications to your email address.You agree that you are liable for any communications you send to third partiespromoting a campaign. These communications include, but are not limited to, &ldquo;share&rdquo; emails sent through the Caribbean Cash website or App and communications sent outside of theCaribbean Cash website or mobile App.</p>
</li>
<li><label class="cus--label">RighttoAmend.</label>
<p>We have the right to change or add to the terms of this Agreement at any time, and to change, delete, discontinue, or impose conditions on any feature or aspect of the Services with notice that we in our sole discretion deem to be reasonable in the circumstances, including such notice on our website or in the App. Any use of Services after our publication of any such changes shall constitute your acceptance of this Agreement as modified. However, any dispute that arose before the modification shall be governed by the Agreement (including the binding individual arbitration clause) that was in place when the dispute arose.</p>
</li>
<li><label class="cus--label">NoEndorsementofProducts</label>
<p>We do not represent or endorse, and shall not be responsible for:</p>
<ul class="a">
<li>the reliability or performance of any Seller, merchant or Third Party Provider;</li>
<li>the truth or accuracy of the description of any Product, or of any advice, opinion, offer, proposal, statement, data or other information (collectively, &ldquo;Content&rdquo;) displayed or distributed, purchased or paid through the Service; or</li>
<li>your ability to buy or redeem Products using the Services. Caribbean Cashhereby disclaims any liability or responsibility for errors or omissions in any Content in the Services. Caribbean Cashreserves the right, but shall have no responsibility, to edit, modify, refuse to post or remove any Content, in whole or in part, that in its sole and absolute discretion is objectionable, erroneous, illegal, fraudulent or otherwise in violation of these Terms of Service.</li>
</ul>
</li>
<li><label class="cus--label">ResponsibilityforTaxes</label>
<p>The reporting and payment of any applicable taxes arising from the use of the Services is your responsibility. You hereby agree to comply with any and all applicable tax laws in connection withyour use of the Services, including without limitation, the reporting and payment of any taxes arising in connection with Payment Transactions made through the Services, or income received through &ldquo;Froggy&rdquo; to &ldquo;Froggy&rdquo; Payments.</p>
</li>
<li><label class="cus--label">UsernameandPasswordInformation</label>
<p>You are responsible for:</p>
<ul class="a">
<li>maintaining the confidentiality of your username and password,</li>
<li>any and all transactions by persons that you give access to or that otherwise use such username or password, and</li>
<li>any and all consequences of use or misuse ofyour username and password. You agree to notify us immediately of any unauthorized use of your username or password or any other breach of security regarding the Services of which you have knowledge.</li>
</ul>
</li>
<li><label class="cus--label">ElectronicCommunications</label>
<p>Caribbean Cashand Third Party Providers may be required to provide certain disclosures, notices and communications (collectively &ldquo;Communications&rdquo;) to you in written form. Pursuant to these Terms of Service, we will deliver such Communications to you in electronic form. Your agreement to the Terms of Service confirms your ability and consent to receive such Communications electronically, rather than in paper form.</p>
<ol class="">
<li><label class="cus--label">Electronicdeliveryofcommunications</label>
<p>You agree and consent to receive electronically all Communications provided to you in connection with your &ldquo;Froggy&rdquo; Account and your use of the Services. Communications include:</p>
<ul class="a">
<li>agreements and policies you must agree to in order to use the Services (e.g., these Terms of Service and the Payments Privacy Notice), including updates to those agreements and policies;</li>
<li>payment authorizations and transaction receipts or confirmations;</li>
<li>account statements and history; and,</li>
<li>all other communications or documents related to or about your account and your use of the Services.</li>
</ul>
<p>Electronic Communications shall be deemed to be received by you upon delivery in the following manner:</p>
<ul class="a">
<li>posting them to your &ldquo;Froggy&rdquo; Account on the Wallet website or in the Wallet mobile application;</li>
<li>posting them on or in a website or mobile application associated with Caribbean Cash or the Services;</li>
<li>sending them via electronic mail to the email address you used to create your Caribbean Cash &ldquo;Froggy&rdquo; Account registrations; or</li>
<li>otherwise communicating them to you via the Services.</li>
</ul>
<p>It is your responsibility to open and review Communications that we deliver to you through the methods described above. We may, but are not obligated to under these Terms of Service, provide you with notice of the availability of a Communication that is delivered in one of the methods described above (for example, by informing you of such Communication through a notification sent to your mobile device).</p>
<p>You should maintain copies of electronic Communications by printing paper copies or saving electronic copies, as applicable.</p>
</li>
<li><label class="cus--label">Hardwareandsoftwarerequirements</label>
<p>In order to access and retain electronic Communications, you will need to maintain or have access to the following computer hardware and software at your own expense:</p>
<ul class="a">
<ul class="a">
<li>a computer or mobile device with Internet or mobile connectivity;</li>
<li>a current updated web browser that includes 128-bit encryption with cookies enabled;</li>
<li>the appropriate mobile application, in the case of Communications delivered in such application,</li>
<li>softwarecapable of opening documents in PDF formats</li>
<li>access to the valid email address provided upon User&rsquo;s creation of the Caribbean Cash account; and,</li>
<li>sufficient storage space to save past Communications or a printer to print them.</li>
</ul>
</ul>
<p>By giving your consent to these Terms of Service, you confirm that you are able to meet the above requirements, and that you can receive, open, and print or save any Communications referenced in these Terms of Service for your records.</p>
</li>
<li><label class="cus--label">Requestingadditionalcopiesandwithdrawingconsent</label>
<p>The following additional terms will apply to such electronic Communications:</p>
<ul class="a">
<li>you may contact Caribbean Cash to request another electronic copy of the electronic Communication without a fee;</li>
<li>you may request a paper copy of such electronic Communication within ninety days of the original Communication issuance date, and Caribbean Cash or the Third Party Provider, as applicable, reserves the right to charge a fee to provide such paper copy;</li>
<li>you may contact Caribbean Cash atinfo@caribbeancash.cwto update your registration information used for electronic Communications or to withdraw consent to receive electronic Communications; and</li>
<li>Caribbean Cash or the Third Party Provider reserves the right to terminate your use of &ldquo;Froggy&rdquo; and the associated Third Party Provider products and services if you decline or withdraw consent to receive electronic Communications.</li>
</ul>
</li>
</ol>
</li>
</ol>';
        return view('admin.country.index',$data);
    }

    public function store()
    {
        $this->validate(request(), Country::validationRules(request()->all()));
        $id = request('id');
        $country = Country::find($id);
        $inputs = request()->all();
        if ($country) {
            if (request()->hasFile('logo')) {
                if ($country->logo != '') {
                    Storage::delete(public_path('uploads/' . $country->logo));
                }
                $logo = time() . '_' . request()->file('logo')->getClientOriginalName();
                $path = request()->logo->move(public_path('uploads'), $logo);
                $inputs['logo'] = $logo;
            } else {
                if (request()->removeLogo != 'true') {
                    $inputs['logo'] = $country->logo;
                } else {
                    if ($country->logo != '') {
                        Storage::delete(public_path('uploads/' . $country->logo));
                        $inputs['logo'] = '';
                    }
                }
            }
            $country->update($inputs);
        } else {
            if (request()->hasFile('logo')) {
                $logo = time() . '_' . request()->file('logo')->getClientOriginalName();
                $path = request()->logo->move(public_path('uploads'), $logo);
                $inputs['logo'] = $logo;
            }
            Country::create($inputs);
        }
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }

    public function show(Country $country)
    {
        if ($country->logo) {
            $country->logo = asset('uploads/' . $country->logo);
        } else {
            $country->logo = '';
        }
        $filteredArr = [
            'id'             => ["type" => "hidden", 'value' => $country->id],
            'name'           => ["type" => "text", 'value' => $country->name],
            'country_code'   => ["type" => "text", 'value' => $country->country_code],
            'phone_length'   => ["type" => "text", 'value' => $country->phone_length],
            'valuta_name'    => ["type" => "text", 'value' => $country->valuta_name],
            'tax'            => ["type" => "text", 'value' => $country->tax],
            'logo'           => ["type" => "image", 'value' => $country->logo],
            'tax_percentage' => ["type" => "text", 'value' => $country->tax_percentage],
            'timezone'       => ["type" => "text", 'value' => $country->timezone],
            'map_link'       => ["type" => "text", 'value' => $country->map_link],
            'terms_eng'      => ["type" => "tinymce", 'value' => $country->terms_eng],
            'terms_esp'      => ["type" => "tinymce", 'value' => $country->terms_esp],
            'terms_pap'      => ["type" => "tinymce", 'value' => $country->terms_pap],
        ];
        return response()->json([
            "status" => "success",
            "inputs" => $filteredArr,
        ]);
    }

    public function indexDatatable()
    {
        $countries = Country::select('*');
        return DataTables::of($countries)
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' onclick='setEdit($data->id)' class='$iconClass'><i class='fa fa-pencil'></i></a>";
                $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteCountry' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' onclick='setEdit($data->id," . '"view"' . ")' class='$iconClass'><i class='fa fa-eye'></i></a>";
                if ($data->map_link != '') {
                    $html .= "<a href='" . $data->map_link . "' target='_blank' title='Map Link' class='$iconClass'><i class='fa fa-map-marker'></i></a>";
                }
                $html .= "</div>";
                return $html;
            })
            ->make();
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }

    public function territories(Country $country)
    {
        $data = [];

        $data['territories'] = UserTerritory::where('country_id', '=', $country->id)
            ->orderBy('title', 'asc')
            ->pluck('title', 'id');

        return $data;
    }

    public function info(Country $country)
    {
        $data = [];
        $data['country'] = $country;
        return $data;
    }

    public function branches(Country $country)
    {
        $data = [];

        $data['branches'] = Branch::where('country_id', '=', $country->id)
            ->orderBy('title', 'asc')
            ->pluck('title', 'id');

        return $data;
    }
}
