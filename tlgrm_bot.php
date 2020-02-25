<?php
include 'parse_schedule.php';
include 'sql.php';
include 'email.php';

function sendMessage($chat_id, $message) {
	file_get_contents($GLOBALS['api'] . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message));
}

function sendMessageMarkup($chat_id, $message, $replyMarkup) {
  file_get_contents($GLOBALS['api'] . '/sendMessage?chat_id=' . $chat_id . '&text=' . urlencode($message) . '&reply_markup=' . $replyMarkup);
}

$access_token = '803139219:AAFjfgzmifc2QOrck_WwaTIcAZjvV3jvz9Y';
$api = 'https://api.telegram.org/bot' . $access_token;

#Получаем данные от бота в json и декодируем
$output = json_decode(file_get_contents('php://input'), TRUE);
#Данные о пользователе
$chat_id = $output['message']['chat']['id'];
$user_id = $output['message']['user']['id'];
$first_name = $output['message']['chat']['first_name'];
$last_name = $output['message']['chat']['last_name'];
$message = $output['message']['text'];
$username = $output['message']['chat']['username'];

#Смотрим какие данные пришли (от пользователя или от бота)
if (array_key_exists('message', $output)) {
        // получаем id чата
        $chat_id = $output['message']['chat']['id'];
        // текстовое значение 
        $message = $output['message']['text'];
    // если это объект callback_query
    } elseif (array_key_exists('callback_query', $output)) {
        $chat_id = $output['callback_query']['message']['chat']['id'];
        $message = $output['callback_query']['data'];
    }


if ($message == '/start') {
	$set = add_user($chat_id,$first_name,$last_name,$user_id);
	// $set = is_user($chat_id);
	if ($set) {
		$button1 = array("text"=>"Доступные команды");
		$not_inline_keyboard = [[$button1]];
		$keyboard=array("keyboard"=>$not_inline_keyboard,"resize_keyboard" => true);
		$replyMarkup = json_encode($keyboard); 
		sendMessageMarkup($chat_id,'Клавиатура активирована!', $replyMarkup);
		// sendMessage($chat_id, 'Добро пожаловать! Новый пользователь зарегистрирован!
		// Осталось только добавить группу:)');
		$inline_button1 = array("text"=>"Добавить группу","callback_data"=>"command:addGroup");
	    $inline_button2 = array("text"=>"Помощь","callback_data"=>'/help');
	    $inline_keyboard = [[$inline_button1],[$inline_button2]];
	    $keyboard=array("inline_keyboard"=>$inline_keyboard,"resize_keyboard" => true);
	    $replyMarkup = json_encode($keyboard); 
	    sendMessageMarkup($chat_id, "Добро пожаловать, ".$first_name."! Пользователь зарегистрирован! Осталось только добавить группу:)", $replyMarkup);
	} else {
		sendMessage($chat_id, 'Вы уже зарегистрированы!');
	}
}

if (stristr($message,'/schedule ')) {
	if (preg_match('#(.+?)-(.+?)#is',$message))	{
		$message = str_replace('/schedule ', '', $message);
		$group_id = get_group_id($message);
		if ($group_id) {
			$inline_button1 = array('text'=>'Пн','callback_data'=>'command:Пн');
			$inline_button2 = array('text'=>'Вт','callback_data'=>'command:Вт');
			$inline_button3 = array('text'=>'Ср','callback_data'=>'command:Ср');
			$inline_button4 = array('text'=>'Чт','callback_data'=>'command:Чт');
			$inline_button5 = array('text'=>'Пт','callback_data'=>'command:Пт');
			$inline_button6 = array('text'=>'Сб','callback_data'=>'command:Сб');
			$inline_keyboard = [[$inline_button1,$inline_button2,$inline_button3,$inline_button4,
			$inline_button5,$inline_button6]];
			$keyboard=array("inline_keyboard"=>$inline_keyboard,"resize_keyboard" => true);
			$replyMarkup = json_encode($keyboard); 
			sendMessageMarkup($chat_id, "Выберите день:", $replyMarkup);
			set_OpType($chat_id,5);
			set_other_group_id($chat_id,$group_id);			
		} else {
			$button1 = array("text"=>"Отмена");
			$not_inline_keyboard = [[$button1]];
			$keyboard=array("keyboard"=>$not_inline_keyboard,"resize_keyboard" => true);
			$replyMarkup = json_encode($keyboard); 
			sendMessageMarkup($chat_id,"Введенная группа не существует!\nПопробуйте ввести еще раз!", $replyMarkup);
		}
	} else {
			$button1 = array("text"=>"Отмена");
			$not_inline_keyboard = [[$button1]];
			$keyboard=array("keyboard"=>$not_inline_keyboard,"resize_keyboard" => true);
			$replyMarkup = json_encode($keyboard); 
			sendMessageMarkup($chat_id,"Введенная группа не существует!\nПопробуйте ввести еще раз!", $replyMarkup);
	}
}

