<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorService extends Model
{
    public function service(){
         return $this->hasOne('App\Models\Service','id','service_id');
    }

    public function categoryService(){
        return $this->hasOne('App\Models\CategoryService','service_id','service_id');
    }

    public function vendor(){
        return $this->hasOne('App\User','id','vendor_id');
    }

}
