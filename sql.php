<?php
function add_all_group() {
	$user = "renatuma";
	$password = "80662391610myac";
	// $link = new PDO('mysql:host=217.182.197.234;dbname=schedule;charset=utf8',$user,$password);
	// $sql = $link->exec("SET NAMES 'utf8';");
	try {
	    $dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
	} catch (PDOException $e) {
	    die($e->getMessage());
	}
	$url_urfu = 'https://urfu.ru/api/schedule/groups/';
	//$url_urfu = 'https://urfu.ru/api/schedule/groups/suggest/?query=РИ-451219';
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

	$obj= json_decode($data,true);
	//echo $data;
	foreach ($obj as $result) {
		$sql = $dbh->exec('INSERT INTO groups (group_id,group_name) VALUES ('.$result['id'].',"'.$result['title'].'")');
	}
	echo 'done';
	curl_close($file);
	return $sql;
}

function group_id_from_user($user_id) {
	try {
    	$dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8','renatuma','80662391610myac');
    	$sql = $dbh->query('SELECT group_id FROM user WHERE user_id='.$user_id);
    	$result = $sql->fetch();
	} catch (PDOException $e) {
    	die($e->getMessage());
	}
	return $result[0];
}

function get_group_id($group_name) {
	$group_name = '"'.$group_name.'"';
	try {
    	$dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8','renatuma','80662391610myac');
    	$sql = $dbh->query('SELECT group_id FROM groups WHERE group_name='.$group_name);
    	$result = $sql->fetch();
	} catch (PDOException $e) {
    	die($e->getMessage());
	}
	return $result[0];
}

function is_user($user_id) {
	$user = "renatuma";
	$password = "80662391610myac";
	try {
	    $dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
	    $sql = $dbh->query('SELECT user_id FROM user WHERE user_id='.$user_id);
	    $result = $sql->fetch();
	} catch (PDOException $e) {
	    die($e->getMessage());
	}
	return $result[0];
}

function is_group($group_id,$user_id) {
	$user = "renatuma";
	$password = "80662391610myac";
	try {
	    $dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
	    $sql = $dbh->query('SELECT group_id FROM user WHERE group_id='.$group_id.' and user_id='.$user_id);
	    $result = $sql->fetch();
	} catch (PDOException $e) {
	    die($e->getMessage());
	}
	return $result[0];
}

function add_group($group_name,$user_id) {
	$user = "renatuma";
	$password = "80662391610myac";
	$group_id = get_group_id($group_name);
	$is = is_group($group_id,$user_id);
	 if (!$is) {
		try {
		    $dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
		    $sql = $dbh->exec('UPDATE user SET group_id='.$group_id.' WHERE user_id='.$user_id);
		} catch (PDOException $e) {
	    die($e->getMessage());
		}
	 	return 1;
	 } else {
	 	return 0;
	 }
}

function add_user($user_id,$first_name,$last_name,$username) {
	$user = "renatuma";
	$password = "80662391610myac";
	$is = is_user($user_id);
	 if (!$is) {
		try {
		    $dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
		    $sql = $dbh->exec('INSERT INTO user (user_id,first_name,last_name,username) VALUES ('.$user_id.',"'.$first_name.'","'.$last_name.'","'.$username.'")');
		} catch (PDOException $e) {
	    die($e->getMessage());
		}
	 	return 1;
	 } else {
	 	return 0;
	 }
}

function change_group($group_name,$user_id) {
	$user = "renatuma";
	$password = "80662391610myac";
	$group_id = get_group_id($group_name);
	try {
		$dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
		$sql = $dbh->exec('UPDATE user SET group_id='.$group_id.' WHERE user_id='.$user_id);
	} catch (PDOException $e) {
	   die($e->getMessage());
	}
	return $sql;
}

function set_OpType($user_id,$OpType) {
	$user = "renatuma";
	$password = "80662391610myac";
	try {
		$dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
		$sql = $dbh->exec('UPDATE user SET OpType='.$OpType.' WHERE user_id='.$user_id);
	} catch (PDOException $e) {
	   die($e->getMessage());
	}
	return $sql;
}

function get_OpType($user_id) {
	$user = "renatuma";
	$password = "80662391610myac";
	try {
	    $dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
	    $sql = $dbh->query('SELECT OpType FROM user WHERE user_id='.$user_id);
	    $result = $sql->fetch();
	} catch (PDOException $e) {
	    die($e->getMessage());
	}
	return $result[0];
}

function get_KbType($user_id) {
	$user = "renatuma";
	$password = "80662391610myac";
	try {
	    $dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
	    $sql = $dbh->query('SELECT kb_id FROM user WHERE user_id='.$user_id);
	    $result = $sql->fetch();
	} catch (PDOException $e) {
	    die($e->getMessage());
	}
	return $result[0];
}

function set_KbType($user_id,$KbType) {
	$user = "renatuma";
	$password = "80662391610myac";
	try {
		$dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
		$sql = $dbh->exec('UPDATE user SET kb_id='.$KbType.' WHERE user_id='.$user_id);
	} catch (PDOException $e) {
	   die($e->getMessage());
	}
	return $sql;
}

function set_email($user_id,$email) {
	$user = "renatuma";
	$password = "80662391610myac";
	try {
		$dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
		$sql = $dbh->exec('UPDATE user SET email="'.$email.'" WHERE user_id='.$user_id);
	} catch (PDOException $e) {
	   die($e->getMessage());
	}
	return $sql;
}

function get_email($user_id) {
	$user = "renatuma";
	$password = "80662391610myac";
	try {
	    $dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
	    $sql = $dbh->query('SELECT email FROM user WHERE user_id='.$user_id);
	    $result = $sql->fetch();
	} catch (PDOException $e) {
	    die($e->getMessage());
	}
	return $result[0];
}

function set_other_group_id($user_id,$group_id) {
	$user = "renatuma";
	$password = "80662391610myac";
	try {
		$dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
		$sql = $dbh->exec('UPDATE user SET other_group_id="'.$group_id.'" WHERE user_id='.$user_id);
	} catch (PDOException $e) {
	   die($e->getMessage());
	}
	return $sql;
}

function get_other_group_id($user_id) {
	$user = "renatuma";
	$password = "80662391610myac";
	try {
	    $dbh = new PDO('mysql:host=localhost;dbname=schedule;charset=utf8',$user,$password);
	    $sql = $dbh->query('SELECT other_group_id FROM user WHERE user_id='.$user_id);
	    $result = $sql->fetch();
	} catch (PDOException $e) {
	    die($e->getMessage());
	}
	return $result[0];
}
