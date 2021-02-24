<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use \Illuminate\Auth\Passwords\PasswordBroker;
use App\User;
use Auth;
use Validator;
use DB;

class AuthController extends Controller
{
    /**
    * End Point  :- user/exist/rut/number
    * Method     :- Get
    * Parameters :- rut_number
    */

    public function exitRutNumber(Request $request){

      $input = $request->all();

      $rules = [
        'rut_number' => 'required',
      ];
      
      $validator = Validator::make($input,$rules);

      if ($validator->fails()) {
          $errors =  $validator->errors()->all();
          return response(['status' => false , 'message' => $errors[0]]);              
      }

      $isExist = User::where('rut_number',$input['rut_number'])->whereNull('deleted_at')->count();

      if($isExist){
           return ['status'=>true,'message'=> __('Exist') ];
      }
           return ['status'=>false,'message'=> __('Not exist') ];
    }

    /**
    * End Point  :- user/check/rut/number
    * Method     :- Get
    * Parameters :- rut_number
    */

    public function checkRutNumber(Request $request){

      $input = $request->all();

      $rules = [
        'rut_number' => 'required',
      ];
      
      $validator = Validator::make($input,$rules);

      if ($validator->fails()) {
          $errors =  $validator->errors()->all();
          return response(['status' => false , 'message' => $errors[0]]);              
      }

      $str = $input['rut_number'];
      $pattern = "/^\d{2}\.?\d{3}\.?\d{3}\-?(\w{1}|[0-9])$/i";
      $isMatch = preg_match($pattern, $str, $matches, PREG_OFFSET_CAPTURE);
      if($isMatch){

        $getDigit = $this->getRutNumber($str);
        
        if($str[-1] == $getDigit){
          return ['status'=>true,'message'=> __('Valid') ];
        }
      }
           return ['status'=>false,'message'=> __('Invalid') ];
    }

    /**
    * End Point  :- user/login
    * Method     :- Post
    * Parameters :- rut_number,password,device_type
    * Optional Parameter :- device_token,lat,lng
    */
    public function login(Request $request){

        $input = $request->all();

        $rules = [
          'rut_number'    => 'required',
          'password'      => 'required',
          'device_type'   => 'required',
        ];

        $validator = Validator::make($request->all(), $rules );

        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }

         if(!auth()->guard()->attempt(array( 'rut_number' => $input['rut_number'] , 'password' => $input['password'] , 'role_id' => '3'  , 'deleted_at' => NULL ))) {
            return response(['status' => false , 'message' => __('Invalid credientials') ]);       
         } 
        
         $User = User::find(auth()->guard()->id());

         auth::logout();

         if($User->is_active != '1'){
            return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')]);
         }

         $User->device_type  = $input['device_type']  ?? NULL;
         $User->device_token = $input['device_token'] ?? NULL;
         $User->update();

         $data['user_id']           = $User->id;
         $data['user_name']         = $User->name       ?? '';
         $data['profile_image']     = $User->profile_image;
         $data['email']             = $User->email      ?? '';
         $data['phone']             = $User->phone      ?? '';
         $data['address']           = $User->address    ?? '';
         $data['rut_numbe']         = $User->rut_number ?? '';

