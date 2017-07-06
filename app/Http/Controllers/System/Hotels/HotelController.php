<?php namespace App\Http\Controllers\System\Hotels;

use App\AjaxResponse;
use App\HotelValue;
use App\Language;
use App\Helpers\HotelHelper;
use Error;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use DB;
use Request;
use Session;
use File;
use Validator;
use Auth;
use App\Hotel;
use App\HotelProperty;
use App\Http\Controllers\Controller;

/**
 * Class HotelController
 *
 * Use for hotel management
 *
 * @package App\Http\Controllers
 */
class HotelController extends Controller
{
	protected $data = [];

	public function __construct()
	{
		$this->middleware('auth');
		$this->data['page_herader'] = 'Hotel Management';
		$this->data['tag_first_menu'] = 'HOTEL';
	}

	/**
	 * Generate general Page Setting i.e. Main Menu Active, Sub Menu Active etc.
	 */
	protected function getPageSetting()
	{
		switch ($this->data['form_type']) {
			case 'CREATE':
				$this->data['tag_sub_menu'] = 'CREATE_HOTEL';
				break;
			case 2:
				$this->data['tag_first_menu'] = 'MEDIA';
				$this->data['tag_sub_menu'] = $this->data['content_object']->name;
				break;
			case 3:
				$this->data['tag_first_menu'] = 'MULTI_MEDIA';
				$this->data['tag_sub_menu'] = $this->data['content_object']->name;
				break;
            case 4:
                $this->data['tag_first_menu'] = 'LAYOUT';
                $this->data['tag_sub_menu']   = $this->data['content_object']->name;
                break;
		}
	}

	public function hotelPrepareRule()
	{
		$hotel_properties = HotelProperty::with('dataType')->get();
		$prepare_rule = array();
		foreach ($hotel_properties as $hotel_property) {
			$tmp['name'] = $hotel_property->name;
			$tmp['variable_name'] = $hotel_property->variable_name;
			$tmp['type'] = $hotel_property->dataType->name;
			$tmp['option'] = $hotel_property->options;
			$tmp['is_mandatory'] = $hotel_property->is_mandatory;
			$prepare_rule[] = $tmp;
		}

		return $prepare_rule;
	}

	public function getGetHotelData()
	{
		$returnData['data'] = Hotel::listData();

		return response()->json($returnData);
	}

	public function getHotelList()
	{
		$this->data['tag_sub_menu'] = 'HOTEL_LIST';


		return view('system.lists.hotel', $this->data);
	}

	public function getCreateHotel()
	{
		$this->data['form_type'] = "CREATE";
		$this->data['title'] = 'Create Hotel';
		$this->data['action'] = route('hotel.system.post_save_hotel');
		$this->data['method'] = 'POST';

		$this->data['other_hotel_homepage_ids'] = HotelHelper::getAllHotelHomepageIds();
		$this->data['contents'] = HotelHelper::getContents($this->data['other_hotel_homepage_ids']);
		$this->data['languages'] = Language::whereStatus(1)->orderBy('priority')->get();
		$this->data['hotel_properties'] = HotelProperty::with('DataType')->orderBy('priority', 'asc')->get();

		/** Define for language condition in views render folder remove later*/
		$this->data['content_object'] = new Hotel();

		$this->getPageSetting();

		return view('system.forms.hotel', $this->data);
	}

	public function getEditHotel($hotelId)
	{
		$this->data['obj'] = Hotel::find($hotelId);
		$this->data['other_hotel_homepage_ids'] = HotelHelper::getAllHotelHomepageIds();
		$this->data['hotel_homepage'] = $this->data['obj']->contents()->where('hotels_contents.is_homepage_content', '=', 1)->first();

		$this->data['title'] = 'Edit Hotel';
		$this->data['form_type'] = "EDIT";
		$this->data['action'] = route('hotel.system.post_update_hotel', $hotelId);
		$this->data['method'] = 'POST';

		$this->data['contents'] = HotelHelper::getContents($this->data['other_hotel_homepage_ids']);
		$this->data['hotel_properties'] = HotelProperty::with('DataType')->orderBy('priority', 'asc')->get();
		$this->data['languages'] = Language::whereStatus(1)->orderBy('priority')->get();

		/** Define for language condition in views render folder remove later*/
		$this->data['content_object'] = $this->data['obj'];

		$this->getPageSetting();

		return view('system.forms.hotel', $this->data);
	}

