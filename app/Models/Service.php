<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    public function getImageAttribute($service)
    {
        if($service)
            return asset('public/images/service/'.$service);
        else
            return asset('public/images/system/peguitas.png');
    }

    public function totalVendors($sevice_id){
        return \App\Models\VendorService::where('service_id',$sevice_id)->count();
    }
}
