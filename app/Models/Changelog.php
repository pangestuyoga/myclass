<?php

namespace App\Models;

use App\Enums\ChangelogType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Changelog extends Model
{
    // --- Traits ---

    use HasFactory, SoftDeletes;

    // --- Properties ---

    protected $fillable = [
        'version',
        'release_date',
        'title',
        'changes',
        'type',
        'description',
    ];

    // --- Casts ---

    protected $casts = [
        'changes' => 'array',
        'release_date' => 'date',
        'type' => ChangelogType::class,
    ];

    // --- Accessors & Mutators ---

    protected function formattedVersion(): Attribute
    {
        return Attribute::get(fn () => strtoupper($this->version));
    }

    protected function formattedReleaseDate(): Attribute
    {
        return Attribute::get(fn () => $this->release_date->translatedFormat('l, d M Y'));
    }

    protected function formattedCreatedAt(): Attribute
    {
        return Attribute::get(fn () => $this->created_at->translatedFormat('l, d M Y H:i'));
    }

    protected function formattedUpdatedAt(): Attribute
    {
        return Attribute::get(fn () => $this->updated_at?->translatedFormat('l, d M Y H:i'));
    }
}
