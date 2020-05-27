<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserStatus extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'title',
        'title_nl',
        'title_es',
    ];
    protected $table = 'user_status';

    public static function pluckListing($ids = [], $type = 'user')
    {
        $status = UserStatus::select('id', 'title', 'role');
        if ($type == 'merchant') {
            $status->whereIn('id', [1, 5]);
        }
        if (!empty($ids)) {
            $status->whereIn('id', $ids);
        }
        $status = $status->orderBy('title', 'asc')
            ->get();
        return $status->map(function ($item, $key) {
            $item->title = ucwords(strtolower($item->title));
            return $item;
        });
    }
}
