<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\App;

class LoanReason extends Model
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

    public static function getAllReasons($lang = null)
    {
        if ($lang == null) {
            $lang = App::getLocale();
        }
        $loanReasons = [];
        if ($lang == 'esp') {
            $loanReasons = LoanReason::orderBy('title_es', 'asc')
                ->whereNotNull('title_es')
                ->select('title_es as title', 'id');
        } elseif ($lang == 'pap') {
            $loanReasons = LoanReason::orderBy('title_nl', 'asc')
                ->whereNotNull('title_nl')
                ->select('title_nl as title', 'id');
        } else {
            $loanReasons = LoanReason::orderBy('title', 'asc')
                ->whereNotNull('title')
                ->select('title', 'id');
        }
        return $loanReasons->get();
    }
}
