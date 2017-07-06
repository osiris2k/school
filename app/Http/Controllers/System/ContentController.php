<?php namespace App\Http\Controllers\System;

use App\ContentObjectType;
use App\ContentParent;
use App\Helpers\ViewHelper;
use App\Hotel;
use App\HotelContent;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use ErrorException;
use Illuminate\Http\Request;
use App\Content;
use App\ContentObject;
use App\ContentProperty;
use App\ContentValue;
use App\Language;
use App\SiteLanguages;
use App\MenuGroup;
use App\Site;
use App\Helpers\CmsHelper;

use \Auth;
use \DB;
use App\AjaxResponse;
use Illuminate\Support\Facades\Session;

class ContentController extends Controller {
	protected $data = [ ];

	public function __construct() {
		$this->middleware( 'auth' );
		$this->data['page_herader']   = 'Content Management';
		$this->data['tag_first_menu'] = 'CONTENT';
		$this->data['tag_sub_menu']   = '';
	}

    public function setDefault()
    {
        $default['page'] = "homepage";
        $default['parent'] = "";
        $default['site'] = ViewHelper::getMainSite();
        $language = CmsHelper::getInitLanguage($default['site']);
        $default['lang'] = $language->name;

        return $default;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index( $contentObjTypeId = 1 ) {

		$user = Auth::user();

		$user_site = $user->sites()->get()->lists( 'id' );

		if ( $user->role_id == 1 || $user->role_id == 4 ) {
			$this->data['objects'] = Content::all();

		} else {
			$this->data['objects'] = Content::whereIn( 'site_id', $user_site )->get();
		}

		return view( 'system.lists.content', $this->data );
	}

	public function getContentNameAjax( $id ) {
		$content = Content::find( $id );
		if ( ! empty( $content ) ) {
			return response()->json( [ 'content_name' => $content->name ], 200 );
		} else {
			return response()->json( [ 'content_name' => '' ], 200 );
		}
	}

	public function getAjax( $contentObjTypeId, $contentObjectId = 0 ) {
		$user     = Auth::user();
		$userSite = $user->sites()->get()->lists( 'id' );

		$data = $this->generateContentWithParent( $contentObjTypeId );

		$data = $this->filterContentByContentObjectId( $contentObjectId, $data );

		$data = $this->filterContentByUserSiteId( $user, $data, $userSite );

		$returnData = [ ];
		foreach ( $data->get() as $index => $item ) {
			$returnData[ $index ]['id']             = $item->id;
			$returnData[ $index ]['slug']           = $item->slug;
			$returnData[ $index ]['level']          = $item->level;
			$returnData[ $index ]['content_name']   = ( $item->level == 1 )
				? $item->name
				: str_pad( "", ( $item->level * 1 ), '-', STR_PAD_LEFT ) . $item->name;
			$returnData[ $index ]['parent_content'] = $item->contentParents()->orderBy( 'name', 'asc' )->get();
			$returnData[ $index ]['active']         = $item->active;
			$returnData[ $index ]['site_name']      = $item->site_name;
			/** Now use 1-1 */
			$returnData[ $index ]['hotel_name'] = ( $item->hotel->first() ) ? $item->hotel->first()->name : '-';

			$returnData[ $index ]['content_object_name'] = ucfirst( str_replace( '_', ' ', $item->content_object_name ) );
		}
		$return['data'] = $returnData;

		return response()->json( $return, 200 );

	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create( $contentObjId ) {
		$user     = Auth::user();
		$userSite = $user->sites()->get()->lists( 'id' );
        $this->data['id']                     = 0;
		$this->data['object_id']              = $contentObjId;
		$this->data['content_object']         = ContentObject::find( $contentObjId );
		$contentObjType                       = $this->data['content_object']->content_object_type;
		$this->data['content_object_type_id'] = $contentObjType->id;
		$this->data['content_object_id']      = $this->data['object_id'];
		$this->data['content_object_type']    = $contentObjType;

		$this->getPageSetting();

		$this->data['form_type'] = "CREATE";
		$this->data['title']     = 'Title';
		$this->data['action']    = 'system/contents';
		$this->data['method']    = 'POST';
		$this->data['languages'] = Language::whereStatus( 1 )->orderBy( 'priority' )->get();

		if ( $this->data['content_object']->name !== 'offer' ) {
			$this->data['contents'] = $this->generateContentWithParent( config( 'content.CONTENT_TYPE_ID' ) );
			$this->data['contents'] = $this->filterContentByUserSiteId( $user, $this->data['contents'], $userSite );
			$this->data['contents'] = $this->data['contents']->get();
		} else {
			/** Offer type condition */
			$this->data['contents'] = $this->generateContentWithParent( config( 'content.CONTENT_TYPE_ID' ) );
			$this->data['contents'] = $this->filterContentByUserSiteId( $user, $this->data['contents'], $userSite );
			$this->data['contents'] = $this->data['contents']->where( function ( $query ) {
				$query->where( 'content_objects.name', '=', 'property_offer_list' )
				      ->orWhere( 'content_objects.name', '=', 'brand_offer_list' );
			} )
			                                                 ->get();
		}

		$this->getMenuGroups( $user, $userSite );
		$this->getSiteData( $user );
		$this->data['hotels'] = Hotel::all();

		$this->data['properties'] = ContentProperty::where( 'content_object_id', '=', $contentObjId )
		                                           ->with( 'DataType' )
		                                           ->orderBy( 'priority', 'ASC' )
		                                           ->orderBy( 'updated_at', 'Desc' )
		                                           ->get();

		return view( 'system.forms.content', $this->data );
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store( Request $request ) {
		$input = \Request::all();

		/**
		 * Set multiple language data
		 */
		$initialFormData      = $input['initial_form_data'][0];
		$initialFormParseData = [ ];
		$otherFormParseData   = [ ];
		parse_str( $initialFormData, $initialFormParseData );

		if ( $input['slug'] == '' ) {
			$input['slug'] = \App\Helpers\CmsHelper::createSlug( $input['name'] );
		} else {
			$input['slug'] = \App\Helpers\CmsHelper::createSlug( $input['slug'] );
		}

		// validate
		$prepare_rule     = $this->contentPrepareRule( $input['content_object_id'] );
		$rule             = \App\Rule::prepareRule( $prepare_rule, $initialFormParseData );
		$rule['name']     = 'required';
		$rule['slug']     = 'required|unique:contents,slug,null,id,site_id,' . $input['site_id'] . '|not_in:' . env( 'FOLDERS_PUBLIC' ) . '"_callback.';
		$inputForValidate = array_merge( $initialFormParseData, $input );

		$validate = \Validator::make( $inputForValidate, $rule );
		if ( $validate->fails() ) {
			$messages = $validate->messages();

			return response()->json( [
				'REDIRECT_URL' => "",
				'STATUS'       => "FAILED",
				'MESSAGE'      => $validate->errors()
			] );
		}
		// end validate

		try {
			$contentObj = ContentObject::find( $input['content_object_id'] );

			$site_id                    = $input['site_id'];
			$content_object_id          = $input['content_object_id'];
			$content                    = new Content();
			$content->content_object_id = $content_object_id;
			$content->site_id           = $site_id;
			$content->parent_content_id = 0;
			$content->name              = $input['name'];
			$content->slug              = $input['slug'];
			$content->allow_cross_site  = isset( $input['allow_cross_site'] ) ? $input['allow_cross_site'] : 0;
			$content->active            = isset( $input['active'] ) ? $input['active'] : 0;
			$content->save();

			/** Sync multi parents  */
			$parentArray = empty( $input['parent_id'] ) ? [ ] : $input['parent_id'];
			$content->contentParents()->sync( $parentArray );

			/** Sync Hotel feature (Multiple Properties) */
			if ( isset( $input['hotel_id'] ) && ! empty( $input['hotel_id'] ) ) {
				$content->hotel()->sync( [ $input['hotel_id'] ] );
			}

			$content_id                   = $content->id;
			$content_properties           = ContentProperty::where( 'content_object_id', '=', $content_object_id )->with( 'dataType' )->get();
			$options['content_object_id'] = $content_object_id;

			if ( $input['menu_group_id'] != '' ) {
				$menu                = new \App\Menu();
				$menu->content_id    = $content_id;
				$menu->menu_title    = $input['name'];
				$menu->menu_group_id = $input['menu_group_id'];
				$menu->save();
			}

			/**
			 * Save initial language value
			 */
			$options['language_id'] = $initialFormParseData['language_id'];
			$tmp                    = \App\CmsType::processType( $content_properties, $initialFormParseData, false, false, $options );
			$content->contentProperties()->sync( $tmp );

			/**
			 * Save other language value
			 */
			if ( isset( $input['other_form_data'] ) ) {
				$otherFormData = $input['other_form_data'];
				foreach ( $otherFormData as $otherFormItem ) {
					parse_str( $otherFormItem, $otherFormParseData );
					$options['language_id'] = $otherFormParseData['language_id'];
					$tmp                    = \App\CmsType::processType( $content_properties, $otherFormParseData, false, false, $options, $initialFormParseData );
					$content->contentProperties()->attach( $tmp );
				}
			}

			$status   = 'SUCCESS';
			$messages = 'Save successful.';
		} catch ( ErrorException $e ) {
			$status   = 'FAILED';
			$messages = $e->getCode() . "\n";
			$messages .= $e->getMessage() . "\n";
			$messages .= $e->getLine() . "\n";
		}

		$redirectUrl = '';
		Session::flash( 'PROCESS_STATUS', $status );
		Session::flash( 'PROCESS_STATUS_MESSAGE', $messages );
		if ( $input['bt_state'] == 'SAVE_AND_EXIT' ) {
			if ( $input['content_object_type_id'] == 1 ) {
				$redirectUrl = url( 'system/contents/' . $contentObj->content_object_type->id );
			} else {
				$redirectUrl = url( 'system/contents/' . $input['content_object_type_id'] . '/' . $content_object_id );
			}
		} else {
			$redirectUrl = url( 'system/contents/' . $content->id . '/edit' );
		}

		return response()->json( [
			'REDIRECT_URL' => $redirectUrl,
			'STATUS'       => $status,
			'MESSAGE'      => $messages
		] );

	}

	public function show( $contentObjTypeId, $contentObjId = 0 ) {
		$this->data['content_object_type_id'] = $contentObjTypeId;
		$this->data['content_object_id']      = $contentObjId;
		$this->data['content_object_type']    = ContentObjectType::find( $contentObjTypeId );

		if ( $contentObjId != 0 ) {
			$this->data['content_object'] = ContentObject::find( $contentObjId );
		}
		$this->getPageSetting();

		$user = Auth::user();

		$user_site = $user->sites()->get()->lists( 'id' );

		if ( $user->role_id == 1 || $user->role_id == 4 ) {
			$this->data['objects'] = Content::all();

		} else {
			$this->data['objects'] = Content::whereIn( 'site_id', $user_site )->get();
		}

		$this->data['content_objects'] = ContentObject::whereHas( 'content_object_type', function ( $query ) use ( $contentObjTypeId ) {
			$query->where( 'content_object_types.id', $contentObjTypeId );
		} )
		                                              ->get();

		return view( 'system.lists.content', $this->data );
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function edit( $id ) {

		$user = Auth::user();

		$userSite = $user->sites()->get()->lists( 'id' );

		$content = Content::find( $id );

		$this->data['form_type']      = "EDIT";
		$this->data['action']         = 'system/contents/' . $id;
		$this->data['method']         = 'PUT';
		$this->data['id']             = $id;
		$this->data['obj']            = $content;
		$this->data['parent_ids']     = $content->contentParents->lists( 'id' );
		$this->data['properties']     = ContentProperty::where( 'content_object_id', '=', $this->data['obj']->content_object_id )->with( 'DataType' )->orderBy( 'priority', 'ASC' )->orderBy( 'updated_at', 'Desc' )->get();
		$this->data['content_object'] = ContentObject::find( $this->data['obj']->content_object_id );
		$this->data['languages']      = Language::whereStatus( 1 )->orderBy( 'priority' )->get();
		//$this->data['languages']      = CmsHelper::getSiteLanguages($content->site_id);
		$this->data['obj_menu'] = $menu = \App\Menu::where( 'content_id', '=', $id )->first();

		$this->data['object_id'] = $this->data['content_object']->id;

		$contentObjType                       = $this->data['content_object']->content_object_type;
		$this->data['content_object_type_id'] = $contentObjType->id;
		$this->data['content_object_id']      = $this->data['object_id'];
		$this->data['content_object_type']    = $contentObjType;

		$this->getPageSetting();

		if ( $this->data['content_object']->name !== 'offer' ) {
			$this->data['contents'] = $this->generateContentWithParent( config( 'content.CONTENT_TYPE_ID' ) );
			$this->data['contents'] = $this->filterContentByUserSiteId( $user, $this->data['contents'], $userSite );
			$this->data['contents'] = $this->filterByContentId( $this->data['contents'], $id, '!=' );
			$this->data['contents'] = $this->data['contents']->get();
		} else {
			/** Offer type condition */
			$this->data['contents'] = $this->generateContentWithParent( config( 'content.CONTENT_TYPE_ID' ) );
			$this->data['contents'] = $this->filterContentByUserSiteId( $user, $this->data['contents'], $userSite );
			$this->data['contents'] = $this->filterByContentId( $this->data['contents'], $id, '!=' );
			$this->data['contents'] = $this->data['contents']->where( function ( $query ) {
				$query->where( 'content_objects.name', '=', 'property_offer_list' )
				      ->orWhere( 'content_objects.name', '=', 'brand_offer_list' );
			} )
			                                                 ->get();
		}

		$this->getMenuGroups( $user, $userSite );
		$this->getSiteData( $user );
		$this->data['hotels'] = Hotel::all();

		return view( 'system.forms.content', $this->data );
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function update( $id ) {
		$input = \Request::all();

		/**
		 * Set multiple language data
		 */
		$initialFormData      = $input['initial_form_data'][0];
		$initialFormParseData = [ ];
		$otherFormParseData   = [ ];
		parse_str( $initialFormData, $initialFormParseData );

		// validate
		$prepare_rule = $this->contentPrepareRule( $input['content_object_id'] );
		$rule         = \App\Rule::prepareRule( $prepare_rule, $input );
		$rule['name'] = 'required';
		$rule['slug'] = 'required|unique:contents,slug,' . $id . ',id,site_id,' . $input['site_id'] . '|not_in:' . env( 'FOLDERS_PUBLIC' );

		if ( ! empty( $input['slug'] ) ) {
			$input['slug'] = \App\Helpers\CmsHelper::createSlug( $input['slug'] );
		} else {
			$input['slug'] = \App\Helpers\CmsHelper::createSlug( $input['name'] );
		}

		$inputForValidate = array_merge( $initialFormParseData, $input );
		$validate         = \Validator::make( $inputForValidate, $rule );
		if ( $validate->fails() ) {
			$messages = $validate->messages();

			return response()->json( [
				'REDIRECT_URL' => "",
				'STATUS'       => "FAILED",
				'MESSAGE'      => $validate->errors()
			] );
		}
		// end validate

		try {
			$contentObj        = ContentObject::find( $input['content_object_id'] );
			$site_id           = $input['site_id'];
			$content           = Content::find( $id );
			$content_object_id = $content->content_object_id;
			$content->site_id  = $site_id;
			$content->slug     = $input['slug'];
			$content->name     = $input['name'];
			if ( isset( $input['active'] ) ) {
				$content->active = $input['active'];
			} else {
				$content->active = 0;
			}
			$content->save();

			/** Sync multi parents  */
			$parentArray = empty( $input['parent_id'] ) ? [ ] : $input['parent_id'];
			$content->contentParents()->sync( $parentArray );

			if ( isset( $input['hotel_id'] ) && ! empty( $input['hotel_id'] ) ) {
				$content->hotel()->sync( [ $input['hotel_id'] ] );
			}

			$content_id         = $content->id;
			$content_properties = ContentProperty::where( 'content_object_id', '=', $content_object_id )->with( 'dataType' )->get();

			$options['content_id']        = $content->id;
			$options['content_object_id'] = $content_object_id;
			$option['update']             = 'update';

			/**
			 * Update initial language value
			 */
			$options['language_id'] = $initialFormParseData['language_id'];
			$tmp                    = \App\CmsType::processType( $content_properties, $initialFormParseData, false, false, $options );
			foreach ( $tmp as $key => $value ) {
				$content_property_id = $key;
				$content_value       = ContentValue::where( 'content_id', '=', $content_id )->where( 'content_property_id', '=', $content_property_id )->where( 'language_id', '=', $options['language_id'] )->first();

				if ( $content_value ) {
					$content_value->content_id          = $content_id;
					$content_value->language_id         = $value['language_id'];
					$content_value->content_property_id = $content_property_id;
					$content_value->value               = $value['value'];
					$content_value->content_object_id   = $value['content_object_id'];
					$content_value->save();
				} else {
					$content_value                      = new ContentValue();
					$content_value->content_id          = $content_id;
					$content_value->language_id         = $options['language_id'];
					$content_value->content_property_id = $content_property_id;
					$content_value->value               = $value['value'];
					$content_value->content_object_id   = $content_object_id;
					$content_value->save();
				}
			}
			/**
			 * Update other language value
			 */

			if ( isset( $input['other_form_data'] ) ) {
				$otherFormData = $input['other_form_data'];
				foreach ( $otherFormData as $otherFormItem ) {
					parse_str( $otherFormItem, $otherFormParseData );
					$options['language_id'] = $otherFormParseData['language_id'];
					$tmp                    = \App\CmsType::processType( $content_properties, $otherFormParseData, false, false, $options );
					foreach ( $tmp as $key => $value ) {
						$content_property_id = $key;
						$content_value       = ContentValue::where( 'content_id', '=', $content_id )->where( 'content_property_id', '=', $content_property_id )->where( 'language_id', '=', $options['language_id'] )->first();

						if ( $content_value ) {
							$content_value->content_id          = $content_id;
							$content_value->language_id         = $value['language_id'];
							$content_value->content_property_id = $content_property_id;
							$content_value->value               = $value['value'];
							$content_value->content_object_id   = $value['content_object_id'];
							$content_value->save();
						} else {
							$content_value                      = new ContentValue();
							$content_value->content_id          = $content_id;
							$content_value->language_id         = $options['language_id'];
							$content_value->content_property_id = $content_property_id;
							$content_value->value               = $value['value'];
							$content_value->content_object_id   = $content_object_id;
							$content_value->save();
						}
					}
				}
			}

			if ( $input['menu_group_id'] != '' ) {
				$menu = \App\Menu::where( 'content_id', '=', $content_id )->first();
				if ( $menu ) {
					$menu->menu_group_id = $input['menu_group_id'];
					$menu->save();
				} else {
					$menu                = new \App\Menu();
					$menu->content_id    = $content_id;
					$menu->menu_title    = $input['name'];
					$menu->menu_group_id = $input['menu_group_id'];
					$menu->save();
				}
			}
			$status   = 'SUCCESS';
			$messages = 'Update successful.';
		} catch ( ErrorException $e ) {
			$status = 'FAILED';
//            $messages = 'Update failed, please try again.';
			$messages = $e->getCode() . "\n";
			$messages .= $e->getMessage() . "\n";
			$messages .= $e->getLine() . "\n";

		}

		$redirectUrl = '';
		if ( $input['bt_state'] == 'SAVE_AND_EXIT' ) {
			Session::flash( 'PROCESS_STATUS', $status );
			Session::flash( 'PROCESS_STATUS_MESSAGE', $messages );
			if ( $input['content_object_type_id'] == 1 ) {
				$redirectUrl = url( 'system/contents/' . $contentObj->content_object_type->id );
			} else {
				$redirectUrl = url( 'system/contents/' . $input['content_object_type_id'] . '/' . $content_object_id );
			}
		}

		return response()->json( [
			'REDIRECT_URL' => $redirectUrl,
			'STATUS'       => $status,
			'MESSAGE'      => $messages
		] );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 *
	 * @return Response
	 */
	public function destroy( $id ) {
		try {
			$obj             = Content::findOrFail( $id );
			$obj->slug       = 'del-'.$obj->slug;
			$obj->updated_by = Auth::user()->id;
			$obj->save();
			if ( $obj->delete() ) {
				/** Delete relation */
				$obj->hotel()->detach();

				$return = new AjaxResponse( 200, 'Delete Success' );
				$return->setData( array( 'delete' => 'tr' ) );
			} else {
				$return = new AjaxResponse( 503, 'Unable to delete' );
			}
		} catch ( ModelNotFoundException $e ) {
			$return = new AjaxResponse( 404, 'Object is not found', $e->getMessage() );
		}

		return $return->getJson();
	}

	public function contentPrepareRule( $content_object_id ) {
		$content_properties = ContentProperty::with( 'dataType' )->where( 'content_object_id', '=', $content_object_id )->get();
		$prepare_rule       = array();
		foreach ( $content_properties as $content_property ) {
			$tmp['name']          = $content_property->name;
			$tmp['variable_name'] = $content_property->variable_name;
			$tmp['type']          = $content_property->dataType->name;
			$tmp['option']        = $content_property->options;
			$tmp['is_mandatory']  = $content_property->is_mandatory;
			$prepare_rule[]       = $tmp;
		}

		return $prepare_rule;
	}

	// TODO Clean code
	public function reorder( $reorderType = 'site', $refId = 0, $parentId = 0 ) {

		$this->data['tag_sub_menu'] = 'CONTENT_RERODER';
		$user                       = Auth::user();
		$user_site                  = $user->sites()->get()->lists( 'id' );

		if ( $user->role_id == 1 || $user->role_id == 4 ) {
			$this->data['sites'] = Site::all();
			$user_site           = $this->data['sites']->lists( 'id' );
		} else {
			$this->data['sites'] = Site::whereIn( 'id', $user_site )->get();
		}

		/** Get Hotel */
		$hotelId           = 0;
		$contentIdsInHotel = [ 0 ];
		if ( config( 'hotel.ENABLE' ) === true ) {
			$this->data['hotels'] = Hotel::all();
			$hotelId              = $refId;
			$contentIdsInHotel    = HotelContent::select( 'content_id' )->distinct( 'content_id' )->get()->toArray();
		}

		$this->data['reorder_type']            = $reorderType;
		$this->data['content_objects']         = [ ];
		$this->data['selected_site_id']        = 0;
		$this->data['selected_content_obj_id'] = 0;
		$this->data['contents']                = [ ];
		$this->data['content_lists']           = [ ];
		$this->data['content_hierarchy']       = [ ];
		$this->data['selected_site_id']        = ( $refId == 0 ) ? $user_site[0] : $refId;
		$this->data['selected_content_id']     = $parentId;

		$this->data['content_objects'] = ContentObject::select( 'id', 'name' )
		                                              ->get();

		/** Get parent ids */
		$parentIds = ContentParent::select( 'parent_id' )
		                          ->distinct( 'parent_id' )
		                          ->get()
		                          ->toArray();

		/** Get Parent contents by a parent ids */
		$this->data['content_lists'] = Content::select( 'id', 'site_id', 'content_object_id', 'name' )
		                                      ->whereIn( 'content_object_id', $this->data['content_objects']->lists( 'id' ) )
		                                      ->whereIn( 'id', $parentIds )
		                                      ->orderBy( 'name', 'ASC' );

		if ( $reorderType === 'site' ) {
			$this->data['content_lists'] = $this->data['content_lists']
				->whereIn( 'site_id', [ $this->data['selected_site_id'] ] )
				->whereNotIn( 'id', $contentIdsInHotel )
				->get();
		} else {
			$this->data['content_lists'] = $this->data['content_lists']
				->whereHas( 'hotel', function ( $query ) use ( $hotelId ) {
					$query->where( 'hotel_id', '=', $hotelId );
				} )
				->get();
		}

		/** Get root contents or children contents  */
		if ( count( $user_site ) > 0 ) {
			$selectColumns = [
				'contents.id',
				'name',
				'site_id',
				'content_object_id'
			];

			/** Get sub content by parent id */
			if ( $parentId != 0 ) {
				$this->data['contents'] = Content::select( $selectColumns )
				                                 ->join( 'contents_parents', function ( $join ) use ( $parentId ) {
					                                 $join->on( 'contents.id', '=', 'contents_parents.content_id' )
					                                      ->where( 'parent_id', '=', $parentId );
				                                 } )
				                                 ->distinct()
				                                 ->orderBy( 'display_order', 'ASC' );
			} else {
				/** Get root contents */
				$this->data['contents'] = Content::select( $selectColumns )
				                                 ->whereIn( 'content_object_id', $this->data['content_objects']->lists( 'id' ) )
				                                 ->whereNotIn( 'id', ContentParent::distinct( 'content_id' )->lists( 'content_id' ) )
				                                 ->orderBy( 'name', 'ASC' )
				                                 ->orderBy( 'display_order', 'ASC' );
			}

			if ( $reorderType === 'site' ) {
				$this->data['contents'] = $this->data['contents']
					->whereIn( 'site_id', [ $this->data['selected_site_id'] ] )
					->whereNotIn( 'contents.id', $contentIdsInHotel )
					->get();
			} else {
				/** Hotel condition */
				$this->data['contents'] = $this->data['contents']
					->whereHas( 'hotel', function ( $query ) use ( $hotelId ) {
						$query->where( 'hotel_id', ' = ', $hotelId );
					} )
					->get();
			}
		}

		$this->data['content_reorder_html'] = $this->renderHtmlContentReOrder( $this->data['contents'] );

		return view( 'system.lists.content_reorder', $this->data );
	}

	public function renderHtmlContentReOrder( $data ) {
		$retval = '';
		foreach ( $data as $content ) {
			$retval .= ' <li class="dd-item" data-id = "' . $content->id . '" > ';
			$retval .= '<div class="dd-handle" > ' . $content->name . '</div > ';
//            if (isset($content['children'])) {
//                $retval .= ' < ol class="dd-list" > ';
//                $retval .= $this->renderHtmlContentReOrder($content['children']);
//                $retval .= ' </ol > ';
//            }
			$retval .= '</li > ';
		}

		return $retval;
	}

	public function updateContentReorder( $updateData, $parentId = 0 ) {
		foreach ( $updateData as $key => $data ) {
			$contentObj = Content::find( $data['id'] );
//            $contentObj->parent_content_id = $parentId;
			$contentObj->display_order = $key;
			$contentObj->save();
			if ( isset( $data['children'] ) ) {
				$this->updateContentReorder( $data['children'], $data['id'] );
			}
		}
	}

	public function updateOrder( Request $request ) {
		$this->updateContentReorder( $request->input( 'order_data' ) );

		return response()->json( [ 'status' => 'SUCCESS' ] );
	}

	/**
	 * Generate Content with Parent
	 * and filter by Content Object Type
	 *
	 * @param $contentObjTypeId
	 *
	 * @return mixed
	 */
	protected function generateContentWithParent( $contentObjTypeId ) {
		$data = Content::getContentWithParent()
		               ->filterByContentObjectTypeId( [ $contentObjTypeId ] );

		return $data;
	}

	/**
	 * Filter Content by ContentObjectId
	 *
	 * @param $contentObjectId
	 * @param $data
	 *
	 * @return mixed
	 */
	protected function filterContentByContentObjectId( $contentObjectId, $data ) {
		if ( $contentObjectId != 0 ) {
			$data = $data->filterByContentObjectId( [ $contentObjectId ] );

			return $data;
		}

		return $data;
	}

	/**
	 * Filter Content by User Site Id
	 *
	 * @param $user
	 * @param $data
	 * @param $userSite
	 *
	 * @return mixed
	 */
	protected function filterContentByUserSiteId( $user, $data, $userSite ) {
		if ( $user->role_id != 1 && $user->role_id != 4 ) {
			$data = $data->filterBySiteId( $userSite );

			return $data;
		}

		return $data;
	}

	/**
	 * Filter Content by Content Id
	 *
	 * @param        $data
	 * @param        $contentId
	 * @param string $condition
	 *
	 * @return mixed
	 */
	protected function filterByContentId( $data, $contentId, $condition = ' = ' ) {
		return $data->filterByContentId( $contentId, $condition );
	}

	/**
	 * Generate general Page Setting i.e. Main Menu Active, Sub Menu Active etc.
	 */
	protected function getPageSetting() {
		switch ( $this->data['content_object_type']->id ) {
			case 1:
				$this->data['tag_sub_menu'] = 'CONTENTS';
				break;
			case 2:
				$this->data['tag_first_menu'] = 'MEDIA';
				$this->data['tag_sub_menu']   = $this->data['content_object']->name;
				break;
			case 3:
				$this->data['tag_first_menu'] = 'MULTI_MEDIA';
				$this->data['tag_sub_menu']   = $this->data['content_object']->name;
				break;
            case 4:
                $this->data['tag_first_menu'] = 'LAYOUT';
                $this->data['tag_sub_menu']   = $this->data['content_object']->name;
                break;
		}
	}

	/**
	 * Get Site Data by User Site Id
	 *
	 * @param $user
	 */
	protected function getSiteData( $user ) {
		if ( $user->role_id == 1 || $user->role_id == 4 ) {
			$this->data['sites'] = Site::whereActive( 1 )->get();
		} else {
			$this->data['sites'] = $user->sites()->get();
		}
	}

	/**
	 * Get Menu Groups
	 *
	 * @param $user
	 * @param $userSite
	 */
	protected function getMenuGroups( $user, $userSite ) {
		if ( $user->role_id == 1 || $user->role_id == 4 ) {
			$this->data['menu_groups'] = MenuGroup::all();
		} else {
			$this->data['menu_groups'] = MenuGroup::whereIn( 'site_id', $userSite )->get();
		}
	}

	private function generateContentHierarchy( $contents ) {
		foreach ( $contents as $contentItem ) {
			if ( $contentItem['parent_id'] == 0 ) {
				$this->data['content_hierarchy'][ $contentItem['id'] ] = [
					'id'        => $contentItem['id'],
					'name'      => $contentItem['name'],
					'parent_id' => $contentItem['parent_id']
				];
				unset( $contents[ $contentItem['id'] ] );
				foreach ( $contents as $child1 ) {
					if ( $child1['parent_id'] == $contentItem['id'] ) {
						$this->data['content_hierarchy'][ $contentItem['id'] ]
						['children'][ $child1['id'] ] = [
							'id'        => $child1['id'],
							'name'      => $child1['name'],
							'parent_id' => $child1['parent_id']
						];
						unset( $contents[ $child1['id'] ] );
						foreach ( $contents as $child2 ) {
							if ( $child2['parent_id'] == $child1['id'] ) {
								$this->data['content_hierarchy'][ $contentItem['id'] ]
								['children'][ $child1['id'] ]
								['children'][ $child2['id'] ] = [
									'id'        => $child2['id'],
									'name'      => $child2['name'],
									'parent_id' => $child2['parent_id']
								];
								unset( $contents[ $child2['id'] ] );
								foreach ( $contents as $child3 ) {
									if ( $child3['parent_id'] == $child2['id'] ) {
										$this->data['content_hierarchy'][ $contentItem['id'] ]
										['children'][ $child1['id'] ]
										['children'][ $child2['id'] ]
										['children'][ $child3['id'] ] = [
											'id'        => $child3['id'],
											'name'      => $child3['name'],
											'parent_id' => $child3['parent_id']
										];
										unset( $contents[ $child3['id'] ] );
										foreach ( $contents as $child4 ) {
											if ( $child4['parent_id'] == $child3['id'] ) {
												$this->data['content_hierarchy'][ $contentItem['id'] ]
												['children'][ $child1['id'] ]
												['children'][ $child2['id'] ]
												['children'][ $child3['id'] ]
												['children'][ $child4['id'] ] = [
													'id'        => $child4['id'],
													'name'      => $child4['name'],
													'parent_id' => $child4['parent_id']
												];
												unset( $contents[ $child4['id'] ] );
												foreach ( $contents as $child5 ) {
													if ( $child5['parent_id'] == $child4['id'] ) {
														$this->data['content_hierarchy'][ $contentItem['id'] ]
														['children'][ $child1['id'] ]
														['children'][ $child2['id'] ]
														['children'][ $child3['id'] ]
														['children'][ $child4['id'] ]
														['children'][ $child5['id'] ] = [
															'id'        => $child5['id'],
															'name'      => $child5['name'],
															'parent_id' => $child5['parent_id']
														];
														unset( $contents[ $child5['id'] ] );
													}
												}
											}
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/**
     * Todo : Preview content
     */
	public function preview( $id ){

        $input = \Request::all();
        $content_object_id = $input['content_object_id'];
        $content_properties           = ContentProperty::where( 'content_object_id', '=', $content_object_id )->with( 'dataType' )->get();


        $contentObj = ContentObject::find( $input['content_object_id'] );
        $language_id = $input['language_id'];
        dd($input);
dd($contentObj->name);
        $default = $this->setDefault();
        $site_id = $default['site']->id;
        $siteInfo = \App\Helpers\ViewHelper::getSiteInformation($site_id);

        \App::singleton('user_current_language', function () use ($input, $default) {
            $language_id = $input['language_id'];
            return $language_id;
        });
        // $input is array
        // $contentObj is array

        if($id != 0){

        }else {
            $parent_id = count($input['parent_id']) > 0 ? (int)$input['parent_id'][0] : 0;
            if($parent_id != 0) {
                $parentContent = ViewHelper::getContentById($parent_id,$language_id);
                $mergeContent = array_merge($parentContent);
                $subContent = ViewHelper::getSubContent($parent_id,$language_id);
                $subContent[] = $input;
                $data['site_id'] = $site_id;
                $data['site'] = $siteInfo;
                $data['currentPage'] = $mergeContent;
                $data['currentPage']['sub_content'] = $subContent;

                return view( 'templates.songsaa.page', $data );
            }else{
                $data = [
                    'currentPage' => [
                        'sub_content' => [

                        ]
                    ]
                ];
            }
        }
    }
}