$OpType = get_OpType($chat_id);

if ($OpType == 1) {
	if (preg_match('#(.+?)-(.+?)#is',$message))	{
		$group_id = get_group_id($message);
		if ($group_id) {
			$add = add_group($message,$chat_id);
			if ($add) {
					sendMessage($chat_id, 'Группа успешно добавлена!');
				} else {
					sendMessage($chat_id, 'Упс...Что-то пошло не так, возможно ваша группа уже добавлена!');
				}
			set_OpType($chat_id,0);
			set_KbType($chat_id,1);
		} else {
			sendMessage($chat_id, "Введенная группа не существует!\nПопробуйте ввести еще раз!");
		}
	} else {
			sendMessage($chat_id, "Введенная группа не существует!\nПопробуйте ввести еще раз!");
	}
}

if (($OpType == 2) and ($message != 'Отмена') and ($message != 'Введите номер группы!')) {
	if (preg_match('#(.+?)-(.+?)#is',$message))	{
		$group_id = get_group_id($message);
		if ($group_id) {
			$change = change_group($message,$chat_id);
			if ($change) {
				$button1 = array("text"=>"Доступные команды");
				$not_inline_keyboard = [[$button1]];
				$keyboard=array("keyboard"=>$not_inline_keyboard,"resize_keyboard" => true);
				$replyMarkup = json_encode($keyboard); 
				sendMessageMarkup($chat_id,'Группа успешно изменена!', $replyMarkup);
				set_OpType($chat_id,0);
			} else {
				$button1 = array("text"=>"Отмена");
				$not_inline_keyboard = [[$button1]];
				$keyboard=array("keyboard"=>$not_inline_keyboard,"resize_keyboard" => true);
				$replyMarkup = json_encode($keyboard); 
				sendMessageMarkup($chat_id,'Данная группа уже установлена!', $replyMarkup);
			}			
		} else {
			$button1 = array("text"=>"Отмена");
			$not_inline_keyboard = [[$button1]];
			$keyboard=array("keyboard"=>$not_inline_keyboard,"resize_keyboard" => true);
			$replyMarkup = json_encode($keyboard); 
			sendMessageMarkup($chat_id,"Введенная группа не существует!\nПопробуйте ввести еще раз!", $replyMarkup);
		}
	} else {
			$button1 = array("text"=>"Отмена");
			$not_inline_keyboard = [[$button1]];
			$keyboard=array("keyboard"=>$not_inline_keyboard,"resize_keyboard" => true);
			$replyMarkup = json_encode($keyboard); 
			sendMessageMarkup($chat_id,"Введенная группа не существует!\nПопробуйте ввести еще раз!", $replyMarkup);
	}
}

if ($message == 'command:addGroup') {
	set_OpType($chat_id,1);
	sendMessage($chat_id,'Введите номер группы!');
}

if ($message == 'command:changeGroup') {
	set_OpType($chat_id,2);
	sendMessage($chat_id,'Введите номер группы!');
}

if ($message == 'command:get_schedule') {
	// $date_today = date('Y-m-d');
	$inline_button1 = array('text'=>'Пн','callback_data'=>'command:Пн');
	$inline_button2 = array('text'=>'Вт','callback_data'=>'command:Вт');
	$inline_button3 = array('text'=>'Ср','callback_data'=>'command:Ср');
	$inline_button4 = array('text'=>'Чт','callback_data'=>'command:Чт');
	$inline_button5 = array('text'=>'Пт','callback_data'=>'command:Пт');
	$inline_button6 = array('text'=>'Сб','callback_data'=>'command:Сб');
	$inline_keyboard = [[$inline_button1,$inline_button2,$inline_button3,$inline_button4,
	$inline_button5,$inline_button6]];
	$keyboard=array("inline_keyboard"=>$inline_keyboard,"resize_keyboard" => true);
	$replyMarkup = json_encode($keyboard); 
	sendMessageMarkup($chat_id, "Выберите день:", $replyMarkup);
}

switch($message) {
	case 'command:Пн':
		$callback_day = 1;
		break; 
	case 'command:Вт':
		$callback_day = 2;
		break;
	case 'command:Ср':
		$callback_day = 3;
		break; 
	case 'command:Чт':
		$callback_day = 4;
		break;
	case 'command:Пт':
		$callback_day = 5;
		break;
	case 'command:Сб':
		$callback_day = 6;
		break;    
}