	public function postSaveHotel()
	{
		$input = Request::all();

		if ($input['slug'] == '') {
			$input['slug'] = \App\Helpers\CmsHelper::createSlug($input['name']);
		} else {
			$input['slug'] = \App\Helpers\CmsHelper::createSlug($input['slug']);
		}

		// validate
		$prepare_rule = $this->hotelPrepareRule();
		$rule = \App\Rule::prepareRule($prepare_rule, $input);
		$rule['name'] = 'required';
		$rule['homepage'] = 'required';
		$rule['slug'] = 'required|unique:hotels,slug,null';
		$validate = Validator::make($input, $rule);
		if ($validate->fails()) {
			return response()->json([
				'REDIRECT_URL' => "",
				'STATUS'       => "FAILED",
				'MESSAGE'      => $validate->errors()
			]);
		}
		// end validate

		DB::beginTransaction();

		try {
			$hotel = new Hotel();
			$hotel->name = $input['name'];
			$hotel->slug = $input['slug'];
			$hotel->template = (isset($input['template']) && !empty($input['template'])) ? $input['template'] : 'property';
			$hotel->active = isset($input['active']) ? $input['active'] : 0;
			$hotel->created_by = Auth::user()->id;
			$hotel->updated_by = Auth::user()->id;
			$hotel->save();

			/**
			 * Sync hotels_contents relation
			 */
			if (isset($input['homepage']) && !empty($input['homepage'])) {
				$hotel->contents()->sync([
					$input['homepage'] => ['is_homepage_content' => 1]
				]);
			}

			/**
			 * Set multiple language data
			 */
			$initialFormData = $input['initial_form_data'][0];
			$initialFormParseData = [];
			$otherFormParseData = [];
			parse_str($initialFormData, $initialFormParseData);

			/**
			 * Save initial language value
			 */
			$options['language_id'] = $initialFormParseData['language_id'];
			$hotelProperties = HotelProperty::with('dataType')->get();
			$tmp = \App\CmsType::processType($hotelProperties, $initialFormParseData, false, false, $options);
			$hotel->hotelProperties()->sync($tmp);

			/**
			 * Save other language value
			 */
			if (isset($input['other_form_data'])) {
				$otherFormData = $input['other_form_data'];
				foreach ($otherFormData as $otherFormItem) {
					parse_str($otherFormItem, $otherFormParseData);
					$options['language_id'] = $otherFormParseData['language_id'];
					$tmp = \App\CmsType::processType($hotelProperties, $otherFormParseData, false, false, $options, $initialFormParseData);
					$hotel->hotelProperties()->attach($tmp);
				}
			}

			$status = 'SUCCESS';
			$messages = 'Save successful.';

			DB::commit();

		} catch (Error $e) {
			$status = 'FAILED';
			$messages = $e->getCode() . "\n";
			$messages .= $e->getMessage() . "\n";
			$messages .= $e->getLine() . "\n";

			DB::rollback();
		}

		$redirectUrl = '';
		Session::flash('PROCESS_STATUS', $status);
		Session::flash('PROCESS_STATUS_MESSAGE', $messages);

		if ($input['bt_state'] == 'SAVE_AND_EXIT') {
			$redirectUrl = route('hotel.system.get_hotel_list');
		} else {
			$redirectUrl = route('hotel.system.get_edit_hotel', $hotel->id);
		}

		return response()->json([
			'REDIRECT_URL' => $redirectUrl,
			'STATUS'       => $status,
			'MESSAGE'      => $messages
		]);

	}

