<?php namespace App\Helpers;

use Intervention\Image\Facades\Image;
use stdClass;

class LayoutHelper
{
    public static function assetPath()
    {
        return asset('templates/' . config('app.template_name')) . '/';
    }

    public static function getMockSiteProperty() {
		$headerLogo = ImagesHelper::createImageObjectFromArray([
			"image" => "images/mock/header-logo-sm.png"
		]);

    	return [
    		"header_logo" => $headerLogo,
			"header_logo_url" => "/",
			"maximum_booking_room" => 10,
			"maximum_booking_adult" => 10,
			"maximum_booking_children" => 10,
			"maximum_dropdown_item" => 7,
			"main_address" => '<p><strong>Hotel Indigo Bali Seminyak Beach</strong></p><p>&nbsp;</p><p>JI.Camplung Tanduk</p><p>No. 10 Seminyak, Bali</p><p>Indonesia</p>',
			"main_tel_no" => '62-361-2099999',
			"main_fax_no" => '62-361-2099999',
			"main_email" => 'info-hibali@ihg.com',
			"social_facebook_url" => 'http://www.facebook.com',
			"social_twitter_url" => 'http://www.twitter.com',
			"social_instagram_url" => 'http://www.instagram.com',
			"hotel_group_logo" => ImagesHelper::createImageObjectFromArray([
				"image" => 'images/hotel_group_logos/ihg_intercon_group_logo.png'
			]),
			"hotel_group_link" => "http://www.ihg.com/hotels/gb/en/",
			"footer_remark_text" => "*IHG&reg; Rewards Club not applicable to Kimpton&reg; Hotels & Restaurants; to be included at a future date.",
			"footer_copyright_text" => "&copy; 2016 IHG. All rights reserved. Most hotels are independently owned and operated.",
			"google_map_api_key" => "",
			"google_map_latitude" => "-8.695460",
			"google_map_longitude" => "115.162179",
			"google_map_marker" => ImagesHelper::createImageObjectFromArray([
				"image" => "images/ui/map_marker.png"
			]),
			"google_map_marker_link" => ""
		];
	}

	public static function getMockSidebarMenu() {
		$resultMenu = [];
		$menuArray = [
			[
				"menu_url" => "/neighborhood",
				"label" => "The neighborhood"
			],
			[
				"menu_url" => "/rooms",
				"label" => "The rooms"
			],
			[
				"menu_url" => "/experiences",
				"label" => "The experiences"
			],
			[
				"menu_url" => "/social-events",
				"label" => "The social events"
			],
			[
				"menu_url" => "/offers",
				"label" => "The offers"
			],
			[
				"menu_url" => "/gallery",
				"label" => "The gallery"
			]
		];

		foreach($menuArray as $menu) {
			$menuObject = new stdClass();
			$menuObject->menu_url = $menu["menu_url"];
			$menuObject->label = $menu["label"];
			array_push($resultMenu, $menuObject);
		}

		return $resultMenu;
	}

	public static function getMockAltSidebarMenu() {
		$resultMenu = [];
		$menuArray = [
			[
				"menu_url" => "/",
				"label" => "Home"
			],
			[
				"menu_url" => "/",
				"label" => "About Us"
			],
			[
				"menu_url" => "/",
				"label" => "Hotel Indigo"
			],
			[
				"menu_url" => "/",
				"label" => "Press & Media"
			],
			[
				"menu_url" => "/",
				"label" => "Contact"
			]
		];

		foreach($menuArray as $menu) {
			$menuObject = new stdClass();
			$menuObject->menu_url = $menu["menu_url"];
			$menuObject->label = $menu["label"];
			array_push($resultMenu, $menuObject);
		}

		return $resultMenu;
	}

	public static function getMockSocialShareMenu() {
		$resultMenu = [];
		$menuArray = [
			[
				"menu_url" => "/",
				"label" => "facebook"
			],
			[
				"menu_url" => "/",
				"label" => "twitter"
			],
			[
				"menu_url" => "/",
				"label" => "instagram"
			],
			[
				"menu_url" => "/",
				"label" => "pinterest"
			]
		];

		foreach($menuArray as $menu) {
			$menuObject = new stdClass();
			$menuObject->menu_url = $menu["menu_url"];
			$menuObject->label = $menu["label"];
			array_push($resultMenu, $menuObject);
		}

		return $resultMenu;
	}