if ($callback_day) {
	$num_day_today = date('w', mktime(0,0,0,date('m'),date('d'),date('Y')));
	$diff_date = strtotime($callback_day-$num_day_today.' days');
	$date = date('Ymd',$diff_date);

	if ($OpType == 5) {
		$group_id = get_other_group_id($chat_id);
		set_OpType($chat_id,0);
	} else {
		$group_id = group_id_from_user($chat_id);
	}

	if ($group_id) {
		$schedule = get_schedule($group_id,$date);
		sendMessage($chat_id,$schedule);
	} else {
		sendMessage($chat_id,'Вы не добавили номер вашей группы!');
	}
}

if ($message == 'Отмена') {
	set_OpType($chat_id,0);
	$button1 = array("text"=>"Доступные команды");
	$not_inline_keyboard = [[$button1]];
	$keyboard=array("keyboard"=>$not_inline_keyboard,"resize_keyboard" => true);
	$replyMarkup = json_encode($keyboard); 
	sendMessageMarkup($chat_id,'Операция отменена!', $replyMarkup);
}

$KbType = get_KbType($chat_id);
if ($message == 'Доступные команды') {
	if ($KbType == 1) {
	    $inline_button1 = array("text"=>"Изменить группу","callback_data"=>"command:changeGroup");
	    $inline_button2 = array("text"=>"Расписание","callback_data"=>'command:get_schedule');
	    $inline_button3 = array("text"=>"Обратная связь","callback_data"=>'command:feedback');
	    $inline_keyboard = [[$inline_button1],[$inline_button2],[$inline_button3]];
	    $keyboard=array("inline_keyboard"=>$inline_keyboard,"resize_keyboard" => true);
	    $replyMarkup = json_encode($keyboard); 
	    sendMessageMarkup($chat_id, "Выберите команду:", $replyMarkup);
	} elseif ($KbType == 0) {
		$inline_button1 = array("text"=>"Добавить группу","callback_data"=>"command:addGroup");
		$inline_button2 = array("text"=>"Помощь","callback_data"=>'/help');
		$inline_keyboard = [[$inline_button1],[$inline_button2]];
		$keyboard=array("inline_keyboard"=>$inline_keyboard,"resize_keyboard" => true);
		$replyMarkup = json_encode($keyboard); 
		sendMessageMarkup($chat_id, "Выберите команду:", $replyMarkup);
	}
}

if ($message == 'command:feedback') {
	$button1 = array("text"=>"Отмена");
	$not_inline_keyboard = [[$button1]];
	$keyboard=array("keyboard"=>$not_inline_keyboard,"resize_keyboard" => true);
	$replyMarkup = json_encode($keyboard); 
	sendMessageMarkup($chat_id,"Отправление сообщения:\nВведите ваш email!", $replyMarkup);
	// sendMessage($chat_id,"Отправление сообщения:\nВведите ваш email!");
	// sendMessage($chat_id,'Введите ваш email!');
	set_OpType($chat_id,3);
}

if ($OpType == 3) {
	if (strpos($message,'@') and strpos($message,'.')) {
		set_email($chat_id,$message);
		$button1 = array("text"=>"Отмена");
		$not_inline_keyboard = [[$button1]];
		$keyboard=array("keyboard"=>$not_inline_keyboard,"resize_keyboard" => true);
		$replyMarkup = json_encode($keyboard); 
		sendMessageMarkup($chat_id,'Введите ваше сообщение!', $replyMarkup);
		set_OpType($chat_id,4);
	} else if ($message != 'Отмена') {
		sendMessage($chat_id,"Неверный формат email!\nПопробуйте ввести еще раз!");
		set_OpType($chat_id,3);
	}
}

if (($OpType == 4) and ($message != 'Отмена')) {
	$email = get_email($chat_id);
	send_email($email,$message,$chat_id,$first_name);
	set_OpType($chat_id,0);
	$button1 = array("text"=>"Доступные команды");
	$not_inline_keyboard = [[$button1]];
	$keyboard=array("keyboard"=>$not_inline_keyboard,"resize_keyboard" => true);
	$replyMarkup = json_encode($keyboard); 
	sendMessageMarkup($chat_id,'Сообщение успешно отправлено!', $replyMarkup);
}

if ($message == '/keyboard') {
	$button1 = array("text"=>"Доступные команды");
	$not_inline_keyboard = [[$button1]];
	$keyboard=array("keyboard"=>$not_inline_keyboard,"resize_keyboard" => true);
	$replyMarkup = json_encode($keyboard); 
	sendMessageMarkup($chat_id,'Клавиатура активирована!', $replyMarkup);
}

if ($message == '/help') {
	sendMessage($chat_id,"Помощь:\n---------------------------------------\nДля начала работы необходимо добавить номер группы.\n---------------------------------------\nДля просмотра расписания другой группы выполните команду по шаблону: '/schedule <НОМЕР ГРУППЫ>'.\n---------------------------------------\nЕсли вы обнаружили ошибку, обязательно сообщите об этом. Для этого воспользуйтесь обратной связью.");
}

?>