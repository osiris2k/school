<?php namespace App\Http\Controllers\system;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Redirecturl;
use App\Site;
use \Auth;
use App\AjaxResponse;

use Illuminate\Http\Request;

class RedirecturlController extends Controller {

	public function __construct()
	{
		$this->middleware('auth');
		$this->data['page_herader'] = 'Redirect URL Management';
		$this->data['tag_first_menu'] = 'redirecturls';
	}
	
	public function rules()
	{
		return [
			'source_url'     		=> 'required|unique:redirecturls',
			'destination_url'  		=> 'required',
			'type'     				=> 'required',
	    ];
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$this->data['objs']  = Redirecturl::all();
		return view('system.redirecturl',$this->data);
	}

	public function getAjax()
	{
		$col = array('id','site_id','type','source_url','destination_url');
		$return['data'] = Redirecturl::select($col)->get()->toArray();
		return $return;
	}

	public function getDestinationUrl(){
        $post = \Request::all();
	    $data = Redirecturl::where('source_url','=',$post['source_url'])->get()->toArray();
        if($data!=NULL) {
            $data = array_shift($data);
            echo $data['destination_url'];
        }else{
            echo '';
        }
    }
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		$this->data['sites']  = Site::all();
		$this->data['action'] = 'system/redirecturls';
		$this->data['method'] = 'post';
		return view('system.forms.redirecturls',$this->data);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
        $rule = [
            'source_url'     		=> 'required|unique:redirecturls',
            'destination_url'  		=> 'required',
            'type'     				=> 'required',
        ];
		$post = \Request::all();
		$v = \Validator::make($post, $rule);



	    if ($v->fails()){
	        return redirect()->back()->withErrors($v->errors());
	    }else{
	        $checkLastSlashArr = str_split($post['source_url']);
            $checkLastSlash = $checkLastSlashArr[count($checkLastSlashArr)-1];
            $newSrc = [];
            if($checkLastSlash == '/'){
                $newSrc = array_diff_key($checkLastSlashArr,[count($checkLastSlashArr)-1 => '/']);
            }else{
                $newSrc = $post['source_url'];
            }
            $implodeUrl = implode('',$checkLastSlashArr);
            $newUrl = $checkLastSlashArr[0] != '/' ? $newUrl = '/'.$implodeUrl : $implodeUrl;

			$rURL            = new Redirecturl();
			$rURL->site_id  = $post['site_id'];
			$rURL->source_url = $newUrl;
			$rURL->destination_url  = $post['destination_url'];
			$rURL->type   = $post['type'];
			$rURL->save();
			$message	= '<div class="alert alert-success"> Create Success </div>';
			\Session::flash('response',$message);
			return redirect('system/redirecturls');
	    }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$this->data['sites']  	= Site::all();
		$this->data['obj'] 		= Redirecturl::find($id);
		$this->data['action'] 	= 'system/redirecturls/'.$id;
		$this->data['method'] 	= 'put';
		return view('system.forms.redirecturls',$this->data);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
        $rule = [
            'source_url'     		=> 'required|unique:redirecturls,source_url,' . $id ,
            'destination_url'  		=> 'required',
            'type'     				=> 'required',
        ];

		$post = \Request::all();
		$v = \Validator::make($post, $rule);

	    if ($v->fails()){
	        return redirect()->back()->withErrors($v->errors());
	    }else{
            $checkLastSlashArr = str_split($post['source_url']);
            $checkLastSlash = $checkLastSlashArr[count($checkLastSlashArr)-1];
            $newSrc = [];
            if($checkLastSlash == '/'){
                $newSrc = array_diff_key($checkLastSlashArr,[count($checkLastSlashArr)-1 => '/']);
            }else{
                $newSrc = $checkLastSlashArr;
            }
            $implodeUrl = implode('',$newSrc);
            $newUrl = $checkLastSlashArr[0] != '/' ? $newUrl = '/'.$implodeUrl : $implodeUrl;

			$rURL            = Redirecturl::find($id);
			$rURL->site_id  = $post['site_id'];
			$rURL->type   = $post['type'];
			$rURL->source_url = $newUrl;
			$rURL->destination_url  = $post['destination_url'];
			$rURL->save();
			$message         = '<div class="alert alert-success"> Edit successful </div>';
			\Session::flash('response',$message);
			return redirect('system/redirecturls');
	    }
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$count = Redirecturl::count();
		if($count>1)
		{
			try{
			    $obj = Redirecturl::findOrFail($id);
//			    $obj->updated_by = Auth::user()->id;
				$obj->save();
			    if($obj->delete()){
			    	$return = new AjaxResponse(200,'Delete Success');
			    	$return->setData(array('delete'=>'tr'));			
				}else{
					$return = new AjaxResponse(503,'Unable to delete');
				}
			}catch(ModelNotFoundException $e){			
			    $return = new AjaxResponse(404,'Object is not found',$e->getMessage());			
			}	
		}else{
			$return = new AjaxResponse(503,'Can not delete this Redirect URL');
		}	
		return $return->getJson();	
	}
}
