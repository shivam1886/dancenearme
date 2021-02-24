<?php

namespace App\Http\Controllers\Api\Marchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\User;
use App\Models\UserBankDetails;
use Validator;

class HomeController extends Controller
{

      /**
    * End Point  :- marchant/get/all/products
    * Method     :- Get
    * Parameters :- marchant_id
    *
    */
    public function getAllProducts(Request $request){

        $input = $request->all();

        $rules = [
          'marchant_id' => 'required'
        ];

        $validator = Validator::make($request->all(), $rules );

        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }

         $Auth = new AuthController;

         $isMarchantExist = $Auth->isMarchantExist($input['marchant_id']);

         if(!$isMarchantExist)
           return response(['status'=>false,'message'=>__('This marchant does not exist')],401);

         $Marchant = User::find($input['marchant_id']);

         if($Marchant->is_active != '1'){
          return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
         }
         
         $products = Product::where('marchant_id',$input['marchant_id'])->whereNull('deleted_at')->orderBy('id','desc')->get();

         if($products->toArray()){
             $data = array();
             foreach($products as $key => $product){
                $temp = [];
                $temp['product_id']   = $product->id;
                $temp['title']        = $product->title;
                $temp['description']  = $product->description;
                $temp['worth']        = $product->price;
                $temp['coupon_code']  = $product->coupon_code;
                $temp['limitation']   = $product->limitation;
                $temp['start_date']   = $product->start_date;
                $temp['end_date']     = $product->end_date;
                $temp['image']        = $product->image;
                $temp['images']       = $product->images;
                $temp['type']         = $product->type;
                $temp['created_at']   = $product->created_at;
                array_push($data,$temp);
             } 

             return ['status'=>true,'message'=> __('Record found') , 'data' => $data ];
         }else{
             return ['status'=>false,'message'=> __('Record not found') ];
         }

    }

    /**
    * End Point  :- marchant/get/coupon/code
    * Method     :- Get
    * Parameters :- marchant_id
    *
    */
    public function getCouponCode(Request $request){

        $couponCode = $this->generateCouponCode();

        do{
            $isExistCouponCode  = $this->isExistCouponCode($couponCode);
        } while ($isExistCouponCode);
        
        return ['status'=> true, 'message'=> __('Get Coupon Code') , 'coupon_code'=>$couponCode];

    }
    
    /**
     * Is Coupon Code Exist
     */
    public function isExistCouponCode($couponCode){
        $isExistCouponCode  = Product::where('coupon_code',$couponCode)->count();
        return $isExistCouponCode > 0 ? true : false;
    }

    /**
     * Geneate Copon code
     */
    public function generateCouponCode($strength = 8) {
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
    
    /**
    * End Point  :- marchant/add/bank/details
    * Method     :- Post
    * Parameters :- marchant_id, bank_name, account_holder_name, account_number, tac_code
    *
    */
    public function addBankDetails(Request $request){
        $input = $request->all();

        $rules = [
          'marchant_id'     => 'required',
          'account_number'  => 'required|unique:user_bank_details,account_number,null,id,deleted_at,NULL',
          'bank_name'       => 'required',
          'account_holder_name'  => 'required',
          'account_number'       => 'required',
          'tac_code'             => 'required'
        ];

        $validator = Validator::make($request->all(), $rules );

        if ($validator->fails()) {
            $errors =  $validator->errors()->all();
            return response(['status' => false , 'message' => $errors[0]]);              
        }

         $Auth = new AuthController;

         $isMarchantExist = $Auth->isMarchantExist($input['marchant_id']);

         if(!$isMarchantExist)
           return response(['status'=>false,'message'=>__('This marchant does not exist')],401);

         $Marchant = User::find($input['marchant_id']);

         if($Marchant->is_active != '1'){
          return response(['status'=>false , 'message'=>__('Your are inactive , Please contact with your administrator')],401);
         }

         $insertData = [
            'user_id'     => $input['marchant_id'],
            'account_number'  => $input['account_number'],
            'bank_name'       => $input['bank_name'],
            'account_holder_name'  => $input['account_holder_name'],
            'tac_code'             => $input['tac_code']
          ];

        $productId = UserBankDetails::insertGetId($insertData);

        if($productId){
            return ['status'=>true,'message'=>'Successfully added bank details'];
        }
        else{
            return ['status'=>false,'message'=>'Failed to add bank details'];
        }
    }

    /**
    * End Point  :- marchant/get/limitation
    * Method     :- Get
    *
    */
    public function getLimitation(Request $request){
        $limitation = ['1','2','3','4','5','6','7'];
        return ['status'=>true,'message'=>__('Record found'),'limitation'=>$limitation];
    }
}
