<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UserOperation extends Model
{
    use HasFactory;
    
    protected $table = 'user_operations';

    protected $fillable = [
        'user_id',
        'category_id',
        'currency_id',
        'total',
        'description',
        'operation_date'
    ];

    public function user(){
        return $this->hasMany(User::class, 'id', 'user_id');
    }

    public function category(){
        return $this->hasMany(Category::class, 'id', 'category_id');
    }

    public function currency(){
        return $this->hasMany(Currency::class, 'id', 'currency_id');
    }

}
