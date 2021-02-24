<?php

namespace App\Http\Controllers\Api\Marchant;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Marchant\AuthController;
use App\Models\Product;
use App\Models\ProductImage;
use App\User;
use Auth;
use Validator;
use DB;

class ProductController extends Controller
{

    /**
    * End Point  :- marchant/get/product/list
    * Method     :- Get
    * Parameters :- marchant_id
    *
    */
    public function index(Request $request){

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
         
         $products = Product::where('marchant_id',$input['marchant_id'])->where('type','0')->whereNull('deleted_at')->orderBy('id','desc')->get();

         if($products->toArray()){
             $data = array();
             foreach($products as $key => $product){
                $temp = [];
                $temp['product_id']   = $product->id;
                $temp['title']        = $product->title;
                $temp['description']  = $product->description;
                $temp['worth']        = $product->price;
                $temp['image']        = $product->image;
                $temp['images']       = $product->images;
                $temp['created_at']   = $product->created_at;
                array_push($data,$temp);
             } 

             return ['status'=>true,'message'=> __('Record found') , 'data' => $data ];
         }else{
             return ['status'=>false,'message'=> __('Record not found') ];
         }

    }

      /**
    * End Point  :- marchant/get/product/detail
    * Method     :- Get
    * Parameters :- marchant_id , product_id
    *
    */
    public function detail(Request $request){

      $input = $request->all();

      $rules = [
        'product_id'  => 'required'
      ];

      $validator = Validator::make($request->all(), $rules );

      if ($validator->fails()) {
          $errors =  $validator->errors()->all();
          return response(['status' => false , 'message' => $errors[0]]);              
      }

       $product = Product::where('id',$input['product_id'])->whereNull('deleted_at')->first();
       
       if($product){

          $isFavourite = 0;

          if(isset($input['user_id']) && !is_null($input['user_id']) && !empty($input['user_id'])){
              $isFavourite = DB::table('favourite_products')->where('user_id',$input['user_id'])->where('product_id',$product->id)->count();
              $isFavourite = $isFavourite > 0 ? '1' : '0';
          }
       
       $marchatDetails = User::find($product->marchant_id);

              $data = [];
              $data['product_id']   = $product->id;
              $data['title']        = $product->title;
              $data['description']  = $product->description;
              $data['worth']        = $product->price;
              $data['type']         = $product->type;
              $data['coupon_code']  = $product->coupon_code  ?? '';
              $data['limitation']   = $product->limitation   ?? '';
              $data['start_date']   = $product->start_date   ?? '';
              $data['end_date']     = $product->end_date     ?? '';
              $data['image']        = $product->image;
              $data['images']       = $product->images;
              $data['created_at']   = $product->created_at;

              $data['phone']         = $marchatDetails->phone;
              $data['shop_name']     = $marchatDetails->shop_name;
              $data['shop_link']     = $marchatDetails->shop_link;
              $data['shop_address']  = $marchatDetails->address;
              $data['is_favourite']  = $isFavourite;
              if($marchatDetails->shop_start_time){
                  $data['shop_start_time']  = date('h:i A',strtotime($marchatDetails->shop_start_time));
              }else{
                  $data['shop_start_time']  = '';
              }

              if($marchatDetails->shop_end_time){
                  $data['shop_end_time']  = date('h:i A',strtotime($marchatDetails->shop_end_time));
              }else{
                  $data['shop_end_time']  = '';
              }

           return ['status'=>true,'message'=> __('Record found') , 'data' => $data ];
       }else{
           return ['status'=>false,'message'=> __('Record not found') ];
       }

    }
    
