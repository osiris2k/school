<?php namespace App\Helpers;

use App\SiteLanguages;

class LangHelper {
	private static $languages;
	private static $hreflangHtml = '';

	public static function generateHreflang( $siteId, $currentPage ) {
		self::getAllSiteLang( $siteId );
		self::generateHreflangHtml( $currentPage );

		return self::$hreflangHtml;
	}

	private static function getAllSiteLang( $siteId ) {
		self::$languages = SiteLanguages::whereSiteId( $siteId )->whereHas( 'language', function ( $q ) {
			$q->where( 'status', 1 );
		} )
		                                ->get();
	}

	private static function generateHreflangHtml( $currentPage ) {
		if ( count( self::$languages ) == 0 ) {

			return false;
		}

		foreach ( self::$languages as $lang ) {
			self::$hreflangHtml .= '<link rel="alternate" href="' . url( ViewHelper::getContentURI( $currentPage['id'], $lang->language_id ) ) . '" hreflang="' . $lang->language->local . '" />';
		}
	}
}