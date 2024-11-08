<?php


/**
 * Function to create custom url
 * uses site_url() function
 *
 * @param string $url any slug
 *
 * @return string site_url
 * 
 */
if (!function_exists('url')) {

	function url($url = '')
	{
		return site_url($url);
	}
}

/**
 * Function to get url of assets folder
 *
 * @param string $url any slug 
 *
 * @return string url
 * 
 */
if (!function_exists('assets_url')) {

	function assets_url($url = '')
	{
		return base_url('assets/' . $url);
	}
}

/**
 * Function to get url of assets folder
 *
 * @param string $url any slug 
 *
 * @return string url
 * 
 */
if (!function_exists('admin_assets_url')) {

	function admin_assets($url = '')
	{
		return assets_url('admin/' . $url);
	}
}


/**
 * Function to get url of upload folder
 *
 * @param string $url any slug 
 *
 * @return string url
 * 
 */
if (!function_exists('urlUpload')) {

	function urlUpload($url = '', $time = false)
	{
		return base_url('uploads/' . $url) . ($time ? '?' . time() : '');
	}
}




if (!function_exists('calculate_percentage')) {
	/**
	 * Calcula el porcentaje de un precio.
	 *
	 * @param float $precio Total del precio
	 * @param float $porcentaje Porcentaje que se desea calcular
	 * @return float Resultado del porcentaje aplicado al precio
	 */
	function calculate_percentage(float $precio, float $porcentaje): float
	{
		$resultado = ($precio * $porcentaje) / 100;
		return round($resultado, 2); // Usamos round para asegurar que el resultado tenga solo dos decimales
	}
}

/**
 * Function to check if the user is loggedIn
 *
 * @return boolean
 * 
 */
if (!function_exists('is_logged')) {

	function is_logged()
	{
		$login_token_match = false;

		$isLogged = !empty(session()->get('login')) &&  !empty(session()->get('logged')) ? (object) session()->get('logged') : false;
		$_token = $isLogged && !empty(session()->get('login_token')) ? session()->get('login_token') : false;

		if (!$isLogged) {
			$isLogged = get_cookie('login') && !empty(get_cookie('logged')) ? json_decode(get_cookie('logged')) : false;
			$_token = $isLogged && !empty(get_cookie('login_token')) ? get_cookie('login_token') : false;
		}

		// var_dump($isLogged->id); die;

		if ($isLogged) {
			$userModel = model('App\Models\UserModel');
			$user = $userModel->getById($userModel->escape((int) $isLogged->id));
			// verify login_token
			$login_token_match = (sha1($user->id . $user->password . $isLogged->time) == $_token);
		}

		return $isLogged && $login_token_match;
	}
}



if (!function_exists('calcular_edad')) {
	/**
	 * Calcula la edad de una persona a partir de su fecha de nacimiento.
	 *
	 * @param string $fecha_nacimiento Fecha de nacimiento en formato 'YYYY-MM-DD'.
	 * @return int Edad en años.
	 */
	function calcular_edad($fecha_nacimiento)
	{
		$cumpleanos = new DateTime($fecha_nacimiento);
		$hoy = new DateTime();
		$annos = $hoy->diff($cumpleanos);
		return $annos->y;
	}
}



if (!function_exists('replaceCommaWithDot')) {
	/**
	 * Replace commas with dots in a given number string
	 *
	 * @param string $number The number string to format
	 * @return string The formatted number string
	 */
	function replaceCommaWithDot(string $number): string
	{
		// Reemplazar comas por puntos
		return str_replace(',', '.', $number);
	}
}




if (!function_exists('before')) {
	function before($character, $string)
	{
		$position = strpos($string, $character);

		// Verificar si el carácter se encuentra en la cadena
		if ($position !== false) {
			return substr($string, 0, $position);
		}

		// Si el carácter no se encuentra, devolver la cadena completa o un valor vacío
		return $string;
	}
}


/**
 * Calcula los días restantes entre la fecha actual y una fecha final dada.
 *
 * @param string $fecha_final Fecha final en formato 'Y-m-d'.
 * @return int Número de días restantes. Retorna un número negativo si la fecha final ya ha pasado.
 */


