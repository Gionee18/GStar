<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;
use App\Product;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GStarBaseController;
use DB,Auth;
use Redirect,Session;
use LucaDegasperi\OAuth2Server\Facades\Authorizer;

class ProductController extends GStarBaseController
{
   /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
	//function for Product List----------- 
    public function index(Request $request)
    {
       $inputs = Input::get();
	   $search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
	   $pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';
		

	   //List All Products
			$result_array = array();
			$responseContent = $this->validateUser(CategoryController::G_LEARNER_ROLE_ID,false);
	        if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
			
			$ProductDetails = Product::getListOfAllProducts($search_keyword,$pageNo);
			$result_array['count'] = count($ProductDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $ProductDetails;
			$responseContent = $result_array;
			return $this->reponseBuilder($responseContent);
    }
	
	
	//function for Product List by Category -----------    
	  public function getProductListByCategory($cat_id = null)
    {
		    $result_array = array();
			$ProductDetails = Product::getProductListByCategory($cat_id);
			$result_array['count'] = count($ProductDetails);
			$result_array['status'] = 'success';
			$result_array['data'] = $ProductDetails;
			$responseContent = $result_array;
			return $this->reponseBuilder($responseContent);
			
    }

  //function for Monthly Product Details -----------    
	  public function MonthlyProductDetails(Request $request)
    {
		
		    $responseContent = array();
			$Products = Product::MonthlyProductDetails($request);
			$countProducts = count($Products);
			if($countProducts > 0){
				$responseContent['count'] = $countProducts;
				$responseContent['status'] = 'success';
				$responseContent['data'] = $Products;
			}
			else{
				
				$responseContent['status'] = 'success';
				$responseContent['data'] = ProductController::MSG_NO_RECORD;
			}
			
			return $this->reponseBuilder($responseContent);
			
    }
	
	
	
	// function for change the order of Category----
    public function changeOrder(Request $request)
    {
		 //  print_r($request->input('orderDataArray'));
			// die;

		  $responseContent = array();
		  $inputs = Input::get();
		
		  $responseContent = $this->validateUser(ProductController::G_TRAINER_ROLE_ID,true);
		 
		  if(!empty($responseContent))
		  {
			  return $this->reponseBuilder($responseContent);
		  }
		  $result = Product::changeOrderofProduct($request); 
		if(!Session::has('msg'))
			{
				$responseContent  = $this->queryResponseBuilder(UserController::MSG_TEXT, $result['msg']);
			}else
			{
				$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		  
		 
		  return $this->reponseBuilder($responseContent);
		
	 }

	 
	 
	//function for Product Detail -----------    
	  public function ProductDetails(Request $request)
    {
        	$result_array = array();
			$ProductDetails = Product::getProductDetails($request);
			if(!Session::has('msg'))
			{	
				$result_array['count'] = count($ProductDetails);
				$result_array['status'] = 'success';
				$result_array['data'] = $ProductDetails;
				$responseContent = $result_array;
			}
			else
			{
				$responseContent = $this->errorResponseBuilder(CategoryController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
			return $this->reponseBuilder($responseContent);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
	 
	 //function for Add Product-----------    
     public function create(Request $request)
    {
			$brand=GStarBaseController::GIONEE_BRAND;
			$result_array = array();
			$responseContent = $this->validateUser(CategoryController::G_TRAINER_ROLE_ID,true);
	        if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
			
			$product = Product::createProduct($request);
			if(!Session::has('msg'))
			{	
				if($product->save())
				{ 
					    $lastinsertID = $product->id; 
					    if(Auth::user()->role==ProductController::G_TRAINER_ROLE_ID)
					    {
					    	$product_dummy['category_id']=$product->category_id;
					    	$product_dummy['product_name']=$product->product_name;
					    	$product_dummy['product_desc']=$product->product_desc;
					    	$product_dummy['status']=$product->status;
					    	$product_dummy['new_product_flag']=$product->new_product_flag;
					    	$product_dummy['desc1']=$product->desc1;
					    	$product_dummy['desc3']=$product->desc3;
					    	$product_dummy['request_userid']=Auth::user()->id;
							$product_dummy['approve_status']=0;
							$product_dummy['product_id']=$lastinsertID;
							$productUpdate = DB::table('product_dummy')->insert($product_dummy);
					    }
					    /* Add Hash tags values */
						$HashtagArray = GStarBaseController::get_hashtags($product->product_desc,$str = 0);
						if(!empty($HashtagArray)){
							$saveHashtag = GStarBaseController::saveHashtag($lastinsertID,$HashtagArray,$brand);
						}
						/* Add Hash tags values End */

						$result_array['count'] = count($lastinsertID);
						$result_array['status'] = 'success';
						$result_array['product_id'] = $lastinsertID;
						$result_array['msg'] = CategoryController::MSG_ADDED_PRODUCT;
						$responseContent= $result_array;
				}
				else
				{
					$responseContent = $this->errorResponseBuilder(ProductController::ERROR_ARGUMNETS_MISSING,ProductController::MSG_ARGUMNETS_MISSING);
				}
			}
			else
			{
				$responseContent = $this->errorResponseBuilder(ProductController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
		
		return $this->reponseBuilder($responseContent);
    }

	//function for Edit Product(GET)----------- 
	 public function editProduct($id)
    {
      
	        $result_array = array();
			$responseContent = $this->validateUser(CategoryController::G_SUPERVISOR_ROLE_ID,false);
	        if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
			$productDetail = Product::getProductDetail($id);
			$result_array['count'] = count($productDetail);
			$result_array['status'] = 'success';
			$result_array['data'] = $productDetail;
			$responseContent = $result_array; 
			return $this->reponseBuilder($responseContent);
    }


    public function approvedproductById(Request $request)
    {
      		$responseContent = $this->validateUser(CategoryController::G_SUPERVISOR_ROLE_ID,false);
	        if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
      		$id=$request->input('id');
      		$request_id=$request->input('request_id');
      		$product_id=$request->input('product_id');
      		$result_array = array();
      		if($id && $request_id && $product_id){

      			$productDetail = Product::getapprovedProductDetailId($id,$request_id,$product_id);
				$result_array['count'] = count($productDetail);
				$result_array['status'] = 'success';
				$result_array['data'] = $productDetail;
				$responseContent = $result_array; 
      		}else{
      			$responseContent = $this->errorResponseBuilder(UserController::ERROR_BAD_PARAMETERS,UserController::MSG_BAD_PARAMETERS);
      		}
	        
			return $this->reponseBuilder($responseContent);
    }

     public function approvedproductlist(Request $request)
    {
       		$inputs = Input::get();
			$search_keyword = isset($inputs['search_keyword']) ? trim($inputs['search_keyword']) : '';
		    $pageNo =  isset($inputs['page_no']) ? trim($inputs['page_no']) : '';
		    $responseContent = $this->validateUser(CategoryController::G_TRAINER_ROLE_ID,false);
	        if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}

	        $result_array = array();
			$productDetail = Product::getapprovedProductDetail($request,$search_keyword ,$pageNo);
			$result_array['count'] = count($productDetail);
			$result_array['status'] = 'success';
			$result_array['data'] = $productDetail;
			$responseContent = $result_array; 

			return $this->reponseBuilder($responseContent);
    }

    

    public function ApprovedRejectProductByAdmin(Request $request)
	 {  
		$result_array = array();
		$responseContent = $this->validateUser(ProductController::G_TRAINER_ROLE_ID,true);
	        if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
		$id=$request->input('id');	
		$product_id = trim($request->input('product_id'));
		$request_id = trim($request->input('request_id'));
		$approve_status = trim($request->input('approve_status'));
		if(!$product_id && !$request_id && !$id && !$approve_status) {
			$result_array = $this->errorResponseBuilder(ProductController::ERROR_BAD_PARAMETERS,ProductController::MSG_BAD_PARAMETERS);
			}
			else {

				$productDetail = Product::getapprovedProductDetailId($id,$request_id,$product_id);
				$productupdate=$productDetail['newdata'];
				//print_r($productupdate); exit;
				$arr=Product::ProductApproveAdmin($productupdate,$request);
				if($arr['status'] == 'success'){
					$result_array['status'] = 'success';
					$result_array['msg'] = $arr['msg'];
					//$result_array=Product::ProductApproveAdmin($productupdate,$request);
				}else{
					$result_array = $this->errorResponseBuilder(ProductController::ERROR_BAD_PARAMETERS,$arr['msg']);
				}
				
				
			
		 }
		
		return json_encode($result_array);
	 }
	
	//function for Update Product(POST)----------- 
	public function updateProduct(Request $request)
    {
		    $brand=GStarBaseController::GIONEE_BRAND;
		    $productUpdate = 0;
			 $result_array = array();
			$responseContent = $this->validateUser(ProductController::G_SUPERVISOR_ROLE_ID,true);
	        if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}
			$product_id = $request->input('product_id');
			$product = Product::editProduct($request);
			//print_r($product); exit;
			$role=Auth::user()->role;
			if(!Session::has('msg'))
			{
				if($role>ProductController::G_ADMIN_ROLE_ID)
				{
					$getprevious = DB::table('product_dummy')->where('request_userid', Auth::user()->id)->where('product_id', $product_id)->where('approve_status','0')->first();
					if($getprevious){
						$productUpdate = DB::table('product_dummy')->where('request_userid', Auth::user()->id)->where('product_id', $product_id)->where('approve_status','0')->update($product);
							if($productUpdate)
							{
								$result_array['status'] = 'success';
								$result_array['msg'] = ProductController::MSG_RECORD_UPDATED_ADMIN_APPROVE;
								$responseContent = $result_array;
							}
							else
							   {
								$responseContent = $this->errorResponseBuilder(ProductController::ERROR_BAD_PARAMETERS,ProductController::MSG_NO_UPDATE);
							   
						       }	
					}else{
						
						$product2=array('request_userid'=>Auth::user()->id,'approve_status'=>'0','product_id'=>$product_id);
						$insertProduct=array_merge($product,$product2);
						//print_r($insertProduct); die;
						
					$productUpdate = DB::table('product_dummy')->insert($insertProduct);
					if($productUpdate)
					{
						$result_array['status'] = 'success';
						$result_array['msg'] = ProductController::MSG_RECORD_UPDATED_ADMIN_APPROVE;
						$responseContent = $result_array;
					}
					else
					   {
						$responseContent = $this->errorResponseBuilder(ProductController::ERROR_BAD_PARAMETERS,ProductController::MSG_NO_UPDATE);
					   
				       }	
					}
					

				}
				else{
					$productUpdate = DB::table('product')->where('id', $product_id)->update($product);
				if($productUpdate)
				{
					$result_array['status'] = 'success';
					$result_array['msg'] = ProductController::MSG_RECORD_UPDATED;
					$responseContent = $result_array;
				}
				else
				   {
					$responseContent = $this->errorResponseBuilder(ProductController::ERROR_BAD_PARAMETERS,ProductController::MSG_NO_UPDATE);
				   
			       }	

				}

				/* Add Hash tags values */
					$HashtagArray = GStarBaseController::get_hashtags($product['product_desc'], $str = 0);
					if(!empty($HashtagArray)){
						$deleteExist=DB::table('product_hashtags')
												->where('product_id',$product_id)
												->where('brand',$brand)
												->delete();
						$saveHashtag = GStarBaseController::saveHashtag($product_id,$HashtagArray,$brand);
					}
				/* Add Hash tags values End */
				
			}
			else
			{
				$responseContent = $this->errorResponseBuilder(ProductController::ERROR_BAD_PARAMETERS,Session::get('msg'));
			}
			
		return   $this->reponseBuilder($responseContent);
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
	 
	//function for Delete Product----------- 
    public function destroy(Request $request)
    {
		 $result_array = array();
         $responseContent = $this->validateUser(CategoryController::G_TRAINER_ROLE_ID,true);
	        if(!empty($responseContent)){ return $this->reponseBuilder($responseContent);}

		    $productID = $request->input('id'); 

		    //$TutorialExist = DB::table('video_tutorials')->where('product_id',$productID)->get();
		    // echo $productID;
		    // print_r($TutorialExist);die;
		    // if(empty($TutorialExist))
		    // {
		    	
		    // }else{
		    // 	$responseContent = $this->errorResponseBuilder(ProductController::ERROR_BAD_PARAMETERS,ProductController::MSG_TUTORIAL_EXIST_FOR_PRODUCT);
		    // }
		    $deleteSpec = DB::table('product_hashtags')->where('product_id', $productID)->where(DB::raw("LOWER(brand)"), strtolower(GStarBaseController::GIONEE_BRAND))->delete();
		    $deleteSpec = DB::table('product_spec_display')->where('model_id', $productID)->where(DB::raw("LOWER(brand)"), strtolower(GStarBaseController::GIONEE_BRAND))->delete();
		    $deleteSpec = DB::table('product_attribute_xref')->where('model_id', $productID)->where(DB::raw("LOWER(brand)"), strtolower(GStarBaseController::GIONEE_BRAND))->delete();
		    
		    $deleteImages = DB::table('asset_mapping')->where(array('module_id' =>$productID,'module' =>'product'))->delete();
		    $delete2=DB::table('product_dummy')->where('product_id',$productID)->update(['is_deleted'=>1]);
				$deleteProduct = DB::table('product')->where('id', $productID)->delete();
				if($deleteProduct || $deleteImages)
				{
							GStarBaseController:: deleteLog('product',$productID);
					        $result_array['status'] = 'success';
							$result_array['msg'] = ProductController::MSG_RECORD_DELETED;
							$responseContent = $result_array;
				}
				else
				{
					$responseContent = $this->errorResponseBuilder(ProductController::ERROR_BAD_PARAMETERS,ProductController::MSG_BAD_PARAMETERS);
				}
			
		
		return $this->reponseBuilder($responseContent);
    }
	
	
}