	public function postUpdateHotel($hotelId)
	{
		$input = \Request::all();

		// validate
		$prepare_rule = $this->hotelPrepareRule();
		$rule = \App\Rule::prepareRule($prepare_rule, $input);
		$rule['name'] = 'required';
		$rule['homepage'] = 'required';
		$rule['slug'] = 'required|unique:hotels,slug,' . $hotelId . ',id';

		if (!empty($input['slug'])) {
			$input['slug'] = \App\Helpers\CmsHelper::createSlug($input['slug']);
		} else {
			$input['slug'] = \App\Helpers\CmsHelper::createSlug($input['name']);
		}

		$validate = Validator::make($input, $rule);
		if ($validate->fails()) {
			return response()->json([
				'REDIRECT_URL' => "",
				'STATUS'       => "FAILED",
				'MESSAGE'      => $validate->errors()
			]);
		}
		// end validate

		DB::beginTransaction();

		try {
			$hotel = Hotel::find($hotelId);
			$hotel->name = $input['name'];
			$hotel->slug = $input['slug'];
			$hotel->template = (isset($input['template']) && !empty($input['template'])) ? $input['template'] : 'property';
			$hotel->active = isset($input['active']) ? $input['active'] : 0;
			$hotel->updated_by = Auth::user()->id;
			$hotel->save();

			/**
			 * Sync hotels_contents relation
			 */
			if (isset($input['homepage']) && !empty($input['homepage'])) {
				$hotel->contents()->detach($input['old_homepage']);
				$hotel->contents()->attach([
					$input['homepage'] => ['is_homepage_content' => 1]
				]);
			}

			$hotel_id = $hotel->id;
			$hotel_properties = HotelProperty::with('dataType')->get();

			$options['hotel_id'] = $hotel->id;
			$option['update'] = 'update';

			/**
			 * Set multiple language data
			 */
			$initialFormData = $input['initial_form_data'][0];
			$initialFormParseData = [];
			$otherFormParseData = [];
			parse_str($initialFormData, $initialFormParseData);
			/**
			 * Update initial language value
			 */
			$options['language_id'] = $initialFormParseData['language_id'];
			$tmp = \App\CmsType::processType($hotel_properties, $initialFormParseData, false, false, $options);
			foreach ($tmp as $key => $value) {
				$hotel_property_id = $key;
				$hotel_value = HotelValue::where('hotel_id', '=', $hotel_id)
				                         ->where('hotel_property_id', '=', $hotel_property_id)
				                         ->where('language_id', '=', $options['language_id'])
				                         ->first();

				if ($hotel_value) {
					$hotel_value->hotel_id = $hotel_id;
					$hotel_value->hotel_property_id = $hotel_property_id;
					$hotel_value->language_id = $value['language_id'];
					$hotel_value->value = $value['value'];
					$hotel_value->save();
				} else {
					$hotel_value = new HotelValue();
					$hotel_value->hotel_id = $hotel_id;
					$hotel_value->language_id = $options['language_id'];
					$hotel_value->hotel_property_id = $hotel_property_id;
					$hotel_value->value = $value['value'];
					$hotel_value->save();
				}
			}

			/**
			 * Update other language value
			 */
			if (isset($input['other_form_data'])) {
				$otherFormData = $input['other_form_data'];
				foreach ($otherFormData as $otherFormItem) {
					parse_str($otherFormItem, $otherFormParseData);
					$options['language_id'] = $otherFormParseData['language_id'];
					$tmp = \App\CmsType::processType($hotel_properties, $otherFormParseData, false, false, $options);
					foreach ($tmp as $key => $value) {
						$hotel_property_id = $key;
						$hotel_value = HotelValue::where('hotel_id', '=', $hotel_id)
						                         ->where('hotel_property_id', '=', $hotel_property_id)
						                         ->where('language_id', '=', $options['language_id'])
						                         ->first();

						if ($hotel_value) {
							$hotel_value->hotel_id = $hotel_id;
							$hotel_value->hotel_property_id = $hotel_property_id;
							$hotel_value->language_id = $value['language_id'];
							$hotel_value->value = $value['value'];
							$hotel_value->save();
						} else {
							$hotel_value = new HotelValue();
							$hotel_value->hotel_id = $hotel_id;
							$hotel_value->hotel_property_id = $hotel_property_id;
							$hotel_value->language_id = $options['language_id'];
							$hotel_value->value = $value['value'];
							$hotel_value->save();
						}
					}
				}
			}

			$status = 'SUCCESS';
			$messages = 'Update successful.';

			DB::commit();
		} catch (Error $e) {
			$status = 'FAILED';
			$messages = $e->getCode() . "\n";
			$messages .= $e->getMessage() . "\n";
			$messages .= $e->getLine() . "\n";

			DB::rollback();
		}

		$redirectUrl = '';
		if ($input['bt_state'] == 'SAVE_AND_EXIT') {
			Session::flash('PROCESS_STATUS', $status);
			Session::flash('PROCESS_STATUS_MESSAGE', $messages);

			$redirectUrl = route('hotel.system.get_hotel_list');
		}

		return response()->json([
			'REDIRECT_URL' => $redirectUrl,
			'STATUS'       => $status,
			'MESSAGE'      => $messages
		]);
	}

	public function deleteUpdateHotel($id)
	{
		try {
			$obj = Hotel::findOrFail($id);
			$obj->updated_by = Auth::user()->id;
			$obj->save();
			if ($obj->delete()) {
				/** Delete relation */
				$obj->contents()->detach();

				$return = new AjaxResponse(200, 'Delete Success');
				$return->setData(array('delete' => 'tr'));
			} else {
				$return = new AjaxResponse(503, 'Unable to delete');
			}
		} catch (ModelNotFoundException $e) {
			$return = new AjaxResponse(404, 'Object is not found', $e->getMessage());
		}

		return $return->getJson();
	}

}