	public static function getMockFooterMenu() {
		return [
			[
				"link_url" => "",
				"link_label" => "About IHG"
			],
			[
				"link_url" => "",
				"link_label" => "RoomKey.com"
			],
			[
				"link_url" => "",
				"link_label" => "IHG Careers"
			],
			[
				"link_url" => "",
				"link_label" => "Email Management"
			],
			[
				"link_url" => "",
				"link_label" => "IHG Global Brands"
			],
			[
				"link_url" => "/terms",
				"link_label" => "Term of Use"
			],
			[
				"link_url" => "",
				"link_label" => "Explore Hotels"
			],
			[
				"link_url" => "",
				"link_label" => "Privacy Statement"
			],
			[
				"link_url" => "",
				"link_label" => "AdChoices"
			],
			[
				"link_url" => "",
				"link_label" => "Updated Privacy Statement"
			],
			[
				"link_url" => "",
				"link_label" => "Need Help?"
			],
			[
				"link_url" => "",
				"link_label" => "Site Map"
			]
		];
	}

	public static function getMockHotelGroupMenu() {
		return [
			[
				"large_icon" => false,
				"link_url" => "http://www.ihg.com/intercontinental/hotels/gb/en/",
				"link_image" => ImagesHelper::createImageObjectFromArray([
					"image" => "images/hotel_group_logos/intercon.png"
				])
			],
			[
				"large_icon" => false,
				"link_url" => "https://www.kimptonhotels.com/",
				"link_image" => ImagesHelper::createImageObjectFromArray([
					"image" => "images/hotel_group_logos/kimpton.png"
				])
			],
			[
				"large_icon" => false,
				"link_url" => "http://cn.ihg.com/hualuxe",
				"link_image" => ImagesHelper::createImageObjectFromArray([
					"image" => "images/hotel_group_logos/hualuxe.png"
				])
			],
			[
				"large_icon" => false,
				"link_url" => "http://www.ihg.com/holidayinn/hotels/us/en/",
				"link_image" => ImagesHelper::createImageObjectFromArray([
					"image" => "images/hotel_group_logos/holiday_inn.png"
				])
			],
			[
				"large_icon" => false,
				"link_url" => "http://www.ihg.com/holidayinnexpress/hotels/us/en/",
				"link_image" => ImagesHelper::createImageObjectFromArray([
					"image" => "images/hotel_group_logos/holiday_inn_express.png"
				])
			],
			[
				"large_icon" => false,
				"link_url" => "http://www.ihg.com/staybridge/hotels/us/en/",
				"link_image" => ImagesHelper::createImageObjectFromArray([
					"image" => "images/hotel_group_logos/staybridge.png"
				])
			],
			[
				"large_icon" => false,
				"link_url" => "http://www.ihg.com/hotelindigo/hotels/us/en/",
				"link_image" => ImagesHelper::createImageObjectFromArray([
					"image" => "images/hotel_group_logos/indigo.png"
				])
			],
			[
				"large_icon" => false,
				"link_url" => "http://www.ihg.com/evenhotels/hotels/us/en/",
				"link_image" => ImagesHelper::createImageObjectFromArray([
					"image" => "images/hotel_group_logos/even_hotel.png"
				])
			],
			[
				"large_icon" => false,
				"link_url" => "http://www.ihg.com/crowneplaza/hotels/us/en/",
				"link_image" => ImagesHelper::createImageObjectFromArray([
					"image" => "images/hotel_group_logos/crowne_plaza.png"
				])
			],
			[
				"large_icon" => false,
				"link_url" => "http://www.ihg.com/holidayinnclubvacations/hotels/us/en/",
				"link_image" => ImagesHelper::createImageObjectFromArray([
					"image" => "images/hotel_group_logos/holiday_inn_club_vacation.png"
				])
			],
			[
				"large_icon" => false,
				"link_url" => "http://www.ihg.com/holidayinnresorts/hotels/us/en/",
				"link_image" => ImagesHelper::createImageObjectFromArray([
					"image" => "images/hotel_group_logos/holiday_inn_resort.png"
				])
			],
			[
				"large_icon" => false,
				"link_url" => "http://www.ihg.com/candlewood/hotels/us/en/",
				"link_image" => ImagesHelper::createImageObjectFromArray([
					"image" => "images/hotel_group_logos/candlewood.png"
				])
			],
			[
				"large_icon" => true,
				"link_url" => "http://www.ihgrewardsclub.com/",
				"link_image" => ImagesHelper::createImageObjectFromArray([
					"image" => "images/hotel_group_logos/ihg_rewards_club.png"
				])
			]
		];
	}
}