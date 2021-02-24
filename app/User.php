<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\Product;

class User extends Authenticatable
{
    use Notifiable,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','updated_at','deleted_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getProfileImageAttribute($profileImage){
        if($profileImage)
          return asset('images/profile/'.$profileImage);
        else
          return asset('images/system/profile-image-not-available.jfif');
    }

    public function totalBookings($vendor_id = null){
       return \App\Models\Booking::where(function($query) use ($vendor_id){
           if($vendor_id)
              $query->where('vendor_id',$vendor_id);
       })->count();
    }

    public function avgRating(){
        $rating = \App\Models\Rating::join('bookings','rating_reviews.booking_id','=','bookings.id')->where('bookings.vendor_id',$this->id)->avg('rating');
        return $rating ? number_format($rating,1) : '0.0';
     }

     public function isPlanActive($id = null){
           if(is_null($id)){
               $id = $this->id;
           }
           $plan = \DB::table('user_plans')->where('user_id',$id)->whereDate('plan_expiry_date','>=',date('Y-m-d'))->orderBy('id','desc')->first();
           if($plan){
              return true;
           }
              return false;
     }

     public function currentPlan($id = null){
        if(is_null($id)){
            $id = $this->id;
        }
        $plan = \DB::table('user_plans')->where('user_id',$id)->orderBy('id','desc')->first();
        if($plan){
           return [
             'id'        => $plan->id,
             'plan_id'   => $plan->plan_id,
             'title'     => $plan->title,
             'request_day' => $plan->request_day,
             'plan_active_date' => date('d, M Y',strtotime($plan->plan_active_date)),
             'plan_expiry_date' => date('d, M Y',strtotime($plan->plan_expiry_date)),
             'is_active_plan'   => strtotime($plan->plan_expiry_date) >= strtotime(date('Y-m-d')) ? '1' : '0',
           ];
        }
           return array();
  }

}
