<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\LoanType;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class LoanTypeController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:super admin|client');
    }

    public function index()
    {
        $data = [];
        $data['countries'] = Country::pluck('name', 'id');
        $data['default_terms'] = '<p>&nbsp;</p>
<p class="western" style="margin-bottom: 0in;"><a name="_GoBack"></a><span style="font-size: xx-large;"><strong>LOAN AGREEMENT <br /></strong></span><span style="font-size: large;"><strong>TERMS &amp; CONDITIONS </strong></span></p>
<p class="western" style="margin-bottom: 0in;" align="CENTER">&nbsp;</p>
<p class="western" style="margin-bottom: 0in;" align="CENTER">&nbsp;</p>
<p class="western" style="margin-bottom: 0in;">These terms and conditions outline the rules and regulations for the use of Loans at Caribbean Cash Services N.V.</p>
<p class="western" style="margin-bottom: 0in;">&nbsp;</p>
<p class="western" style="margin-bottom: 0in;">By accessing this website we assume you accept these terms and conditions in full. Do not continue to use Caribbean Cash Services N.V.\'s lending services via Web-application &amp; Mobile Application FROGGY WALLET if you do not accept all of the terms and conditions stated on this Agreement and Promissory Note.</p>
<p class="western" style="margin-bottom: 0in;">&nbsp;</p>
<p class="western" style="margin-bottom: 0in;">The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and any or all Agreements:</p>
<p class="western" style="margin-bottom: 0in;">&nbsp;</p>
<ul>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;">&ldquo;<span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">Client&rdquo;, &ldquo;You&rdquo; and &ldquo;Your&rdquo; &rdquo;, &ldquo;Borrower&rdquo; and &ldquo;Debtor&rdquo; mean the undersigned customer(s) and all who have signed as a customer and/or co-maker. </span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;">&ldquo;<span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">The Company&rdquo;, &ldquo;Ourselves&rdquo;, &ldquo;We&rdquo;, &ldquo;Our&rdquo; and &ldquo;Us&rdquo; and &ldquo;Creditor&rdquo; means the Lender, Caribbean Cash Services NV. </span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;">&ldquo;<span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">Interest Rate&rdquo;, The Cost of your Credit that is charged as interest at a 0.46% per week.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;">&ldquo;<span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">Loan Amount&rdquo; The amount you are borrowing</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;">&ldquo;<span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">APR&rdquo; Annual Percentage Rate (27%) means the total costs of the consumer credit, expressed as an annual percentage of the total credit amount. It</span></span> <span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">is the effective rate of charge that reflects all the costs of the credit to the consumer over the duration of the credit agreement. </span></span></span></span></p>
</li>
</ul>
<p class="western" style="margin-bottom: 0in;">&nbsp;</p>
<p class="western" style="margin-bottom: 0in;"><span style="font-size: large;"><strong>PROMISSORY NOTE</strong></span></p>
<p class="western" style="margin-bottom: 0in;">&nbsp;</p>
<p class="western" style="margin-bottom: 0in;">The undersigned,</p>
<p class="western" style="margin-bottom: 0in;">&nbsp;</p>
<p class="western" style="margin-bottom: 0in;">[INSERT FULL NAME] Residing on Sint Maarten / Cura&ccedil;ao, bearer of I.D. NO. / Passport no. [ID number] hereinafter referred to as: (The Borrower), hereby acknowledges indebtedness to:</p>
<p class="western" style="margin-bottom: 0in;">&nbsp;</p>
<p class="western" style="margin-bottom: 0in;">CARIBBEAN CASH SERVlCES N.V., a Limited Liability Company with its offices on Sint Maarten and Cura&ccedil;ao, herein duly represented by its Director or duly authorized representative hereinafter referred to as: &ldquo;the Creditor&rdquo;.</p>
<p class="western" style="margin-bottom: 0in;">&nbsp;</p>
<p class="western" style="margin-bottom: 0in;">Of any amount received by the Debtor from the Creditor under the following conditions and stipulations:</p>
<p class="western" style="margin-bottom: 0in;">&nbsp;</p>
<ol>
<li>
<p style="margin-bottom: 0.21in; background: #ffffff; line-height: 100%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="color: #000000;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">Borrower is 18 years old or older and capable of entering into a legally binding agreement.</span></span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">The Borrower agrees and understands that this Loan Agreement and its Terms &amp; Conditions is legally binding on all extension of credit applied for by the Borrower and approved and credited by Caribbean Cash Services N.V. from this day forward.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">The Debtor will pay an Origination fee of 10% of the loan amount per each 7 (seven) days term the credit Agreement is applied and approved for. The Debtor agrees to pay the Origination Fee earned at the origination of the loan at the time of funding from Creditor to Debtor.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">The Renewal Fee for each 7 (seven) day term is 10% of the outstanding principle Loan amount. The Debtor declares to owe the Creditor this Renewal Fee and binds himself/herself to pay any Renewal Fee(s) to be determined by the Creditor for each renewed loan or future renewal of this loan or renewal thereof.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">The Debtor will pay an interest of 0.46% per each 7 (seven) days term on the amount received, which interest will be calculated from this date, until the debt has been paid in full. The Debtor agrees to pay Interest Fee earned at the origination of the loan at the time of funding from Creditor to Debtor.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">The Debtor declares to owe the Creditor the Loan Amount, Origination Fee, Renewal Fee &amp; Interest Fee and binds himself/herself to pay any Renewal Fee &amp; Interest Fee on the outstanding principle balance for each term of one week once the loan is in default of the term of this each approved credit agreement, until the debt has been paid in full. </span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">The Debtor agrees and understands that the Credit and Credit Term will be effective from the instant the loan has been approved and credited to their Froggy Mobile Wallet by the Creditor.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">The Debtor agrees and understands that the any Approved Loan Amount by Creditor will be exclusively credited to their Froggy Mobile Wallet Credit Balance.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">The Debtor agrees and understands that any Froggy Mobile Wallet Credit balance can be withdrawn in cash at any of our offices during office hours free of charge.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">As a security for repayment of this debt, the Debtor hereby assigns to the Creditor his/her wages to be paid by his/her current or any future employer. </span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">The debt, including interest and any additional Fees will be paid in full in one installment on or before the due date of each approved credit. All payments will be paid by the Debtor to the Creditor&rsquo;s place of business during normal business hours.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">Payments shall be credited first, to the Creditor&rsquo;s collection expenses &ndash; if any &ndash; next to interest, next to Renewal(s) Fee(s) charges, and the balance to the principal.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">This note may be prepaid at any time, without penalty or premium it being understood and agreed that the Debtor shall not be entitled, by virtue of any prepayment or otherwise to a refund of the Fee(s) or interest.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">It is clearly understood by Debtor that in case of his/her failure to make the payments referred to in article 6 above on the due date, the loan term will be automatically renewed for the allowable term(s), if applicable. Henceforth the loan will be considered in default and will be charged interest fees under the same conditions as the initial Note for each period of 7 days; the interest fee in the amount of 0.46% of the outstanding principle will automatically become due and payable by the Debtor to the Creditor which constitutes an overall effective rate of 26.95% per annum. At the end of each period of 7 days in which Debtor fails to pay all outstanding balances in full, Debtor authorizes the Creditor to automatically renew and accrue additional interest and Transaction(s) Fee(s) on the outstanding principle amount of the loan as described previously in this article.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">Notwithstanding the aforementioned, if any installment is not paid on the date due or whole remaining balance of the Principal sum and accrued interest will become immediately due and payable, without notice of such default becoming necessary. The Creditor has the right to set off its claim under this note with the wages from the Debtor which was assigned as a security for repayment of the Note.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">In the event of default, whenever this Note is placed in the hands of an Attorney or Collection Agency for collection, the Debtor will bear all costs incurred by the Creditor including legal fees, court costs, and collection charges. </span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">Debtor acknowledges that this loan is being made based upon the documentation and information supplied to Creditor by Debtor and Debtor warrants and represents that all such documents and information are true and accurate and belong to the Debtor. Debtor understands and agrees that any false documentation or information given to Creditor in order to induce Creditor to extend credit based upon forged or altered documents is a deceptive practice calculated to illegally obtain funds from the Creditor. </span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">Any forbearance by the Creditor in exercising any right of remedy hereunder or any other Note or instrument in connection with this loan or otherwise afforded by applicable law, shall not be a waiver or preclude the exercise of any right or remedy by the Creditor. The acceptance by the Creditor of any sum payable hereunder after the due date(s) of such payment(s) shall not be a waiver of the right(s) of the Creditor to require prompt payment when due of all other sums payable hereunder or to declare a default for failure to make prompt payment.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">The Debtor declares to understand the consequences of the foregoing stipulations and conditions. The Debtor declares that he/she is not relying on the representations of Caribbean Cash Services N.V. or its employees as to the content of this Note. The Debtor declares not to dispute the validity of the stipulations of this note in a Court of Law and furthermore binds himself/herself not to nullify this note.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">If this Loan Agreement and Promissory Note is executed by a Co-Maker or Guarantor below, the Co-Maker or Guarantor guarantees the performance of the Debtor under this Agreement and Promissory Note and the Co-Maker or Guarantor agrees to be fully bound by all the terms and conditions herein in the case of default or non-payment as if he were the Debtor hereunder. In case of default or non-payment, the Creditor may pursue payment from the Co-Maker or Guarantor by any means available to the Creditor without regard to any continued collection activity with the original Debtor.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">The Lender will credit payments made in Netherlands Antilles Florins (Guilder) at a rate of NAf. 1.78 to USD $1.00 and vice versa. </span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">This note is subject to the laws of the jurisdiction where executed. Insofar as the application of the laws renders any provisions of this Note to be invalid or null, only such provision shall be rendered invalid or null and the other provisions of this Note will remain in full force and effect.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">The Debtor, by agreeing to these Terms &amp; Conditions, represents and warranties to Caribbean Cash Services N.V. that the total payments owed to any Lender by the Borrower in the aggregate including but not limited to banks for car loans, equity loans, home mortgages, credit card debt, etc. does not exceed thirty seven percent (&ldquo;37%&rdquo;) of their gross monthly&nbsp; income and that the Debtor has fully and completely disclosed all such obligations &nbsp;in excess of the stated percentage to Caribbean Cash Services N.V. such that Caribbean Cash Services N.V. is able to insure compliance with the regulations and policies of the Central Bank of Curacao and Sint. Maarten regarding such provisions on overextension of credit to the Debtors and Caribbean Cash Services NV is relying on such full disclosure by the Borrower in making its determination of the Debtor\'s qualification for any extension of credit to the Debtor.</span></span></span></span></p>
</li>
<li>
<p style="margin-bottom: 0.11in; line-height: 107%;"><span style="font-family: Calibri, serif;"><span style="font-size: small;"><span style="color: #000000;"><span style="font-family: \'Times New Roman\', serif;"><span style="font-size: medium;">Caribbean Cash Services N.V. has the right to change or add to the terms of this Agreement at any time, and to change, delete, discontinue, or impose conditions on any feature or aspect of the Lending Services with notice that we in our sole discretion deem to be reasonable in the circumstances, including such notice on our website or in the App. Any use of our Lending Services after our publication of any such changes shall constitute your acceptance of this Agreement as modified. However, any credit extended before the modification shall be governed by the Agreement that was in place when the loan was extended.</span></span></span></span></span></p>
</li>
</ol>
<p class="western" style="margin-bottom: 0in;">&nbsp;</p>
<p class="western" style="margin-left: 1.5in; text-indent: -1.5in; margin-bottom: 0in;">In English: By agreeing to these terms &amp; conditions, I certify that I understand the contents of this document written in English.</p>
<p class="western" style="margin-left: 1.5in; text-indent: -1.5in; margin-bottom: 0in;"><span lang="es-CO">En Papiamento: Aseptando e termino- i kondishonan ta sertifik&aacute; ku mi ta komprend&eacute; kontenido di e dokumento ak&iacute;, skirb&iacute; na ingles.</span></p>
<p class="western" style="margin-left: 1.5in; text-indent: -1.5in; margin-bottom: 0in;"><span lang="nl-NL">In Nederlanse: Door akkoord te gaan met deze algemene voorwaarden verklaar ik, dat ik de inhoud van dit document in het Engels geschreven begrijp.</span></p>
<p class="western" style="margin-left: 1.5in; text-indent: -1.5in; margin-bottom: 0in;"><span lang="es-CO">En Espa&ntilde;ol: Al aceptar estos t&eacute;rminos y condiciones certifico que entiende el contenido de este documento escrito en Ingl&eacute;s.</span></p>
<p class="western" lang="es-CO" style="margin-bottom: 0in;">&nbsp;</p>
<p class="western" lang="es-CO" style="margin-bottom: 0in;">&nbsp;</p>
<p class="western" style="margin-bottom: 0in;">&nbsp;</p>';
        return view('admin.loantype.index', $data);
    }

    public function getList()
    {
        $loanType = LoanType::select([
            'id',
            'title',
            'title_es',
            'title_nl',
        ]);
        return DataTables::of($loanType)
            ->addColumn('action', function ($data) {
                $iconClass = "btn btn-icon btn-sm btn-info waves-effect";
                $html = '<div class="button-list">';
                $html .= "<a href='javascript:;' title='Edit' onclick='setEdit($data->id)' class='$iconClass'><i class='fa fa-pencil'></i></a>";
                $html .= "<a href='javascript:;' title='Delete' data-modal-id='deleteLoanType' data-id='$data->id' onclick='DeleteConfirm(this)' class='$iconClass'><i class='fa fa-trash'></i></a>";
                $html .= "<a href='javascript:;' title='View' onclick='setEdit($data->id," . '"view"' . ")' class='$iconClass'><i class='fa fa-eye'></i></a>";
                $html .= "</div>";
                return $html;
            })
            ->make();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            "title" => 'required',
        ]);
        $id = $request->id;
        $loanType = LoanType::find($id);
        if ($loanType) {
            $inputs = $request->except('territory_id');
            $loanType->update($inputs);
        } else {
            $inputs = $request->except('territory_id');
            $loanType = LoanType::create($inputs);
        }
        $loanType->associated_territories()->sync(request('territory_id'));
        return response()->json([
            "status"  => "success",
            "message" => "Saved successfully.",
        ]);
    }

    public function show($id)
    {
        $loanType = LoanType::find($id);
        $filteredArr = [
            'id'                         => ["type" => "hidden", 'value' => $loanType->id],
            'title'                      => ["type" => "text", 'value' => $loanType->title],
            'title_nl'                   => ["type" => "text", 'value' => $loanType->title_nl],
            'title_es'                   => ["type" => "text", 'value' => $loanType->title_es],
            'minimum_loan'               => ["type" => 'text', 'value' => intval($loanType->minimum_loan)],
            'maximum_loan'               => ["type" => 'text', 'value' => intval($loanType->maximum_loan)],
            'unit'                       => ["type" => 'text', 'value' => intval($loanType->unit)],
            'loan_component'             => ["type" => 'text', 'value' => $loanType->loan_component],
            'apr'                        => ["type" => 'text', 'value' => $loanType->apr],
            'origination_type'           => ["type" => 'select', 'value' => $loanType->origination_type],
            'origination_amount'         => ["type" => 'text', 'value' => $loanType->origination_amount],
            'number_of_days'             => ["type" => 'text', 'value' => $loanType->number_of_days],
            'interest'                   => ["type" => 'text', 'value' => $loanType->interest],
            'cap_period'                 => ["type" => 'text', 'value' => $loanType->cap_period],
            'renewal_type'               => ["type" => 'select', 'value' => $loanType->renewal_type],
            'renewal_amount'             => ["type" => 'text', 'value' => $loanType->renewal_amount],
            'debt_type'                  => ["type" => 'select', 'value' => $loanType->debt_type],
            'debt_amount'                => ["type" => 'text', 'value' => $loanType->debt_amount],
            'debt_collection_type'       => ["type" => 'select', 'value' => $loanType->debt_collection_type],
            'debt_collection_percentage' => ["type" => 'text', 'value' => $loanType->debt_collection_percentage],
            'debt_collection_tax_type'   => ['type' => 'select', 'value' => $loanType->debt_collection_tax_type],
            'debt_collection_tax_value'  => ['type' => 'text', 'value' => $loanType->debt_collection_tax_value],
            'debt_tax_type'              => ["type" => 'select', 'value' => $loanType->debt_tax_type],
            'debt_tax_amount'            => ["type" => 'text', 'value' => $loanType->debt_tax_amount],
            'loan_agreement_eng'         => ["type" => "tinymce", 'value' => $loanType->loan_agreement_eng],
            'loan_agreement_esp'         => ["type" => "tinymce", 'value' => $loanType->loan_agreement_esp],
            'loan_agreement_pap'         => ["type" => "tinymce", 'value' => $loanType->loan_agreement_pap],
            'country_id'                 => [
                'type'      => 'select-territory-multiple',
                'value'     => $loanType->country_id,
                'territory' => $loanType->associated_territories->pluck('id')
            ],
            'territory_id[]'             => [
                "type"  => 'select-multiple',
                'value' => $loanType->associated_territories->pluck('id')
            ],
            'status'                     => ["type" => 'radio', 'checkedValue' => $loanType->status],
        ];
        return response()->json([
            "status" => "success",
            "inputs" => $filteredArr,
        ]);
    }

    public function destroy($id)
    {
        $loanType = LoanType::find($id);
        $loanType->delete();
        return response()->json([
            "status"  => "success",
            "message" => "Deleted successfully.",
        ]);
    }
}
