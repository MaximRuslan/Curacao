<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class ExistingLoanType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'title_nl',
        'title_es',
    ];

    public static function validationRules()
    {
        return [
            'title'    => 'required',
            'title_es' => 'required',
            'title_nl' => 'required',
        ];
    }

    public static function getType($lang = null)
    {
        if ($lang == null) {
            $lang = App::getLocale();
        }
        $loanTypes = [];
        if ($lang == 'esp') {
            $loanTypes = ExistingLoanType::orderBy('title_es', 'asc')
                ->whereNotNull('title_es')
                ->select('title_es as title', 'id')
                ->get();
        } elseif ($lang == 'pap') {
            $loanTypes = ExistingLoanType::orderBy('title_nl', 'asc')
                ->whereNotNull('title_nl')
                ->select('title_nl as title', 'id')
                ->get();
        } else {
            $loanTypes = ExistingLoanType::orderBy('title', 'asc')
                ->whereNotNull('title')
                ->select('title', 'id')
                ->get();
        }
        return $loanTypes;
    }
}
