<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitPeople extends Model
{
    use HasFactory;

          protected $hidden = [
              'id_unit'
          ];

    public $timestamps = false;
    public $table = 'unitpeoples';
}
