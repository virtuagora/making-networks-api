<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Term extends Model
{
    protected $table = 'terms';
    protected $visible = [
        'id', 'name', 'data', 'localization', 'count',
    ];
    protected $fillable = [
        'id', 'name', 'data', 'localization',
    ];
    protected $casts = [
        'data' => 'array',
        'localization' => 'array',
    ];

    public function taxonomy()
    {
        return $this->belongsTo('App\Model\Taxonomy');
    }

    public function groups()
    {
        return $this->morphedByMany('App\Model\Group', 'object', 'term_object')->withTimestamps();
    }
}