if (!function_exists('remaining_days')) {

	function remaining_days(string $final_date): int
	{
		try {
			$current_date = new DateTime();
			$final_date = new DateTime($final_date);

			$interval = $current_date->diff($final_date);
			return (int) $interval->format('%r%a'); // %r para el signo positivo/negativo, %a para los días totales
		} catch (Exception $e) {
			// Manejo de errores: puedes registrar el error o manejarlo de otra manera
			log_message('error', 'Error al calcular los días restantes: ' . $e->getMessage());
			return 0; // O lanzar una excepción, o lo que sea apropiado para tu caso
		}
	}
}




if (!function_exists('getMonthName')) {
	function getMonthName($fecha)
	{
		$meses = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
		$mes = (int)substr($fecha, 5, 2); // Convertir a entero para evitar problemas con ceros a la izquierda

		if ($mes >= 1 && $mes <= 12) {
			return $meses[$mes - 1];
		} else {
			return "Solo existen 12 meses. Hay un error en el formato de tu fecha: " . $fecha;
		}
	}
}




if (!function_exists('timestampToDate')) {

	function timestampToDate($timestamp)
	{
		$date = new DateTime("@$timestamp");
		$date->setTimezone(new DateTimeZone('UTC'));
		// Devolver la fecha en formato RFC 2822
		return $date->format('r');
	}
}


/**
 * Function that returns the data of loggedIn user
 *
 * @param string $key Any key/Column name that exists in users table
 *
 * @return boolean
 * 
 */


if (!function_exists('logged')) {
	function logged($key = false)
	{
		$ionAuth = new \IonAuth\Libraries\IonAuth();

		if ($ionAuth->loggedIn()) {
			$user = $ionAuth->user()->row();

			// Check if the key is provided and exists in the user object
			if ($key && isset($user->{$key})) {
				return $user->{$key};
			}

			// If no key is provided or the key doesn't exist, return null
			return null;
		}

		return null;
	}
}




/**
 * Function to check and get 'post' request
 *
 * @param string $key - key to check in 'post' request
 *
 * @return string 
 * 
 */
if (!function_exists('post')) {

	function post($key)
	{
		return service('request')->getPost($key);
	}
}

/**
 * Function to check and get 'get' request
 *
 * @param string $key - key to check in 'get' request
 *
 * @return string value - uses codeigniter Input library 
 * 
 */
if (!function_exists('get')) {

	function get($key)
	{
		return service('request')->getGetPost($key);
	}
}


/**
 * Function for user profile url
 *
 * @param string $id - user id of the user
 *
 * @return string profile url
 * 
 */
if (!function_exists('userProfile')) {

	function userProfile($id)
	{
		$url = urlUpload('users/' . $id . '.png?' . time());

		if ($id != 'default')
			$url = urlUpload('users/' . $id . '.' . model('App\Models\UserModel')->getRowById($id, 'img_type') . '?' . time());

		return $url;
	}
}
/**
 * Function to dump the passed data
 * Die & Dumps the whole data passed
 *
 * uses - var_dump & die together
 *
 * @param all $key - All Accepted - string,int,boolean,etc
 *
 * @return boolean
 * 
 */
if (!function_exists('dd')) {

	function dd($key)
	{
		die(var_dump($key));
		return true;
	}
}


/**
 * Finds and return the ipaddres of client user
 *
 * @param array $ipaddress IpAddress
 * 
 */
