<?php namespace App\Http\Controllers\System;

use App\Content;
use App\ContentObject;
use App\ContentObjectType;
use App\ContentParent;
use App\Hotel;
use App\HotelContent;
use App\Http\Controllers\Controller;
use App\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReorderController extends Controller {
	public function __construct() {
		$this->middleware( 'auth' );
		$this->data['page_herader']   = 'Reorder Management';
		$this->data['tag_first_menu'] = '';
		$this->data['tag_sub_menu']   = '';
	}

	public function getReorderLists( $reorderType = 'site', $contentObjTypeId = 0, $refId = 0, $parentId = 0 ) {
		$user      = Auth::user();
		$user_site = $user->sites()->get()->lists( 'id' );
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
		$this->data['content_hierarchy']       = [ ];
		$this->data['content_object_type']     = ContentObjectType::find( $contentObjTypeId );
		$this->data['selected_site_id']        = ( $refId == 0 ) ? $user_site[0] : $refId;
		$this->data['selected_content_id']     = $parentId;

		$this->data['content_objects'] = ContentObject::select( 'id', 'name' )
		                                              ->where( 'content_object_types_id', $this->data['content_object_type']->id )
		                                              ->get();

		/** Get parent ids */
		$parentIds = ContentParent::select( 'parent_id' )
		                          ->distinct( 'parent_id' )
		                          ->get()
		                          ->toArray();

		/** Get Parent contents by a parent ids */
		$this->data['content_lists'] = Content::select( 'contents.id', 'site_id', 'content_object_id', 'name' )
		                                      ->whereIn( 'contents.id', $parentIds )
		                                      ->orderBy( 'name', 'ASC' );

		if ( $reorderType === 'site' ) {
			$this->data['content_lists'] = $this->data['content_lists']
				->whereIn( 'site_id', [ $this->data['selected_site_id'] ] )
				->whereNotIn( 'contents.id', $contentIdsInHotel )
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
				'contents.name',
				'site_id',
				'content_object_id',
                'content_objects.name as template'
			];

			/** Get sub content by parent id */
			if ( $parentId != 0 ) {
				$this->data['contents'] = Content::select( $selectColumns )
				                                 ->join( 'contents_parents', function ( $join ) use ( $parentId ) {
					                                 $join->on( 'contents.id', '=', 'contents_parents.content_id' )
					                                      ->where( 'parent_id', '=', $parentId );
				                                 } )
                                                ->join( 'content_objects', function ( $join ){
					                                 $join->on( 'contents.content_object_id', '=', 'content_objects.id' );
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
						$query->where( 'hotel_id', '=', $hotelId );
					} )
					->get();
			}
		}

		$this->data['content_reorder_html'] = $this->renderHtmlContentReOrder( $this->data['contents'] );

		$this->getPageSetting();

		return view( 'system.lists.reorder', $this->data );
	}

	private function updateContentReorder( $updateData, $parentId = 0 ) {
		foreach ( $updateData as $key => $data ) {
			$contentObj = Content::find( $data['id'] );
//            $contentObj->parent_content_id = $parentId;
			$contentObj->display_order = $key;
			$contentObj->save();
//            if (isset($data['children'])) {
//                $this->updateContentReorder($data['children'], $data['id']);
//            }
		}
	}

	public function postUpdateReorder( Request $request ) {
		$this->updateContentReorder( $request->input( 'order_data' ) );

		return response()->json( [ 'status' => 'SUCCESS' ] );
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
				$this->data['tag_sub_menu']   = $this->data['content_object_type']->content_object_types_name;
				break;
			case 3:
				$this->data['tag_first_menu'] = 'MULTI_MEDIA';
				$this->data['tag_sub_menu']   = $this->data['content_object_type']->content_object_types_name;
				break;
            case 4:
                $this->data['tag_first_menu'] = 'LAYOUT';
                $this->data['tag_sub_menu']   = $this->data['content_object_type']->name;
                break;
		}

		$this->data['page_header'] = $this->data['content_object_type']->content_object_types_name . "\tReorder";
	}

	public function renderHtmlContentReOrder( $data ) {
		$retval = '';
		foreach ( $data as $content ) {
			$retval .= '<li class="dd-item" data-id="' . $content['id'] . '">';
			$retval .= '<div class="dd-handle">' . $content['name'] . ' <span style="color:#1ab394">('.str_replace("_", " ", $content['template']).')</span></div>';
//            if (isset($content['children'])) {
//                $retval .= '<ol class="dd-list">';
//                $retval .= $this->renderHtmlContentReOrder($content['children']);
//                $retval .= '</ol>';
//            }
			$retval .= '</li>';
		}

		return $retval;
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
}
