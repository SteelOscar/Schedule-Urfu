<!DOCTYPE html>
<html>
 <head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <title>Обработчик telegram bot</title>
 </head>
</html>
<?php
function get_schedule($group_id,$date_today) {
	$url_urfu = 'https://urfu.ru/api/schedule/groups/lessons/'.$group_id.'/'.$date_today.'/';
	$file = curl_init($url_urfu);

	// Настройка cURL до выполнении операции считывания
	curl_setopt($file, CURLOPT_RETURNTRANSFER, true); // устанавливаем true, для получения содержимого в переменную, вместо вывода в браузер
	curl_setopt($file, CURLOPT_HEADER, false); // отключить вывод заголовка в содержимом
	curl_setopt($file, CURLOPT_FOLLOWLOCATION, true); // следовать редиректу, если сервер пытается перенаправить посетителя
	curl_setopt($file, CURLOPT_MAXREDIRS, 5); // максимальное кол-во редиректов
	curl_setopt($file, CURLOPT_USERAGENT,
	    'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.130 Safari/537.36'); // информация о браузере
	curl_setopt($curl, CURLOPT_POST, true); // включаем POST передачу данных
	curl_setopt($curl, CURLOPT_POSTFIELDS, "a=4&b=7"); // указываем POST данные

	// Выполнение операции считывания и получение результата
	$data = curl_exec($file); // получаем содержимое (если страница - html-код, если картинка - код картинки и т.п.)
	curl_close($file);

	preg_match_all('#<tr class="divide">(.+?)<tr class="divide">#is', $data, $arr_day);
	$data_not_json = json_decode($data);
	$result = $arr_day[0][0];
	$result = preg_replace("!<(.*?)>!si", "", $result);
	$result = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", " ", $result);
	//$result = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $result);
	$result = str_replace("                                 "," ",$result);
	$result = str_replace("                     ","\n",$result);
	$result = str_replace("     ","\n",$result);
	$result = str_replace("    ","",$result);
	$result = str_replace("

   "," ",$result);
	$result = str_replace(".\n",". ",$result);
	$result = preg_replace("/\. (\d)/", ".\n\\1", $result);

	return $result;	
}
	echo get_schedule($_GET['group_id'],20190408);

?>