if (!function_exists('ip_address')) {

	function ip_address()
	{
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if (isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if (isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if (isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
}

/**
 * Provides the shortcodes which are available in any email template
 *
 * @return array $data Array of shortcodes
 * 
 */
if (!function_exists('getEmailShortCodes')) {

	function getEmailShortCodes()
	{

		$data = [
			'site_url' => site_url(),
			'company_name' => setting('company_name'),
		];

		return $data;
	}
}

/**
 * Redirects with error if user doesnt have the permission to passed key/module
 *
 * @param string $code Code permissions
 * 
 * @return boolean true/false
 * 
 */
if (!function_exists('ifPermissions')) {

	function ifPermissions($code = '')
	{


		// header('Location : '.url('errors/permission_denied')); die;

		// die(var_dump( hasPermissions($code) ));

		if (is_logged() && hasPermissions($code)) {
			return true;
		}


		header('Location : ' . url('errors/permission_denied'));
		die;

		return false;
	}
}


/**
 * Check and return boolean if user have the permission to passed key or not
 *
 * @param string $code Code permissions
 * 
 * @return boolean true/false
 * 
 */
if (!function_exists('hasPermissions')) {

	function hasPermissions($code = '')
	{
		return !empty(model('App\Models\RolePermissionModel')->getByWhere(['role' => logged('role'), 'permission' => $code]));
	}
}


function updateViewData($data)
{
	$view = \Config\Services::renderer();
	$view->setData($data);
}

function setPageData($data)
{
	updateViewData(['_page' => (object) $data]);
}

function setDefaultViewData()
{
	setPageData([
		'title' => '',
		'menu' => '',
		'submenu' => '',
	]);
}



/**
 * Die/Stops the request if its not a 'post' requetst type
 *
 * @return boolean
 * 
 */
if (!function_exists('postAllowed')) {

	function postAllowed()
	{

		if (service('request')->getMethod(true) != 'POST')
			die('Invalid Request');

		return true;
	}
}


/**
 * Hides Some Characters in Email. Basically Used in Forget Password System
 *
 * @param string $email Email 
 * 
 * @return string
 * 
 */
if (!function_exists('obfuscate_email')) {

	function obfuscate_email($email)
	{

		// die(var_dump($email));

		$em   = explode("@", $email);
		$name = implode('@', array_slice($em, 0, count($em) - 1));
		$len  = floor(strlen($name) / 2);

		return substr($name, 0, $len) . str_repeat('*', $len) . "@" . end($em);
	}
}


/**
 * return language code
 *
 * @return string
 * 
 */
if (!function_exists('getUserlang')) {

	function getUserlang()
	{

		return !empty(get_cookie('current_lang', true)) ? get_cookie('current_lang', true) : setting('default_lang');
	}
}



/**
 * Currency formating
 *
 * @param int/float/string $amount
 *
 * @return string $amount formated amount with currency symbol
 * 
 */
if (!function_exists('currency')) {

	function currency($amount)
	{
		return '$ ' . $amount;
	}
}




/**
 * Generates teh html for breadcrumb - Supports AdminLte
 *
 * @param array $args Array of values
 * 
 */
/* if (!function_exists('breadcrumb')) {

	function breadcrumb($args = '')
	{
		$html = '<ol class="breadcrumb">';
		$i = 0;
		foreach ($args as $key => $value) {
			if(count($args) < $i)
				$html .= '<li><a href="'.url($key).'">'.$value.'</a></li>';
			else
				$html .= '<li class="active">'.$value.'</li>';
			$i++;
		}
		    
		    
		$html .= '</ol>';
		echo $html;
	}


} */
/**
 * Finds and return the ipaddres of client user
 *
 * @param array $ipaddress IpAddress
 * 
 */
if (!function_exists('ip_address')) {

	function ip_address()
	{
		$ipaddress = '';
		if (isset($_SERVER['HTTP_CLIENT_IP']))
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		else if (isset($_SERVER['HTTP_X_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		else if (isset($_SERVER['HTTP_FORWARDED']))
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		else if (isset($_SERVER['REMOTE_ADDR']))
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		else
			$ipaddress = 'UNKNOWN';
		return $ipaddress;
	}
}



/**
 * return language code
 *
 * @return string
 * 
 */

function supported_languages()
{

	$supported_languages = json_decode('{
		"en":{
			"name":"English",
			"nativeName":"english"
		},
		"es":{
			"name":"Spanish",
			"nativeName":"español"
		},
		"hi":{
			"name":"Hindi",
			"nativeName":"हिन्दी"
		}
	}');


	$all_languages = json_decode('{
		"ab":{
			"name":"Abkhaz",
			"nativeName":"аҧсуа"
		},
		"aa":{
			"name":"Afar",
			"nativeName":"Afaraf"
		},
		"af":{
			"name":"Afrikaans",
			"nativeName":"Afrikaans"
		},
		"ak":{
			"name":"Akan",
			"nativeName":"Akan"
		},
		"sq":{
			"name":"Albanian",
			"nativeName":"Shqip"
		},
		"am":{
			"name":"Amharic",
			"nativeName":"አማርኛ"
		},
		"ar":{
			"name":"Arabic",
			"nativeName":"العربية"
		},
		"an":{
			"name":"Aragonese",
			"nativeName":"Aragonés"
		},
		"hy":{
			"name":"Armenian",
			"nativeName":"Հայերեն"
		},
		"as":{
			"name":"Assamese",
			"nativeName":"অসমীয়া"
		},
		"av":{
			"name":"Avaric",
			"nativeName":"авар мацӀ, магӀарул мацӀ"
		},
		"ae":{
			"name":"Avestan",
			"nativeName":"avesta"
		},
		"ay":{
			"name":"Aymara",
			"nativeName":"aymar aru"
		},
		"az":{
			"name":"Azerbaijani",
			"nativeName":"azərbaycan dili"
		},
		"bm":{
			"name":"Bambara",
			"nativeName":"bamanankan"
		},
		"ba":{
			"name":"Bashkir",
			"nativeName":"башҡорт теле"
		},
		"eu":{
			"name":"Basque",
			"nativeName":"euskara, euskera"
		},
		"be":{
			"name":"Belarusian",
			"nativeName":"Беларуская"
		},
		"bn":{
			"name":"Bengali",
			"nativeName":"বাংলা"
		},
		"bh":{
			"name":"Bihari",
			"nativeName":"भोजपुरी"
		},
		"bi":{
			"name":"Bislama",
			"nativeName":"Bislama"
		},
		"bs":{
			"name":"Bosnian",
			"nativeName":"bosanski jezik"
		},
		"br":{
			"name":"Breton",
			"nativeName":"brezhoneg"
		},
		"bg":{
			"name":"Bulgarian",
			"nativeName":"български език"
		},
		"my":{
			"name":"Burmese",
			"nativeName":"ဗမာစာ"
		},
		"ca":{
			"name":"Catalan; Valencian",
			"nativeName":"Català"
		},
		"ch":{
			"name":"Chamorro",
			"nativeName":"Chamoru"
		},
		"ce":{
			"name":"Chechen",
			"nativeName":"нохчийн мотт"
		},
		"ny":{
			"name":"Chichewa; Chewa; Nyanja",
			"nativeName":"chiCheŵa, chinyanja"
		},
		"zh":{
			"name":"Chinese",
			"nativeName":"中文 (Zhōngwén), 汉语, 漢語"
		},
		"cv":{
			"name":"Chuvash",
			"nativeName":"чӑваш чӗлхи"
		},
		"kw":{
			"name":"Cornish",
			"nativeName":"Kernewek"
		},
		"co":{
			"name":"Corsican",
			"nativeName":"corsu, lingua corsa"
		},
		"cr":{
			"name":"Cree",
			"nativeName":"ᓀᐦᐃᔭᐍᐏᐣ"
		},
		"hr":{
			"name":"Croatian",
			"nativeName":"hrvatski"
		},
		"cs":{
			"name":"Czech",
			"nativeName":"česky, čeština"
		},
		"da":{
			"name":"Danish",
			"nativeName":"dansk"
		},
		"dv":{
			"name":"Divehi; Dhivehi; Maldivian;",
			"nativeName":"ދިވެހި"
		},
		"nl":{
			"name":"Dutch",
			"nativeName":"Nederlands, Vlaams"
		},
		"en":{
			"name":"English",
			"nativeName":"English"
		},
		"eo":{
			"name":"Esperanto",
			"nativeName":"Esperanto"
		},
		"et":{
			"name":"Estonian",
			"nativeName":"eesti, eesti keel"
		},
		"ee":{
			"name":"Ewe",
			"nativeName":"Eʋegbe"
		},
		"fo":{
			"name":"Faroese",
			"nativeName":"føroyskt"
		},
		"fj":{
			"name":"Fijian",
			"nativeName":"vosa Vakaviti"
		},
		"fi":{
			"name":"Finnish",
			"nativeName":"suomi, suomen kieli"
		},
		"fr":{
			"name":"French",
			"nativeName":"français, langue française"
		},
		"ff":{
			"name":"Fula; Fulah; Pulaar; Pular",
			"nativeName":"Fulfulde, Pulaar, Pular"
		},
		"gl":{
			"name":"Galician",
			"nativeName":"Galego"
		},
		"ka":{
			"name":"Georgian",
			"nativeName":"ქართული"
		},
		"de":{
			"name":"German",
			"nativeName":"Deutsch"
		},
		"el":{
			"name":"Greek, Modern",
			"nativeName":"Ελληνικά"
		},
		"gn":{
			"name":"Guaraní",
			"nativeName":"Avañeẽ"
		},
		"gu":{
			"name":"Gujarati",
			"nativeName":"ગુજરાતી"
		},
		"ht":{
			"name":"Haitian; Haitian Creole",
			"nativeName":"Kreyòl ayisyen"
		},
		"ha":{
			"name":"Hausa",
			"nativeName":"Hausa, هَوُسَ"
		},
		"he":{
			"name":"Hebrew (modern)",
			"nativeName":"עברית"
		},
		"hz":{
			"name":"Herero",
			"nativeName":"Otjiherero"
		},
		"hi":{
			"name":"Hindi",
			"nativeName":"हिन्दी, हिंदी"
		},
		"ho":{
			"name":"Hiri Motu",
			"nativeName":"Hiri Motu"
		},
		"hu":{
			"name":"Hungarian",
			"nativeName":"Magyar"
		},
		"ia":{
			"name":"Interlingua",
			"nativeName":"Interlingua"
		},
		"id":{
			"name":"Indonesian",
			"nativeName":"Bahasa Indonesia"
		},
		"ie":{
			"name":"Interlingue",
			"nativeName":"Originally called Occidental; then Interlingue after WWII"
		},
		"ga":{
			"name":"Irish",
			"nativeName":"Gaeilge"
		},
		"ig":{
			"name":"Igbo",
			"nativeName":"Asụsụ Igbo"
		},
		"ik":{
			"name":"Inupiaq",
			"nativeName":"Iñupiaq, Iñupiatun"
		},
		"io":{
			"name":"Ido",
			"nativeName":"Ido"
		},
		"is":{
			"name":"Icelandic",
			"nativeName":"Íslenska"
		},
		"it":{
			"name":"Italian",
			"nativeName":"Italiano"
		},
		"iu":{
			"name":"Inuktitut",
			"nativeName":"ᐃᓄᒃᑎᑐᑦ"
		},
		"ja":{
			"name":"Japanese",
			"nativeName":"日本語 (にほんご／にっぽんご)"
		},
		"jv":{
			"name":"Javanese",
			"nativeName":"basa Jawa"
		},
		"kl":{
			"name":"Kalaallisut, Greenlandic",
			"nativeName":"kalaallisut, kalaallit oqaasii"
		},
		"kn":{
			"name":"Kannada",
			"nativeName":"ಕನ್ನಡ"
		},
		"kr":{
			"name":"Kanuri",
			"nativeName":"Kanuri"
		},
		"ks":{
			"name":"Kashmiri",
			"nativeName":"कश्मीरी, كشميري‎"
		},
		"kk":{
			"name":"Kazakh",
			"nativeName":"Қазақ тілі"
		},
		"km":{
			"name":"Khmer",
			"nativeName":"ភាសាខ្មែរ"
		},
		"ki":{
			"name":"Kikuyu, Gikuyu",
			"nativeName":"Gĩkũyũ"
		},
		"rw":{
			"name":"Kinyarwanda",
			"nativeName":"Ikinyarwanda"
		},
		"ky":{
			"name":"Kirghiz, Kyrgyz",
			"nativeName":"кыргыз тили"
		},
		"kv":{
			"name":"Komi",
			"nativeName":"коми кыв"
		},
		"kg":{
			"name":"Kongo",
			"nativeName":"KiKongo"
		},
		"ko":{
			"name":"Korean",
			"nativeName":"한국어 (韓國語), 조선말 (朝鮮語)"
		},
		"ku":{
			"name":"Kurdish",
			"nativeName":"Kurdî, كوردی‎"
		},
		"kj":{
			"name":"Kwanyama, Kuanyama",
			"nativeName":"Kuanyama"
		},
		"la":{
			"name":"Latin",
			"nativeName":"latine, lingua latina"
		},
		"lb":{
			"name":"Luxembourgish, Letzeburgesch",
			"nativeName":"Lëtzebuergesch"
		},
		"lg":{
			"name":"Luganda",
			"nativeName":"Luganda"
		},
		"li":{
			"name":"Limburgish, Limburgan, Limburger",
			"nativeName":"Limburgs"
		},
		"ln":{
			"name":"Lingala",
			"nativeName":"Lingála"
		},
		"lo":{
			"name":"Lao",
			"nativeName":"ພາສາລາວ"
		},
		"lt":{
			"name":"Lithuanian",
			"nativeName":"lietuvių kalba"
		},
		"lu":{
			"name":"Luba-Katanga",
			"nativeName":""
		},
		"lv":{
			"name":"Latvian",
			"nativeName":"latviešu valoda"
		},
		"gv":{
			"name":"Manx",
			"nativeName":"Gaelg, Gailck"
		},
		"mk":{
			"name":"Macedonian",
			"nativeName":"македонски јазик"
		},
		"mg":{
			"name":"Malagasy",
			"nativeName":"Malagasy fiteny"
		},
		"ms":{
			"name":"Malay",
			"nativeName":"bahasa Melayu, بهاس ملايو‎"
		},
		"ml":{
			"name":"Malayalam",
			"nativeName":"മലയാളം"
		},
		"mt":{
			"name":"Maltese",
			"nativeName":"Malti"
		},
		"mi":{
			"name":"Māori",
			"nativeName":"te reo Māori"
		},
		"mr":{
			"name":"Marathi (Marāṭhī)",
			"nativeName":"मराठी"
		},
		"mh":{
			"name":"Marshallese",
			"nativeName":"Kajin M̧ajeļ"
		},
		"mn":{
			"name":"Mongolian",
			"nativeName":"монгол"
		},
		"na":{
			"name":"Nauru",
			"nativeName":"Ekakairũ Naoero"
		},
		"nv":{
			"name":"Navajo, Navaho",
			"nativeName":"Diné bizaad, Dinékʼehǰí"
		},
		"nb":{
			"name":"Norwegian Bokmål",
			"nativeName":"Norsk bokmål"
		},
		"nd":{
			"name":"North Ndebele",
			"nativeName":"isiNdebele"
		},
		"ne":{
			"name":"Nepali",
			"nativeName":"नेपाली"
		},
		"ng":{
			"name":"Ndonga",
			"nativeName":"Owambo"
		},
		"nn":{
			"name":"Norwegian Nynorsk",
			"nativeName":"Norsk nynorsk"
		},
		"no":{
			"name":"Norwegian",
			"nativeName":"Norsk"
		},
		"ii":{
			"name":"Nuosu",
			"nativeName":"ꆈꌠ꒿ Nuosuhxop"
		},
		"nr":{
			"name":"South Ndebele",
			"nativeName":"isiNdebele"
		},
		"oc":{
			"name":"Occitan",
			"nativeName":"Occitan"
		},
		"oj":{
			"name":"Ojibwe, Ojibwa",
			"nativeName":"ᐊᓂᔑᓈᐯᒧᐎᓐ"
		},
		"cu":{
			"name":"Old Church Slavonic, Church Slavic, Church Slavonic, Old Bulgarian, Old Slavonic",
			"nativeName":"ѩзыкъ словѣньскъ"
		},
		"om":{
			"name":"Oromo",
			"nativeName":"Afaan Oromoo"
		},
		"or":{
			"name":"Oriya",
			"nativeName":"ଓଡ଼ିଆ"
		},
		"os":{
			"name":"Ossetian, Ossetic",
			"nativeName":"ирон æвзаг"
		},
		"pa":{
			"name":"Panjabi, Punjabi",
			"nativeName":"ਪੰਜਾਬੀ, پنجابی‎"
		},
		"pi":{
			"name":"Pāli",
			"nativeName":"पाऴि"
		},
		"fa":{
			"name":"Persian",
			"nativeName":"فارسی"
		},
		"pl":{
			"name":"Polish",
			"nativeName":"polski"
		},
		"ps":{
			"name":"Pashto, Pushto",
			"nativeName":"پښتو"
		},
		"pt":{
			"name":"Portuguese",
			"nativeName":"Português"
		},
		"qu":{
			"name":"Quechua",
			"nativeName":"Runa Simi, Kichwa"
		},
		"rm":{
			"name":"Romansh",
			"nativeName":"rumantsch grischun"
		},
		"rn":{
			"name":"Kirundi",
			"nativeName":"kiRundi"
		},
		"ro":{
			"name":"Romanian, Moldavian, Moldovan",
			"nativeName":"română"
		},
		"ru":{
			"name":"Russian",
			"nativeName":"русский язык"
		},
		"sa":{
			"name":"Sanskrit (Saṁskṛta)",
			"nativeName":"संस्कृतम्"
		},
		"sc":{
			"name":"Sardinian",
			"nativeName":"sardu"
		},
		"sd":{
			"name":"Sindhi",
			"nativeName":"सिन्धी, سنڌي، سندھی‎"
		},
		"se":{
			"name":"Northern Sami",
			"nativeName":"Davvisámegiella"
		},
		"sm":{
			"name":"Samoan",
			"nativeName":"gagana faa Samoa"
		},
		"sg":{
			"name":"Sango",
			"nativeName":"yângâ tî sängö"
		},
		"sr":{
			"name":"Serbian",
			"nativeName":"српски језик"
		},
		"gd":{
			"name":"Scottish Gaelic; Gaelic",
			"nativeName":"Gàidhlig"
		},
		"sn":{
			"name":"Shona",
			"nativeName":"chiShona"
		},
		"si":{
			"name":"Sinhala, Sinhalese",
			"nativeName":"සිංහල"
		},
		"sk":{
			"name":"Slovak",
			"nativeName":"slovenčina"
		},
		"sl":{
			"name":"Slovene",
			"nativeName":"slovenščina"
		},
		"so":{
			"name":"Somali",
			"nativeName":"Soomaaliga, af Soomaali"
		},
		"st":{
			"name":"Southern Sotho",
			"nativeName":"Sesotho"
		},
		"es":{
			"name":"Spanish; Castilian",
			"nativeName":"español, castellano"
		},
		"su":{
			"name":"Sundanese",
			"nativeName":"Basa Sunda"
		},
		"sw":{
			"name":"Swahili",
			"nativeName":"Kiswahili"
		},
		"ss":{
			"name":"Swati",
			"nativeName":"SiSwati"
		},
		"sv":{
			"name":"Swedish",
			"nativeName":"svenska"
		},
		"ta":{
			"name":"Tamil",
			"nativeName":"தமிழ்"
		},
		"te":{
			"name":"Telugu",
			"nativeName":"తెలుగు"
		},
		"tg":{
			"name":"Tajik",
			"nativeName":"тоҷикӣ, toğikī, تاجیکی‎"
		},
		"th":{
			"name":"Thai",
			"nativeName":"ไทย"
		},
		"ti":{
			"name":"Tigrinya",
			"nativeName":"ትግርኛ"
		},
		"bo":{
			"name":"Tibetan Standard, Tibetan, Central",
			"nativeName":"བོད་ཡིག"
		},
		"tk":{
			"name":"Turkmen",
			"nativeName":"Türkmen, Түркмен"
		},
		"tl":{
			"name":"Tagalog",
			"nativeName":"Wikang Tagalog, ᜏᜒᜃᜅ᜔ ᜆᜄᜎᜓᜄ᜔"
		},
		"tn":{
			"name":"Tswana",
			"nativeName":"Setswana"
		},
		"to":{
			"name":"Tonga (Tonga Islands)",
			"nativeName":"faka Tonga"
		},
		"tr":{
			"name":"Turkish",
			"nativeName":"Türkçe"
		},
		"ts":{
			"name":"Tsonga",
			"nativeName":"Xitsonga"
		},
		"tt":{
			"name":"Tatar",
			"nativeName":"татарча, tatarça, تاتارچا‎"
		},
		"tw":{
			"name":"Twi",
			"nativeName":"Twi"
		},
		"ty":{
			"name":"Tahitian",
			"nativeName":"Reo Tahiti"
		},
		"ug":{
			"name":"Uighur, Uyghur",
			"nativeName":"Uyƣurqə, ئۇيغۇرچە‎"
		},
		"uk":{
			"name":"Ukrainian",
			"nativeName":"українська"
		},
		"ur":{
			"name":"Urdu",
			"nativeName":"اردو"
		},
		"uz":{
			"name":"Uzbek",
			"nativeName":"zbek, Ўзбек, أۇزبېك‎"
		},
		"ve":{
			"name":"Venda",
			"nativeName":"Tshivenḓa"
		},
		"vi":{
			"name":"Vietnamese",
			"nativeName":"Tiếng Việt"
		},
		"vo":{
			"name":"Volapük",
			"nativeName":"Volapük"
		},
		"wa":{
			"name":"Walloon",
			"nativeName":"Walon"
		},
		"cy":{
			"name":"Welsh",
			"nativeName":"Cymraeg"
		},
		"wo":{
			"name":"Wolof",
			"nativeName":"Wollof"
		},
		"fy":{
			"name":"Western Frisian",
			"nativeName":"Frysk"
		},
		"xh":{
			"name":"Xhosa",
			"nativeName":"isiXhosa"
		},
		"yi":{
			"name":"Yiddish",
			"nativeName":"ייִדיש"
		},
		"yo":{
			"name":"Yoruba",
			"nativeName":"Yorùbá"
		},
		"za":{
			"name":"Zhuang, Chuang",
			"nativeName":"Saɯ cueŋƅ, Saw cuengh"
		}
	}');

	return $supported_languages;

	//    die(var_dump($list));
}