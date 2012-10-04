<?php

namespace SlidesLive\SlidesLiveBundle\DependencyInjection;

/*
	Seznam pouzitych jazyku tridenych podle jejich kodu.
	Slouzi jako vychozi mnozina jazyku pro nastaveni jazyka prezentaci
	a jako prevodnik.get_key
*/
class LanguageList {

	protected static $languages = array(
		'ar' => 'العربية',
		'bg' => 'Български',
		'ca' => 'Català',
		'cs' => 'Česky',
		'da' => 'Dansk',
		'de' => 'Deutsch',
		'en' => ' English',
		'es' => 'Español',
		'eo' => 'Esperanto',
		'eu' => 'Euskara',
		'fa' => 'فارسی',
		'fr' => 'Français',
		'ko' => '한국어',
		'hi' => 'हिन्दी',
		'hr' => 'Hrvatski',
		'id' => 'Bahasa Indonesia',
		'it' => 'Italiano',
		'he' => 'עברית',
		'lt' => 'Lietuvių',
		'hu' => 'Magyar',
		'ms' => 'Bahasa Melayu',
		'nl' => 'Nederlands',
		'ja' => '日本語',
		'no' => 'Norsk (bokmål)',
		'pl' => 'Polski',
		'pt' => 'Português',
		'kk' => 'Қазақша / Qazaqşa / قازاقشا',
		'ro' => 'Română',
		'ru' => 'Русский',
		'sk' => 'Slovenčina',
		'sl' => 'Slovenščina',
		'sr' => 'Српски / Srpski',
		'fi' => 'Suomi',
		'sv' => 'Svenska',
		'tr' => 'Türkçe',
		'uk' => 'Українська',
		'vi' => 'Tiếng Việt',
		'vo' => 'Volapük',
		'zh' => '中文',
	);

	/* Ziskani celeho seznamu jazyku. */ 
	public static function getLanguages() {
		return self::$languages;
	}

	/* Preklad kod jazyka => nazev jazyka. */
	public static function getLanguage($code) {
		if (isset(self::$languages[$code])) {
			return self::$languages[$code];
		}
		else {
			return '?';
		}
	}

	/* Preklad nazev jazyka => kod jazyka. */
	public function getLanguageCode($lang) {
		$code = in_array($lang, self::$languages);
		if ($code) {
			return $code;
		}
		else {
			return '?';
		}
	}
}