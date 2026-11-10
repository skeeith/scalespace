<?php

namespace App\Models;

use App\Traits\FilterRelationships;
use App\Traits\Filtering;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use Filtering;
    use FilterRelationships;
    /** @use HasFactory<\Database\Factories\ModuleFactory> */
    use HasFactory;
    use HasUuids;

    /**
     * Modules table.
     *
     * @var string
     */
    protected $table = 'modules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];
}