        return response(['status' => true , 'message' => __('Successfully logged In') , 'data' => $data ]);
    }

    /**
    * End Point  :- user/social/login
    * Method     :- Post
    * Parameters :- rut_number,password,device_type
    * Optional Parameter :- device_token,lat,lng
    */
    public function socialLogin(Request $request){
      
      $input = $request->all();

      $rules = [
        'social_id'     => 'required',
        'login_type'    => 'required',
      ];

      $validator = Validator::make($request->all(), $rules );

      if ($validator->fails()) {
          $errors =  $validator->errors()->all();
          return response(['status' => false , 'message' => $errors[0]]);              
      }
      
      $User = User::where('social_id',$input['social_id'])->whereNull('deleted_at')->first();

      if(is_null($User) || empty($User)){
          $insertData['social_id'] = $input['social_id'];
          $insertData['name']      = $input['name'];
          $insertData['phone']     = $input['phone'];
          $insertData['email']     = $input['email'];
          try{
            $insertId = \DB::table('users')->insertGetId($insertData);
            $User = User::where('id',$insertId)->first();
          }catch(\Exception $e){
            return ['status'=>false,'message'=>__('Failed to login')];
          }
      }

       if($User->is_active != '1'){
          return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')]);
       }

       $User->device_type  = $input['device_type']  ?? NULL;
       $User->device_token = $input['device_token'] ?? NULL;
       $User->update();

       $data['user_id']           = $User->id;
       $data['user_name']         = $User->name       ?? '';
       $data['profile_image']     = $User->profile_image;
       $data['email']             = $User->email      ?? '';
       $data['phone']             = $User->phone      ?? '';
       $data['address']           = $User->address    ?? '';
       $data['rut_numbe']         = $User->rut_number ?? '';

      return response(['status' => true , 'message' => __('Successfully logged In') , 'data' => $data ]);
    }

    /**
    * End Point  :- user/signup
    * Method     :- Post
    * Parameters :- rut_number, password, confirm_password, device_type
    *      device_token, lat,lng
    */
    public function signup(Request $request){

        $input = $request->all();

        $rules = [
          'rut_number'    => 'required|unique:users,rut_number,null,id,deleted_at,NULL',
          'password'      => 'required',
          'device_type'   => 'required'
        ];

        $validator = Validator::make($request->all(), $rules );

        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }

        $str = $input['rut_number'];
        $pattern = "/^\d{2}\.?\d{3}\.?\d{3}\-?(\w{1}|[0-9])$/i";
        $isMatch = preg_match($pattern, $str, $matches, PREG_OFFSET_CAPTURE);

        if($isMatch < 1){
            return ['status'=>false,'message'=>__('Invalid rut number')];
        }

        $getDigit = $this->getRutNumber($str);
        
        if($str[-1] != $getDigit){
          return ['status'=>false,'message'=> __('Invalid rut number') ];
        }

        $storeData = [
           'rut_number'    => $input['rut_number'],
           'password'      => \Hash::make($input['password']),
           'device_type'   => $input['device_type'],
           'device_token'  => $input['device_token'] ?? Null,
           'role_id'       => '3'
        ];
        
        DB::beginTransaction();
        
        try {
           $plan  =  DB::table('plans')->where('is_active','1')->where('id','1')->first();
           $userId = DB::table('users')->insertGetId($storeData);
           $planStoreData = [
            'user_id' => $userId,
            'plan_id' => '1',
            'title'   => $plan->title,
            'price'   => $plan->price,
            'request_day'    => $plan->request_day,
            'plan_active_date' => date('Y-m-d H:i:s'),
            'plan_expiry_date' => date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s') . '+3 months')),
            'payment_status' => '1'
           ];

           DB::table('user_plans')->insertGetId($planStoreData);
           DB::commit();
           $storeData['user_id'] = $userId;
           unset($storeData['password']);
           return ['status'=>true,'message'=>__('Successfully signed up'),'data'=>$storeData];
        } catch (\Exception $e) {
          DB::rollback();
          return ['status'=>false,'message'=>__('Failed to sign up')];
        }

    }

    /**
    * End Point  :- user/get/profile
    * Method     :- Get
    * Parameters :- user_id
    */
    public function getProfile(Request $request){

      $input = $request->all();

      $rules = [
        'user_id' => 'required',
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

       $data['user_id']           = $User->id;
       $data['user_name']         = $User->name       ?? '';
       $data['profile_image']     = $User->profile_image;
       $data['email']             = $User->email      ?? '';
       $data['phone']             = $User->phone      ?? '';
       $data['address']           = $User->address    ?? '';
       $data['rut_numbe']         = $User->rut_number ?? '';

      return response(['status' => true , 'message' => __('Record found') , 'data' => $data ]);
    }

     /**
    * End Point  :- user/update/profile
    * Method     :- Post
    * Parameters :- user_id,phone, name, email, address
    *                , profile_image (Optional)
    */
    public function updateProfile(Request $request){

      $input = $request->all();

      $UserId     = $input['user_id'] ?? NULL;

      $rules = [
        'user_id'  => 'required',
        'name'     => 'required'
      ];

      if(isset($input['phone']) && !empty($input['phone']))
         $rules['phone'] = 'unique:users,phone,'.$UserId.',id,deleted_at,NULL';

      if(isset($input['email']) && !empty($input['email']))
          $rules['email'] = 'unique:users,email,'.$UserId.',id,deleted_at,NULL';

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

       $fileName = null;
       if ($request->hasFile('profile_image')) {
          $fileName = str_random('10').'.'.time().'.'.request()->profile_image->getClientOriginalExtension();
          request()->profile_image->move(public_path('images/profile/'), $fileName);
        }
      $updateData = [
         'phone'         => $input['phone']   ?? NULL,
         'name'          => $input['name']    ?? NULL,
         'email'         => $input['email']   ?? NULL,
         'address'       => $input['address'] ?? NULL,
        ];

        if(isset($input['rut_number']) && !empty($input['rut_number'])){
           $updateData['rut_number'] = $input['rut_number'];
        }

        if($fileName)
           $updateData['profile_image'] = $fileName;
      
      $User = User::where('id',$UserId)->update($updateData);

      return ['status'=>true,'message'=>__('Successfully updated')];

      if($User)
        return ['status'=>true,'message'=>__('Successfully updated')];
      else
        return ['status'=>false,'message'=>__('Failed to update')];
    }

     /**
    * End Point  :- user/change/password
    * Method     :- Post
    * Parameters :- user_id, old_password, new_password
    *
    */
    public function changePassword(Request $request){
        
      $input    = $request->all();

      $rules = [
                'user_id'           => 'required',
                'old_password'      => 'required',
                'new_password'      => 'min:8|required',
               ];

      $validator = Validator::make($request->all(), $rules);

      if ($validator->fails()) {
        $errors =  $validator->errors()->all();
        return response(['status' => false , 'message' => $errors[0]] , 200);              
      }

      $isUserExist = $this->isUserExist($input['user_id']);

      if(!$isUserExist)
           return response(['status'=>false,'message'=>__('This user does not exist')],401);

       $User = User::find($input['user_id']);

       if($User->is_active != '1'){
          return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
       }

       if (!(\Hash::check($request->old_password,  $User->password))) {
            return ['status' => false , 'message' => __('Your old password does not matches with the current password  , Please try again')];
       }

       elseif(strcmp($request->old_password, $request->new_password) == 0){
            return ['status' => false , 'message' => __('New password cannot be same as your current password,Please choose a different new password')];
       }

        $User->password = \Hash::make($input['new_password']);
        if($User->update()){
         return response(['status' => true , 'message' => __('Successfully updated')] , 200);
        }
        return response(['status' => false , 'message' => __('Failed to update')] , 200);
    }

    /**
    * End Point  :- user/update/current/location
    * Method     :- Post
    * Parameters :- user_id, lat, lng
    *
    */
    public function updateCurrentLocation(Request $request){
      $input = $request->all();

      $UserId     = $input['user_id'] ?? NULL;

      $rules = [
        'user_id'  => 'required',
        'lat'      => 'required',
        'lng'      => 'required',
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
         'lat'  => $input['lat'],
         'lng'  => $input['lng']
        ];

      $User = User::where('id',$UserId)->update($updateData);

      return ['status'=>true,'message'=>__('Successfully updated')];

      if($User)
        return ['status'=>true,'message'=>__('Successfully updated')];
      else
        return ['status'=>false,'message'=>__('Failed to update')];
    }
  
      /**
    * End Point  :- user/forgot/password
    * Method     :- Post
    * Parameters :- rut_number
    *
    */
    public function forgotPassword(Request $request){
       
      $input    = $request->all();

      $rules = [
                'rut_number' => 'required'
               ];

      $validator = Validator::make($request->all(), $rules);

      if ($validator->fails()) {
        $errors =  $validator->errors()->all();
        return response(['status' => false , 'message' => $errors[0]] , 200);              
      }

      $User = User::where('rut_number',$input['rut_number'])->whereNull('deleted_at')->first();

      if(empty($User) || is_null($User)){
          return ['status'=>false,'message'=>'This rut number does not exist'];
      }

       if($User->is_active != '1'){
        return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
       }

       if(is_null($User->email) || empty($User->email)){
          return ['status'=>false,'messsage'=>'Your RUT number does not link to the email'];
       }

      $token = app(PasswordBroker::class)->createToken($User);
      $data = array(
        'to'     => $User->email,
        'link'   => url('password/reset/'.$token)
      );

      try {
          \Mail::send('Mails.forgot_password', $data, function ($message) use($data) {
            $message->from( env('MAIL_FROM') , env('MAIL_FROM_NAME') );
            $message->to($data['to'])->subject('Reset password for GR eCommerce account');
          });
          return ['status'=>true,'message'=>'Reset password link sent to you email address'];
      } catch (\Exception $th) {
          return ['status' => false , 'message'=>'Failed to send reset password link'];
      }
      
    }

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
     * Is Exist Referral Code Exist
     */
    public function isExistReferralCode($couponCode){
      $isExistReferralCode  = User::where('referral_code',$couponCode)->count();
      return $isExistReferralCode > 0 ? true : false;
  }
  
  /**
   * Generate referral code
   */
  public function generateReferralCode($strength = 8) {
      $permitted_chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $input           = $permitted_chars;
      $input_length = strlen($permitted_chars);
      $random_string = '';
      for($i = 0; $i < $strength; $i++) {
          $random_character = $input[mt_rand(0, $input_length - 1)];
          $random_string .= $random_character;
      }
      return $random_string;
  }

  public function getRutNumber($r) {
    $s = 1;
    $r = str_replace('-','',$r);
    for($m = 0; $r!= 0; $r /= 10)
        $s = ($s + $r % 10 * (9- $m++ % 6))% 11;
    return chr ($s? $s + 47 : 75);
  }

}
