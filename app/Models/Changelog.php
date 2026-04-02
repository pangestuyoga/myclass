<?php

namespace App\Models;

use App\Enums\ChangelogType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Changelog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'version',
        'release_date',
        'title',
        'changes',
        'type',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'release_date' => 'date',
        'changes' => 'array',
        'type' => ChangelogType::class,
    ];

    public function formattedVersion(): Attribute
    {
        return Attribute::get(fn () => strtoupper($this->version));
    }

    public function formattedReleaseDate(): Attribute
    {
        return Attribute::get(fn () => $this->release_date->translatedFormat('l, d M Y'));
    }
}