    /**
    * End Point  :- marchant/create/product
    * Method     :- Post
    * Parameters :- marchant_id, title, description, worth, image1, image2, image3
    *
    */
    public function create(Request $request){

        $input = $request->all();

        $rules = [
          'marchant_id' => 'required',
          'title'       => 'required',
          'description' => 'required',
          'worth'       => 'required'
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
           'marchant_id'   => $input['marchant_id'],
           'title'         => $input['title'],
           'description'   => $input['description'],
           'price'         => $input['worth'],
        ];
        
        $productId = Product::insertGetId($insertData);

        if($productId){

            $images = [];
            $fileName = null;
            if ($request->hasFile('image1')) {
               $fileName = str_random('10').'.'.time().'.'.request()->image1->getClientOriginalExtension();
               request()->image1->move(public_path('images/product/'), $fileName);
               array_push($images, [ 'product_id' => $productId , 'image' => $fileName]);
             }
             $fileName = null;
            if ($request->hasFile('image2')) {
               $fileName = str_random('10').'.'.time().'.'.request()->image2->getClientOriginalExtension();
               request()->image2->move(public_path('images/product/'), $fileName);
               array_push($images, [ 'product_id' => $productId , 'image' => $fileName]);
             }
             $fileName = null;
            if ($request->hasFile('image3')) {
               $fileName = str_random('10').'.'.time().'.'.request()->image3->getClientOriginalExtension();
               request()->image3->move(public_path('images/product/'), $fileName);
               array_push($images, [ 'product_id' => $productId , 'image' => $fileName]);
             }

             if($images)
               ProductImage::insert($images);

            return ['status'=>true,'message'=>'Successfully added product'];
        }
        else{
            return ['status'=>false,'message'=>'Failed to add product'];
        }
    }

    /**
    * End Point  :- marchant/update/product
    * Method     :- Post
    * Parameters :- product_id, marchant_id, title, description, worth, image1, image2, image3
    *
    */
    public function update(Request $request){

        $input = $request->all();

        $rules = [
          'marchant_id' => 'required',
          'product_id'  => 'required',
          'title'       => 'required',
          'description' => 'required',
          'worth'       => 'required'
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

        $updateData = [
           'title'         => $input['title'],
           'description'   => $input['description'],
           'price'         => $input['worth']
        ];
        
         if(!Product::where('id',$input['product_id'])->where('marchant_id',$input['marchant_id'])->update($updateData)){
            return ['status'=>false,'message'=> 'Failed to update product'];
         }

            $images = [];
            $fileName = null;
            if ($request->hasFile('image1')) {
               $fileName = str_random('10').'.'.time().'.'.request()->image1->getClientOriginalExtension();
               request()->image1->move(public_path('images/product/'), $fileName);
               array_push($images, [ 'product_id' => $input['product_id'] , 'image' => $fileName]);
             }
             $fileName = null;
            if ($request->hasFile('image2')) {
               $fileName = str_random('10').'.'.time().'.'.request()->image2->getClientOriginalExtension();
               request()->image2->move(public_path('images/product/'), $fileName);
               array_push($images, [ 'product_id' => $input['product_id'] , 'image' => $fileName]);
             }
             $fileName = null;
            if ($request->hasFile('image3')) {
               $fileName = str_random('10').'.'.time().'.'.request()->image3->getClientOriginalExtension();
               request()->image3->move(public_path('images/product/'), $fileName);
               array_push($images, [ 'product_id' => $input['product_id'] , 'image' => $fileName]);
             }
             if($images)
                ProductImage::insert($images);

            return ['status'=>true,'message'=>'Successfully updated product'];
    }

    /**
    * End Point  :- marchant/delete/product
    * Method     :- Post
    * Parameters :- marchant_id, product_id
    *
    */
    public function destroy(Request $request){

        $input = $request->all();

        $rules = [
          'marchant_id' => 'required',
          'product_id'  => 'required'
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

        $where = [
           'marchant_id'   => $input['marchant_id'],
           'id'            => $input['product_id']
        ];
        
        if(Product::where($where)->delete()){
            return ['status'=>true,'message'=> __('Successfully deleted product')];
        }
        else{
            return ['status'=>false,'message'=> __('Failed to delete product')];
        }
    }

  
}
