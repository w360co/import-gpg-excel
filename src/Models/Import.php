<?php

namespace W360\ImportGpgExcel\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Import extends Model
{
      use HasFactory;

      protected $guarded = [];

      public function model()
      {
         return $this->morphTo();
      }
}