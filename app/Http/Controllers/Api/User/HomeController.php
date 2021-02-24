<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Service;
use App\Models\CategoryService;
use App\Models\VendorService;
use App\Models\Booking;
use App\Models\Chat;
use App\Models\Notification;
use App\User;
use DB;
use Validator;

class HomeController extends Controller
{

    /**
     *  Is User Exist
    */
    public function isUserExist($userId){
        $User = DB::table('users')->where('id',$userId)->where('role_id','3')->whereNull('deleted_at')->count();
        if($User)
          return true;
        else
          return false;
      }

    /**
    * End Point  :- user/get/categories
    * Method     :- Get
    */
    public function getCategories(Request $request){
        $categories = Category::where('is_active','1')->whereNull('deleted_at')->get();
        if($categories->toarray())
          return ['status'=>true,'message'=>__('Record found'),'data'=>$categories];
        else
          return ['status'=>false,'message'=>__('Record not found')];
    }

    /**
    * End Point  :- user/get/services
    * Method     :- Get
    * Parameter  :- category_id
    */
    public function getServices(Request $request){
        $services = CategoryService::where(function($query) use ($request){
           if($request->category_id){
               $query->where('category_id',$request->category_id);
           }
        })->get();
        if($services->toarray()){
            $data = array();
            foreach($services as $key => $value){
              array_push($data,[
                  'category_id' => $value->category_id,
                  'service_id'  => $value->service_id,
                  'image'       => $value->service->image,
                  'service'     => $value->service->title,
                  'category'    => $value->category->title,
                  'total_vendors'  => $value->service->totalVendors($value->service_id)
              ]);
            }
            return ['status'=>true,'message'=>__('Record found'),'data'=>$data];
        }else{
            return ['status'=>false,'message'=>__('Record not found')];
        }
    }

    /**
    * End Point  :- user/get/vendors
    * Method     :- Get
    * Parameter  :- category_id,user_id,service_id,vendor_id,search
    */
     public function getVendors(Request $request){
        $vendors = VendorService::where(function($query) use ($request){
            if($request->category_id)
                $query->where('category_id',$request->category_id);

            if($request->service_id)
            $query->where('service_id',$request->service_id);

        })->orderBy('service_cost','asc')->get();

        if($vendors->toArray()){
            $data = array();
            foreach($vendors as $key => $value){
                array_push($data,[
                    'vendor_id'   => $value->vendor_id,
                    'vendor_name' => $value->vendor->name,
                    'category_id' => $value->categoryService->category_id,
                    'service_id'  => $value->categoryService->service_id,
                    'image'       => $value->vendor->profile_image,
                    'category'    => $value->categoryService->category->title,
                    'service'     => $value->categoryService->service->title,
                    'total_jobs'  => $value->vendor->totalBookings(),
                    'rating'      => $value->vendor->avgRating(),
                    'rate'        => $value->service_cost,
                ]);
            }
            return ['status'=>true,'message'=>__('Record found'),'data'=>$data];
        }
        return ['status'=>false,'message'=>__('Record not found')];
     }

    /**
    * End Point  :- user/get/vendor/details
    * Method     :- Get
    * Parameter  :- vendor_id,user_id
    */
     public function getVendorDetails(Request $request){

        $input = $request->all();

        $rules = [
          'user_id'    => 'required',
          'vendor_id'  => 'required',
        ];
  
        $validator = Validator::make($request->all(), $rules );
  
        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }
  
        $isUserExist = $this->isUserExist($input['user_id']);
  
        if(!$isUserExist)
             return response(['status'=>false,'message'=>__('This user does not exist')],401);
  
         $User = User::find($input['user_id']);
  
         if($User->is_active != '1'){
            return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
         }

        $vendors = VendorService::where(function($query) use ($request){
            if($request->category_id)
                $query->where('category_id',$request->category_id);

            if($request->service_id)
            $query->where('service_id',$request->service_id);

        })->where('vendor_id',$input['vendor_id'])->get();

