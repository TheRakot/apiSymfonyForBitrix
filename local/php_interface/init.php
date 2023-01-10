<?

use DI\Di;

function pre($value, $force = false, $dump = false)
{
	global $USER;
	
	if ($USER->GetLogin() === 'admin' || $_REQUEST['pre'] == 1 || $force) {
		echo '<pre>';
		($dump ? var_dump($value) : print_r($value));
		echo '</pre>';
		print_r(PHP_EOL);
	}
}

#подключение библиотек установленных через composer
require_once($_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php');

Di::load(__DIR__ . '/services.yml');