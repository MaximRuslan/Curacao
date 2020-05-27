<?php

namespace App\Models;

use App\Library\Helper;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{
    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            if (auth()->check()) {
                $model->created_by = auth()->user()->id;
            }
            if (Helper::authMerchant()) {
                $model->created_by = Helper::authMerchantUser()->id;
            }
        });

        self::updating(function ($model) {
            if (auth()->check()) {
                $model->updated_by = auth()->user()->id;
            }
            if (Helper::authMerchant()) {
                $model->updated_by = Helper::authMerchantUser()->id;
            }
        });

        self::deleting(function ($model) {
            if (auth()->check()) {
                // $model->deleted_by = auth()->user()->id;
            }
        });
    }
}