        if($vendors->toArray()){
            $data = array();
            foreach($vendors as $key => $value){
                $history = Booking::where('vendor_id',$value->vendor_id)->get();
                array_push($data,[
                    'vendor_id'   => $value->vendor_id,
                    'category_id' => $value->categoryService->category_id,
                    'service_id'  => $value->categoryService->service_id,
                    'image'       => $value->vendor->profile_image,
                    'category'    => $value->categoryService->category->title,
                    'service'     => $value->categoryService->service->title,
                    'total_jobs'  => $value->vendor->totalBookings(),
                    'rating'      => $value->vendor->avgRating(),
                    'rate'        => $value->service_cost,
                    'bio'         => $value->vendor->bio,
                    'history'     => $history ?? array()
                ]);
            }
            return ['status'=>true,'message'=>__('Record found'),'data'=>$data[0]];
        }
        return ['status'=>false,'message'=>__('Record not found')];
     }

    /**
    * End Point  :- user/book/service
    * Method     :- Post
    * Parameters :- user_id,vendor_id,service_id,booking_date,card_holder_name,card_number,card_expiry_month,card_expiry_year,card_cvv
    *
    */
    public function bookService(Request $request){
        
        $input = $request->all();

        $rules = [
          'user_id'      => 'required',
          'vendor_id'    => 'required',
          'service_id'   => 'required',
          'booking_date' => 'required',
          'card_holder_name'  => 'required',
          'card_number'       => 'required',
          'card_expiry_month' => 'required',
          'card_expiry_year'  => 'required',
          'card_cvv'          => 'required',
        ];
  
        $validator = Validator::make($request->all(), $rules );
  
        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }
  
        $isUserExist = $this->isUserExist($input['user_id']);
  
        if(!$isUserExist)
             return response(['status'=>false,'message'=>__('This user does not exist')],401);
  
         $User = User::find($input['user_id']);
  
         if($User->is_active != '1'){
            return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
         }

         $vendorService = VendorService::where('vendor_id',$input['vendor_id'])->where('service_id',$input['service_id'])->first();
         $insertData = [
            'user_id'       => $input['user_id'],
            'vendor_id'     => $input['vendor_id'],
            'service_id'    => $input['service_id'],
            'category_id'   => $vendorService->categoryService->category_id,
            'service_cost'  => $vendorService->service_cost,
            'service_name'  => $vendorService->categoryService->service->title,
            'category_name' => $vendorService->categoryService->category->title,
            'booking_date'  => date('Y-m-d', strtotime($input['booking_date'])),
            'booking_time'  => $input['booking_time'] ?? NUll
         ];

         $insetId = Booking::insertGetId($insertData);

         if($insetId)
           return ['status'=>true,'message'=>__('Thank you for booked service')];
         else
           return ['statua'=>false,'message'=>__('Failed to book service')];
         
    }

    /**
    * End Point  :- user/get/bookings
    * Method     :- Get
    * Parameters :- user_id
    *
    */
    public function getBookings(Request $request){
        $input = $request->all();

        $rules = [
          'user_id'      => 'required'
        ];
  
        $validator = Validator::make($request->all(), $rules );
  
        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }
  
        $isUserExist = $this->isUserExist($input['user_id']);
  
        if(!$isUserExist)
             return response(['status'=>false,'message'=>__('This user does not exist')],401);
  
         $User = User::find($input['user_id']);
  
         if($User->is_active != '1'){
            return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
         }

         $bookings = Booking::where('user_id',$input['user_id'])->where(function($query) use($request){
             if($request->booking_status){
                 $query->where('booking_status',$request->booking_status);
             }
         })->whereNull('deleted_at')->orderBy('id','desc')->get();

         if($bookings->toArray()){
             $data = array();
             foreach($bookings as $key => $value){
                  array_push($data,[
                      'booking_id'     => $value->id,
                      'vendor_id'      => $value->vendor_id,
                      'vendor_name'    => $value->vendor->name,
                      'vendor_profile' => $value->vendor->profile_image,
                      'service_id'     => $value->service_id,
                      'service'        => $value->service_name,
                      'category_id'    => $value->category_id,
                      'category'       => $value->category_name,
                      'service_cost'   => $value->service_cost,
                      'booking_date'   => date('Y-m-d',strtotime($value->booking_date)),
                      'booking_time'   => $value->booking_time,
                      'booking_status' => $value->booking_status,
                      'payment_status' => $value->payment_status,
                      'created_at'      => date('Y-m-d',strtotime($value->created_at)),
                 ]);
             }
             return ['status'=>true,'message'=>__('Record found'),'data'=>$data];
         }
         return ['status'=>false,'message'=>__('Record not found')];
    }

    /**
    * End Point  :- user/get/booking/details
    * Method     :- Get
    * Parameters :- user_id,booking_id
    *
    */
    public function getBookingDetails(Request $request){
        $input = $request->all();

        $rules = [
          'user_id'      => 'required'
        ];
  
        $validator = Validator::make($request->all(), $rules );
  
        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }
  
        $isUserExist = $this->isUserExist($input['user_id']);
  
        if(!$isUserExist)
             return response(['status'=>false,'message'=>__('This user does not exist')],401);
  
         $User = User::find($input['user_id']);
  
         if($User->is_active != '1'){
            return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
         }

         $bookings = Booking::where('user_id',$input['user_id'])->where(function($query) use($request){
             if($request->booking_status){
                 $query->where('booking_status',$request->booking_status);
             }
         })->whereNull('deleted_at')->where('id',$input['booking_id'])->get();

         if($bookings->toArray()){
             $data = array();
             foreach($bookings as $key => $value){
                  array_push($data,[
                      'booking_id'     => $value->id,
                      'vendor_id'      => $value->vendor_id,
                      'vendor_name'    => $value->vendor->name,
                      'vendor_profile' => $value->vendor->profile_image,
                      'service_id'     => $value->service_id,
                      'service'        => $value->service_name,
                      'category_id'    => $value->category_id,
                      'category'       => $value->category_name,
                      'service_cost'   => $value->service_cost,
                      'booking_date'   => date('Y-m-d',strtotime($value->booking_date)),
                      'booking_time'   => $value->booking_time,
                      'booking_status' => $value->booking_status,
                      'payment_status' => $value->payment_status,
                      'created_at'      => date('Y-m-d',strtotime($value->created_at)),
                      'status_history'  => $value->statusHistory,
                 ]);
             }
             return ['status'=>true,'message'=>__('Record found'),'data'=>$data[0]];
         }
         return ['status'=>false,'message'=>__('Record not found')];
    }

    /**
    * End Point  :- user/cancel/booking
    * Method     :- Post
    * Parameters :- user_id,booking_id,cancel_reason(Optinal)
    *
    */
    public function cancelBooking(Request $request){
        $input = $request->all();

        $rules = [
          'user_id'      => 'required',
          'booking_id'   => 'required'
        ];
  
        $validator = Validator::make($request->all(), $rules );
  
        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }
  
        $isUserExist = $this->isUserExist($input['user_id']);
  
        if(!$isUserExist)
             return response(['status'=>false,'message'=>__('This user does not exist')],401);
  
         $User = User::find($input['user_id']);
  
         if($User->is_active != '1'){
            return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
         }

         $updateData = [
            'booking_status' => '2'
         ];
            DB::beginTransaction();
         try{
            DB::table('bookings')->where('id',$input['booking_id'])->where('user_id',$input['user_id'])->update($updateData);
            DB::table('booking_status_histories')->insert([
                'user_id'    =>$input['user_id'],
                'booking_id' =>$input['booking_id'],
                'user_id'    =>$input['user_id'],
                'status'     => '2',
                'cancel_reason' => $input['cancel_reason'] ?? null
            ]);
            DB::commit();
            return ['status'=>true,'message'=>__('Successfully cancelled booking')];
         }catch(\Exception $e){
             DB::rollback();
             return ['status'=>false,'message'=>__('Failed to cancel booking')];
         }

    }

    /**
    * End Point  :- user/get/booking/history
    * Method     :- Post
    * Parameters :- user_id
    *
    */
    public function getBookingHistory(Request $request){
        $input = $request->all();

        $rules = [
          'user_id'      => 'required'
        ];
  
        $validator = Validator::make($request->all(), $rules );
  
        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }
  
        $isUserExist = $this->isUserExist($input['user_id']);
  
        if(!$isUserExist)
             return response(['status'=>false,'message'=>__('This user does not exist')],401);
  
         $User = User::find($input['user_id']);
  
         if($User->is_active != '1'){
            return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
         }

         $bookings = Booking::where('user_id',$input['user_id'])
                                 ->whereIn('booking_status',['2'])
                                 ->whereNull('deleted_at')
                                 ->orderBy('id','desc')
                                 ->get();

         if($bookings->toArray()){
             $data = array();
             foreach($bookings as $key => $value){
                  array_push($data,[
                      'booking_id'     => $value->id,
                      'vendor_id'      => $value->vendor_id,
                      'vendor_name'    => $value->vendor->name,
                      'vendor_profile' => $value->vendor->profile_image,
                      'service_id'     => $value->service_id,
                      'service'        => $value->service_name,
                      'category_id'    => $value->category_id,
                      'category'       => $value->category_name,
                      'service_cost'   => $value->service_cost,
                      'booking_date'   => date('Y-m-d',strtotime($value->booking_date)),
                      'booking_time'   => $value->booking_time,
                      'booking_status' => $value->booking_status,
                      'payment_status' => $value->payment_status,
                      'created_at'      => date('Y-m-d',strtotime($value->created_at)),
                 ]);
             }
             return ['status'=>true,'message'=>__('Record found'),'data'=>$data];
         }
         return ['status'=>false,'message'=>__('Record not found')];
    }

    /**
    * End Point  :- user/give/rating
    * Method     :- Post
    * Parameters :- user_id,booking_id,rating,review
    */
    public function giveRating(Request $request){
        $input = $request->all();

        $rules = [
          'user_id'      => 'required',
          'booking_id'   => 'required',
          'rating'       => 'required'
        ];
  
        $validator = Validator::make($request->all(), $rules );
  
        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }
  
        $isUserExist = $this->isUserExist($input['user_id']);
  
        if(!$isUserExist)
             return response(['status'=>false,'message'=>__('This user does not exist')],401);
  
         $User = User::find($input['user_id']);
  
         if($User->is_active != '1'){
            return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
         }
            DB::beginTransaction();
         try{
             $insertData = [
                'user_id'    => $input['user_id'],
                'booking_id' => $input['booking_id'],
                'rating'     => $input['rating'] ?? '1',
                'review'     => $input['review'] ?? NULL,
             ];
             DB::table('rating_reviews')
                    ->where('user_id',$input['user_id'])
                    ->where('booking_id',$input['booking_id'])
                    ->update(['deleted_at'=>date('Y-m-d H:i:s')]);
             DB::table('rating_reviews')->insert($insertData);
             DB::commit();
             return ['status'=>true,'message'=>__('Thank you to give your feedback')];
         }catch(\Exception $e){
             DB::rollback();
             return ['status'=>false,'message'=>__('Failed to give rating')];
         }

    }

    /**
    * End Point  :- user/get/notifications
    * Method     :- Get
    * Parameters :- user_id
    */
    public function getNotifications(Request $request){
        $input = $request->all();

        $rules = [
          'user_id'      => 'required'
        ];
  
        $validator = Validator::make($request->all(), $rules );
  
        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }
  
        $isUserExist = $this->isUserExist($input['user_id']);
  
        if(!$isUserExist)
             return response(['status'=>false,'message'=>__('This user does not exist')],401);
  
         $User = User::find($input['user_id']);
  
         if($User->is_active != '1'){
            return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
         }

         $notifications = Notification::where('receiver_id',$input['user_id'])->get();

         if($notifications->toarray()){
            $data = array();
            foreach($notifications as $key => $value){
                array_push($data,[
                   'notification_id' => $value->id,
                   'sender_id'       => $value->sender_id,
                   'sender_name'     => $value->sender->name,
                   'title'           => $value->title,
                   'body'            => $value->body,
                   'timestamp'       => date('Y-m-d',strtotime($value->created_at)),
                   'id'              => $value->id,
                   'type'            => $vlaue->type
                ]);
            }
            return ['status'=>true,'message'=>__('Record found'),'data'=>$data];
         }
            return ['status'=>false,'message'=>__('Record not found')];
    }

    /**
    * End Point  :- user/get/conversation/users
    * Method     :- Get
    * Parameters :- sender_id
    */
    public function getConversationUsers(Request $request){
    }

    /**
    * End Point  :- user/get/conversations
    * Method     :- Post
    * Parameters :- sender_id,receiver_id
    */
    public function getConversations(Request $request){
    }

    /**
    * End Point  :- user/send/message
    * Method     :- Post
    * Parameters :- sender_id,receiver_id,message
    */
    public function sendMessage(Request $request){

        $input = $request->all();

        $rules = [
          'sender_id'     => 'required',
          'receiver_id'   => 'required',
          'message'       => 'required'
        ];

        $validator = Validator::make($request->all(), $rules );
  
        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }
  
         $insertData = [
             'sender_id'   => $input['sender_id'],
             'receiver_id' => $input['receiver_id'],
             'message'     => $input['message'],
         ];
         $Chat = new Chat;
         $Chat->sender_id   = $input['sender_id'];
         $Chat->receiver_id = $input['receiver_id'];
         $Chat->message     = $input['message'];
         if($Chat->save()){
             $data = array();
             $data['sender_id']         = $Chat->sender->id;
             $data['sender_name']       = $Chat->sender->name;
             $data['sender_profile']    = $Chat->sender->profile_image;
             $data['receiver_id']       = $Chat->receiver->id;
             $data['receiver_name']     = $Chat->receiver->name;
             $data['receiver_profile']  = $Chat->receiver->profile_image;
             $data['message']           = $Chat->message;
             $data['timtestamp']        = date('Y M d H:i:s',strtotime($Chat->created_at));
             $data['date']              = date('Y M d',strtotime($Chat->created_at));
             $data['time']              = date('H:i A',strtotime($Chat->created_at));
            return ['statua'=>false,'message'=>__('Successfully sent message'),'data'=>$data];
         }
         return ['statua'=>false,'message'=>__('Failed to send message')];
     }

     public function getPlans(){

        $plans = \DB::table('plans')->where('is_active','1')->get();

        if($plans->toarray()){
            $data = array();
            foreach($plans as $key => $value){
               array_push($data,[
                    'id' => $value->id,
                    'title' => $value->title,
                    'price' => $value->price,
                    'request_day' => $value->request_day
               ]);
            }
            return ['status'=>true,'message'=>'Record found','data'=>$data];
        }
        return ['status'=>false,'message'=>'Record not found'];
     }

     public function buyPlan(Request $request){
        
        $input = $request->all();

        $rules = [
          'user_id'   => 'required',
          'plan_id'   => 'required'
        ];

        $validator = Validator::make($request->all(), $rules );
  
        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }

        $isExistPlan = \DB::table('user_plans')->where(['user_id'=>$input['user_id'],'plan_id'=>$input['plan_id']])->count();

        if($isExistPlan){
            return response(['status' => false , 'message' => 'Already actived your plan']);
        }

        $plan = \DB::table('plans')->where('is_active','1')->where('id',$input['plan_id'])->first();
        
        if($plan){
            $insertData = [
                'user_id' => $input['user_id'],
                'plan_id' => $input['plan_id'],
                'title'   => $plan->title,
                'price'   => $plan->price,
                'request_day'    => $plan->request_day,
                'payment_status' => '1'
            ];

            $insertId = \DB::table('user_plans')->insertGetId($insertData);

            if($insertId)
               return response(['status' => true , 'message' => 'Your plan actived']);
            else
               return response(['status' => false , 'message' => 'Failed to buy plan']);  
              

        }

        return ['status'=>false,'message'=>_('This plan does not exist')];

     }

     public function getMyPlan(Request $request){

        $input = $request->all();

        $rules = [
          'user_id'   => 'required'
        ];

        $validator = Validator::make($request->all(), $rules );
  
        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }
         
        $plan = \DB::table('user_plans')->where('user_id',$input['user_id'])->first();

        if($plan){
            $data = array();
            $data['id'] = $plan->id;
            $data['plan_id']     = $plan->id;
            $data['title']       = $plan->id;
            $data['price']       = $plan->id;
            $data['request_day'] = $plan->id;
            return ['status'=>true,'message'=>'Record found','data'=>$data];
        }
        return ['status'=>false,'message'=>'Record not found'];
     }

}
