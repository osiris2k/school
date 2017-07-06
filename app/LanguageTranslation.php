<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LanguageTranslation extends Model{
	
	use SoftDeletes;
	protected $table = 'language_translation';

	public function	Translation()
	{
		return $this->belongsTo('Translation');
	}

	public function scopeFilterByTranslationId($query, $translation_id)
	{
		return $query->where('language_translation.translation_id', $translation_id);
	}
    public function scopeFilterByLanguageId($query, $language_id)
    {
        return $query->where('language_translation.language_id', $language_id);
    }


    public function scopeFilterByKey($query, $key)
    {
        return $query->where('key', $key);
    }

    public static function getTranslationsValuesForExportFile(){
        return self::select('translation_id', 'key', 'language_id', 'values')->leftJoin('translations', 'translations.id', '=', 'language_translation.translation_id');

    }
}
?>