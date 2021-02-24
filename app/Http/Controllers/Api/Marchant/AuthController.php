<?php

namespace App\Http\Controllers\Api\Marchant;

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
    * End Point  :- marchant/login
    * Method     :- Post
    * Parameters :- email, password
    *               device_type (1 for Android, 2 for IOS), device_token (Optional)
    */
    public function login(Request $request){

        $input = $request->all();

        $rules = [
          'email'         => 'required|email',
          'password'      => 'required',
          'device_type'   => 'required',
        ];

        $validator = Validator::make($request->all(), $rules );

        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }

         if(!auth()->guard()->attempt(array( 'email' => $input['email'] , 'password' => $input['password'] , 'role_id' => '2'  , 'deleted_at' => NULL ))) {
            return response(['status' => false , 'message' => __('Invalid credientials') ]);       
         } 
        

         $Marchant = User::find(auth()->guard()->id());

         auth::logout();

         if($Marchant->is_active != '1'){
            return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')]);
         }

         $Marchant->device_type  = $input['device_type']  ?? NULL;
         $Marchant->device_token = $input['device_token'] ?? NULL;
         $Marchant->update();

         $data['user_id']           = $Marchant->id;
         $data['user_name']         = $Marchant->name      ?? '';
         $data['profile_image']     = $Marchant->profile_image;
         $data['email']             = $Marchant->email     ?? '';
         $data['phone_number']      = $Marchant->phone     ?? '';
         $data['shop_name']         = $Marchant->shop_name ?? '';
         $data['shop_address']      = $Marchant->address   ?? '';
         $data['city']              = $Marchant->city      ?? '';
         $data['zip_code']          = $Marchant->zip_code  ?? '';
         $data['shop_start_time']   = $Marchant->shop_start_time  ? date('h:i A',strtotime($Marchant->shop_start_time)) : '';
         $data['shop_end_time']     = $Marchant->shop_end_time    ? date('h:i A',strtotime($Marchant->shop_end_time))   : '';
         $data['shop_link']         = $Marchant->shop_link ?? '';
         $data['referral_code']     = $Marchant->referral_code   ?? '';
         $data['my_wallet']         = $Marchant->my_wallet;

        return response(['status' => true , 'message' => __('Successfully logged In') , 'data' => $data ]);
    }

    /**
    * End Point  :- marchant/signup
    * Method     :- Post
    * Parameters :- phone_number, user_name, email, password ,shop_name, shop_address, city, zip_code
    *               device_type (1 for Android, 2 for IOS), 'referral_code ,  device_token (Optional)
    */
    public function signup(Request $request){

        $input = $request->all();

        $rules = [
          'phone_number' => 'required|min:10|max:10|unique:users,phone,null,id,deleted_at,NULL',
          'user_name'    => 'required',
          'email'        => 'required|unique:users,email,null,id,deleted_at,NULL',
          'password'     => 'min:8|required',
          'shop_name'    => 'required',
          'shop_address' => 'required',
          'city'         => 'required',
          'zip_code'     => 'required',
          'device_type'  => 'required'
        ];

        $validator = Validator::make($request->all(), $rules );

        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }

        $insertData = [
           'phone'         => $input['phone_number'],
           'name'         => $input['user_name'],
           'email'         => $input['email'],
           'password'      => \Hash::make($input['password']),
           'shop_name'     => $input['shop_name'],
           'address'       => $input['shop_address'],
           'city'          => $input['city'],
           'zip_code'      => $input['zip_code'],
           'device_type'   => $input['device_type'],
           'device_token'  => $input['device_token'] ?? null,
           'role_id'       => '2',
           'invite_referral_code' => $input['referral_code'] ?? NULL
        ];
        $referralCode	 = $this->generateReferralCode();

        do{
            $isExistReferralCode  = $this->isExistReferralCode($referralCode);
        } while ($isExistReferralCode);
        
        $insertData['referral_code'] = $referralCode;

        $Marchant = User::insertGetId($insertData);

        if($Marchant){
          $insertData['marchat_id'] = $Marchant;
          $insertData['my_wallet']  = 0.00;

          return ['status'=>true,'message'=>'Successfully signed up','data'=>$insertData];
          }else{
           return ['status'=>false,'message'=>'Failed to sign up'];
          }
    }

    /**
    * End Point  :- marchant/get/profile
    * Method     :- Get
    * Parameters :- marchant_id
    */
    public function getProfile(Request $request){

      $input = $request->all();

      $rules = [
        'marchant_id' => 'required',
      ];

      $validator = Validator::make($request->all(), $rules );

      if ($validator->fails()) {
          $errors =  $validator->errors()->all();
          return response(['status' => false , 'message' => $errors[0]]);              
      }

      $isMarchantExist = $this->isMarchantExist($input['marchant_id']);

      if(!$isMarchantExist)
           return response(['status'=>false,'message'=>__('This marchant does not exist')],401);

       $Marchant = User::find($input['marchant_id']);

       if($Marchant->is_active != '1'){
          return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
       }

       $data['user_id']           = $Marchant->id;
       $data['user_name']         = $Marchant->name      ?? '';
       $data['profile_image']     = $Marchant->profile_image;
       $data['email']             = $Marchant->email     ?? '';
       $data['phone_number']      = $Marchant->phone     ?? '';
       $data['shop_name']         = $Marchant->shop_name ?? '';
       $data['shop_address']      = $Marchant->address   ?? '';
       $data['city']              = $Marchant->city      ?? '';
       $data['zip_code']          = $Marchant->zip_code  ?? '';
       $data['shop_start_time']   = $Marchant->shop_start_time  ? date('h:i A',strtotime($Marchant->shop_start_time)) : '';
       $data['shop_end_time']     = $Marchant->shop_end_time    ? date('h:i A',strtotime($Marchant->shop_end_time))   : '';
       $data['shop_link']         = $Marchant->shop_link ?? '';
       $data['referral_code']     = $Marchant->referral_code   ?? '';
       $data['my_wallet']         = $Marchant->my_wallet;

      return response(['status' => true , 'message' => __('Record found') , 'data' => $data ]);
    }

    /**
    * End Point  :- marchant/update/profile
    * Method     :- Post
    * Parameters :- marchant_id,phone_number, user_name, email, password ,shop_name, shop_address, city, zip_code,shop_start_time,shop_end_time
    *               shop_link (Optional) , profile_image (Optional)
    */
    public function updateProfile(Request $request){

      $input = $request->all();

      $marchantId     = $input['marchant_id'] ?? NULL;

      $rules = [
        'marchant_id'  => 'required',
        'phone_number' => 'required|min:10|max:10|unique:users,phone,'.$marchantId.',id,deleted_at,NULL',
        'user_name'    => 'required',
        'email'        => 'required|unique:users,email,'.$marchantId.',id,deleted_at,NULL',
        'shop_name'    => 'required',
        'shop_address' => 'required',
        'city'         => 'required',
        'shop_start_time' => 'required',
        'shop_end_time'   => 'required',
        'zip_code'        => 'required',
        'shop_link'       => 'url'
      ];

      $validator = Validator::make($request->all(), $rules );

      if ($validator->fails()) {
          $errors =  $validator->errors()->all();
          return response(['status' => false , 'message' => $errors[0]]);              
      }

      $isMarchantExist = $this->isMarchantExist($input['marchant_id']);

      if(!$isMarchantExist)
           return response(['status'=>false,'message'=>__('This marchant does not exist')],401);

       $Marchant = User::find($input['marchant_id']);

       if($Marchant->is_active != '1'){
          return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
       }

       $fileName = null;
       if ($request->hasFile('profile_image')) {
          $fileName = str_random('10').'.'.time().'.'.request()->profile_image->getClientOriginalExtension();
          request()->profile_image->move(public_path('images/profile/'), $fileName);
        }
       
      $updateData = [
         'phone'         => $input['phone_number'],
         'name'          => $input['user_name'],
         'email'         => $input['email'],
         'shop_name'     => $input['shop_name'],
         'address'       => $input['shop_address'],
         'city'          => $input['city'],
         'zip_code'      => $input['zip_code'],
         'shop_start_time' => date('H:i:s',strtotime($input['shop_start_time'])),
         'shop_end_time'   => date('H:i:s',strtotime($input['shop_end_time'])),
         'shop_link'       => $input['shop_link'] ?? NULL,
        ];

        if($fileName)
           $updateData['profile_image'] = $fileName;
      
      $Marchant = User::where('id',$marchantId)->update($updateData);

      return ['status'=>true,'message'=>__('Successfully updated')];

      if($Marchant)
        return ['status'=>true,'message'=>__('Successfully updated')];
      else
        return ['status'=>false,'message'=>__('Failed to sign up')];
    }

    /**
    * End Point  :- marchant/change/password
    * Method     :- Post
    * Parameters :- marchant_id, old_password, new_password
    *
    */
    public function changePassword(Request $request){
        
      $input    = $request->all();

      $rules = [
                'marchant_id'       => 'required',
                'old_password'      => 'required',
                'new_password'      => 'min:8|required',
               ];

      $validator = Validator::make($request->all(), $rules);

      if ($validator->fails()) {
        $errors =  $validator->errors()->all();
        return response(['status' => false , 'message' => $errors[0]] , 200);              
      }

      $isMarchantExist = $this->isMarchantExist($input['marchant_id']);

      if(!$isMarchantExist)
           return response(['status'=>false,'message'=>__('This marchant does not exist')],401);

       $Marchant = User::find($input['marchant_id']);

       if($Marchant->is_active != '1'){
          return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
       }

       if (!(\Hash::check($request->old_password,  $Marchant->password))) {
            return ['status' => false , 'message' => __('Your old password does not matches with the current password  , Please try again')];
       }

       elseif(strcmp($request->old_password, $request->new_password) == 0){
            return ['status' => false , 'message' => __('New password cannot be same as your current password,Please choose a different new password')];
       }

        $Marchant->password = \Hash::make($input['new_password']);
        if($Marchant->update()){
         return response(['status' => true , 'message' => __('Successfully updated')] , 200);
        }
        return response(['status' => false , 'message' => __('Failed to update')] , 200);
    }
   
    /**
    * End Point  :- marchant/forgot/password
    * Method     :- Post
    * Parameters :- email
    *
    */
    public function forgotPassword(Request $request){
       
      $input    = $request->all();

      $rules = [
                'email' => 'required|email',
               ];

      $validator = Validator::make($request->all(), $rules);

      if ($validator->fails()) {
        $errors =  $validator->errors()->all();
        return response(['status' => false , 'message' => $errors[0]] , 200);              
      }

      $Marchant = User::where('email',$input['email'])->whereNull('deleted_at')->first();

      if(empty($Marchant) || is_null($Marchant)){
          return ['status'=>false,'message'=>'This email address does not exist'];
      }

       if($Marchant->is_active != '1'){
        return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
       }

      $token = app(PasswordBroker::class)->createToken($Marchant);
      $data = array(
        'to'     => $input['email'],
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
     *  Is Marchant Exist
    */
    public function isMarchantExist($marchentId){
      $Marchant = DB::table('users')->where('id',$marchentId)->where('role_id','2')->whereNull('deleted_at')->count();
      if($Marchant)
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
}
