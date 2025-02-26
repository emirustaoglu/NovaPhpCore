<?php

namespace App\Models;

use NovaCore\Database\Model;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // İlişki örnekleri
    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }

    // Scope örneği
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Accessor örneği
    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->surname}";
    }

    // Mutator örneği
    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = strtolower($value);
    }
}
