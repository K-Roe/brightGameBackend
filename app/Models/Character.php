<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Character extends Model
{
    protected $fillable = [
        'user_id', 'name', 'hero_class', 'str', 'dex', 'agi', 'int', 
        'height', 'starting_weight', 'current_weight', 'bmi', 'level', 'xp'
    ];

    /**
     * Relationship: A character belongs to a User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship: A character has many Items (Inventory).
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Eloquent Lifecycle Hooks (The Doctrine "Lifecycle Listeners" equivalent)
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($character) {
            if ($character->height > 0 && $character->current_weight > 0) {
                // Formula: weight (kg) / [height (m)]²
                $heightInMeters = $character->height > 3 ? $character->height / 100 : $character->height;
                $character->bmi = $character->current_weight / ($heightInMeters ** 2);
            }
        });
    }

    /**
     * Scopes: Pro-tip for clean RPG logic.
     * Use this to quickly find characters by class.
     * Example: Character::warriors()->get();
     */
    public function scopeWarriors($query)
    {
        return $query->where('hero_class', 'Warrior');
    }

    /**
     * Accessor: Useful for UI display.
     * This adds a virtual property 'bmi_status'.
     */
    public function getBmiStatusAttribute(): string
    {
        if ($this->bmi < 18.5) return 'Underweight';
        if ($this->bmi < 25) return 'Healthy';
        if ($this->bmi < 30) return 'Overweight';
        return 'Obese';
    }

    
    public function gainExperience(int $amount)
    {
        $this->xp += $amount;

        // Simple RPG logic: Level up every 1000 XP
        if ($this->xp >= $this->level * 1000) {
            $this->levelUp();
        }

        $this->save();
    }

    protected function levelUp()
    {
        $this->level++;
        // boost stats automatically
        $this->str += 1;
        $this->int += 1;
        $this->agi += 1;
        $this->dex += 1;
        // Reset XP
        $this->xp = 0; 
    }
}