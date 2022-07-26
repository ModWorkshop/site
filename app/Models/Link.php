<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Link extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $hidden = ['mod'];
    protected $with = ['user'];

    public function mod() : BelongsTo
    {
        return $this->belongsTo(Mod::class);
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted() {
        static::deleting(function (Link $link)
        {
            $mod = $link->mod;
            
            if ($mod->download_type === Link::class && $mod->download_id === $link->id) {
                $mod->download_id = null;
                $mod->download_type = null;
                $mod->save();   
            }
        });
    }
}
