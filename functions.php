<?php

// проверяем пользователя, авторизован он или нет
function check_user()
{
  //if (!isset($_SESSION['user_id']))
  if(!isset($_SESSION['user_class']))
  {
    // то проверяем его куки
    // вдруг там есть логин и пароль к нашему скрипту
    if (isset($_COOKIE['login']) && isset($_COOKIE['password']))
    {
      // если же такие имеются
      // то пробуем авторизировать пользователя по этим логину и паролю
      $login = mysql_escape_string($_COOKIE['login']);
      $password = mysql_escape_string($_COOKIE['password']);
      // и по аналогии с авторизацией через форму:
      // делаем запрос к БД
      // и ищем юзера с таким логином и паролем
      $user = new CUser($login,$password);
      $_SESSION['user_class'] = serialize($user);
      header('Location: '.$_SERVER['REQUEST_URI']);
    }
    else {
    print_access_denaid();
    }
  } 

  //if (isset($_SESSION['user_id']))
  //if(isset($_SESSION['user_class']))
  /*{
    //$user = unserialize($_SESSION['user_class']);
    $query = "SELECT `login` FROM `users` WHERE `id`='{$_SESSION['user_id']}' LIMIT 1";
    $sql = mysql_query($query) or die(mysql_error());
    // если нету такой записи с пользоватеем
    // ну вдруг удалил его пока лазил по сайту.. =)
    // то надо ему убить ID, установленный в сессии, чтобы он был гостем
    if (mysql_num_rows($sql) != 1)
    {
      header('location: login.php?logout');
      exit;
    }
    $user = unserialize($_SESSION['user_class']);

    // переброска на главную страницу пользоывтеля, если она не является index.php
    //if($user->index_page != 'index.php')
      //header('location: '.$user->index_page);
  }*/
}

function print_date($date)
{
    $mounth = array("01"=>"января","02"=>"февраля","03"=>"марта","04"=>"апреля","05"=>"май","06"=>"июня","07"=>"июля","08"=>"августа","09"=>"сентября","10"=>"октября","11"=>"ноября","12"=>"декабря");
    $tmp = explode('-',$date);
    if($tmp[1] == 0)
      $ret = "<font color=\"red\"><b>ДАТА НЕ УСТАНОВЛЕНА</b></font><details><font color=\"gray\">functions.php:print_date($date)</font></details>";
      else $ret = $tmp[2].' '.$mounth[$tmp[1]].' '.$tmp[0].' г.';
    return $ret;
}

//int2ip: Преобразование числа в ip-адрес
function int2ip($int)
{
        $p[0]=($int>>24) & 0xff;
        $p[1]=($int>>16) & 0x00ff;
        $p[2]=($int>>8) & 0x0000ff;
        $p[3]=$int & 0x000000ff;
        return join('.', $p);
}

//ip2int: Преобразование ip-адреса в число (которое можно хранить в PHP, помещать в MySQL и.т.д.) 
function ip2int($ip)
{
        $rgIp=explode(".", $ip);
        if ($rgIp[0]>=0 && $rgIp[0]<256 && $rgIp[1]>=0 && $rgIp[1]<256 && $rgIp[2]>=0 && $rgIp[2]<256 && $rgIp[3]>=0 && $rgIp[3]<256)
                return $rgIp[3]+($rgIp[2]<<8)+($rgIp[1]<<16)+($rgIp[0]<<24);
        else
                return 0;
}


/** высчитывание возраста из дня рождения и выбранной даты */
function get_age($birdth,$qwe)
{
    $sec = 0;
    $min = 0;
    $hour = 0;
    $b = explode('-',$birdth);
    $birthdate_unix = mktime($hour, $min, $sec, $b[1], $b[2], $b[0]);
    $current_unix = strtotime($qwe);
    $period_unix=$current_unix - $birthdate_unix;
    return floor($period_unix / (365*24*60*60));
}

// Получение даты из номера квартала
// $k - номер кавартала от 1 до 4
// $y - текущий год
// Возвращает массив, первое значение начало, второе значение конец квартала
function get_kvartal($k,$y)
{
  $arr = array();
  switch($k)
  {
    case 1:
      $arr= array(1=>$y.'-01-01', 2=>$y.'-03-31');
      break;
    case 2:
      $arr= array(1=>$y.'-04-01', 2=>$y.'-06-30');
      //echo '0000-04-01';
      //echo '0000-06-30';
      break;
    case 3:
      $arr= array(1=>$y.'-07-01', 2=>$y.'-09-30');
      //echo '0000-07-01';
      //echo '0000-09-30';
      break;
    case 4:
      $arr= array(1=>$y.'-10-01', 2=>$y.'-12-31');
      //echo '0000-10-01';
      //echo '0000-12-31';
      break;
  }
  return $arr;
}

// установка пользователя в онлайн или оффлайн
function get_online($id)
{
    $query = "SELECT `user_id` FROM `online` WHERE `user_id`='{$id}'";
    $sql = mysql_query($query) or die(mysql_error());
    $t = time();
    if (mysql_num_rows($sql) == 1)
    {
        $q = "UPDATE `online` SET 
            `last_time`='{$t}',
            `ip` = INET_ATON('{$_SERVER['REMOTE_ADDR']}') WHERE `user_id`='{$id}'";
        mysql_query($q) or die(mysql_error());
    }else{
        $q = "INSERT INTO `online` SET
            `last_time`='{$t}',
            `user_id`='{$id}',
            `ip` = INET_ATON('{$_SERVER['REMOTE_ADDR']}')";
        mysql_query($q) or die(mysql_error());
    }
}

//получить номер отделения из аббревиатуры
function get_id_otd($otd)
{
  $ttt = array('uzi'=>8,'ofd'=>4,'nko'=>9,'rentgen'=>6,'fizio'=>5,'kdl'=>7,'1otd'=>1,'2otd'=>2,'3otd'=>3,'neuromed'=>14);
  return $ttt[$otd];
}

/** Получить название отделения из аббревиатуры
  * @param $otd - идентификатор отделение, буквено uzi,kdl,ofd. Или цифра 1,2,3
  * @return русское название отделения
  */
function get_otd($otd)
{

/*switch($otd)
    {
        case 'uzi':
        case 8:
            $str = 'УЗИ';
            break;
        case 'ofd':
        case 4:
            $str = 'ОФД';
            break;
        case 'nko':
        case 9:
            $str = 'НКО';
            break;
        case 'rentgen':
        case 6:
            $str = 'Рентген';
            break;
        case 'fizio':
        case 5:
            $str = 'Физио';
            break;
        case 'kdl':
        case 7:
            $str = 'КДЛ';
            break;
        case '1otd':
        case 1:
            $str = '1 Отделение';
            break;
        case '2otd':
        case 2:
            $str = '2 Отделение';
            break;
        case '3otd':
        case 3:
            $str = '3 Отделение';
            break;
        case 'neuromed':
        case 14:
	    $str = 'Нейромед';
	    break;
        default:
	  $str = 'Не установлено';
	  break;
    }
    return $str;
    unset($str);
    */
    if(is_numeric($otd))
      $q = "SELECT `name` FROM `p_group` WHERE `id`='{$otd}' LIMIT 1";
      else $q = "SELECT `name` FROM `p_group` WHERE `table` = '{$otd}' LIMIT 1";
    $s = mysql_query($q) or die(mysql_error());
    $r = mysql_fetch_assoc($s);
    return $r['name'];
}

function get_name_otd($id)
{
  $q = mysql_query("SELECT `table` FROM `p_group` WHERE `id`='{$id}' LIMIT 1") or die(mysql_error());
  $r = mysql_fetch_assoc($q);
  return $r['table'];
  //$ar = array(8=>'uzi',4=>'ofd',9=>'nko',6=>'rentgen',5=>'fizio',7=>'kdl',1=>'1otd',2=>'2otd',3=>'3otd',14=>'neuromed');
  //return $ar[$id];
}

// Названия стационарных отделений на основе ID
function get_otd_name($p_group,$color = true)
{
global $arr_p_group;
return $arr_p_group[$p_group];
/*switch($p_group)
            {
                case 0:
		  if($color == true)
                    $str = '<font color="'.LPU_WARNING_COLOR.'">Отеделение не установлено</font>';
                    else $str = 'Отеделение не установлено';
                    break;
                case 1:
                    $str = '1 отделение';
                    break;
                case 2:
                    $str = '2 отделение';
                    break;
                case 3:
                    $str = '3 отделение';
                    break;
                case 4:
                    $str = 'НКО';
                    break;
            }
  return $str;*/
}


/** Вывести на экран выпадающий список с выбором отделения
  * @param $otd - устанвить выбранно отделение, 0 - не выбрано
 */
function print_select_otd($otd = 0)
{
  //$ar_o = array('Не установлено','1 Отделение','2 Отделение','3 Отделение','НКО','Коуглосуточный стационар','Дневной стационар');
  global $arr_p_group;
  $c = '';
  $s = '<select name="p_group" id="id_p_group">';
  foreach($arr_p_group as $k=>$v)
    if($k == $otd)
      $c .= '<option value="'.$k.'" selected>'.$v.'</option>';
      else $c .= '<option value="'.$k.'">'.$v.'</option>';
  print $s.$c.'</select>';
}

// извлечь из быза ФИО пациента и id, по ID карты
function get_pacient_name($karta_id)
{
  $arr = array();
  $query = "SELECT `pacient_id` FROM `karta` WHERE `id`='{$karta_id}'";
  $sql_k = mysql_query($query) or die(mysql_error());
  $row_k = mysql_fetch_assoc($sql_k);
  $query = "SELECT `surname`,`name`,`otchestvo`,`birdth`,`id`,`document` FROM `pacient` WHERE `id`='{$row_k['pacient_id']}'";
  $sql_p = mysql_query($query) or die(mysql_error());
  $row_p = mysql_fetch_assoc($sql_p);
  $arr['name'] = $row_p['surname'].' '.$row_p['name'].' '.$row_p['otchestvo'];
  $arr['id'] = $row_p['id'];
  $arr['birdth'] = $row_p['birdth'];
  $arr['document'] = $row_p['document'];
  return $arr;
}

/**	Возвращает массив даты начала месяца и конца месяца
  *	@param $d - какой месяц
  *	@param $year - какой год
  *	@return array[0] - первый день месяца
  *	@return array[1] - последний день месяца
  */
function get_mounth($d,$year)
{
  $sd = $year.'-'.$d.'-'.'01';
  $m = $d+1;
  $lastday = mktime(0, 0, 0, $m, 0, $year);
  $ed =date('Y-m-d',$lastday);
  //$ed = $year.'-'.$d.'-'.'31';
  $ar = array(0=>$sd,1=>$ed);
  return $ar;
}

/**	выввести внизу экрана текущую страница и сколько их всего
  *
  */
function print_num_pages($num_pages,$page,$get='')
{
  $argv1 = '';
  unset($_GET['page']);
  foreach($_GET as $k=>$v)
    $argv1 .= "&$k=$v";
  

  $p = $page+1;
  print '<p>';
  print '<font size="6"><b>'.$p.'</b></font> <font color="gray" size="3">из '.$num_pages.'</font>';
  if($p != 1)
    print ' <a href="?page='.$page.$argv1.'">Предыдущая</a> ';
  if($p != $num_pages)
  {
    $pp = $page+2;
    print ' <font size="4"><a href="?page='.$pp.$argv1.'"><b>Следующая страница</b></a></a>';
  }
  print '</p>';
}

// возвращает окончательную приставку к число. год, года, лет и т.д.
function ci($n,$c)
{
 return $c[0].((preg_match("/^[0,2-9]?[1]$/",$n))?$c[2]:((preg_match("/^[0,2-9]?[2-4]$/",$n))?$c[3]:$c[1]));
}

/*$ruCi=[
'year'=>['','лет','год','года'],
'rub'=>['руб','лей','ль','ля'],
'day'=>['','дней','день','дня']
];*/
$ruCi = array(
'year'=>array('','лет','год','года'),
'rub'=>array('руб','лей','ль','ля'),
'day'=>array('','дней','день','дня'));


// обрезка строки до указанного количество символов
function cropStr($string,$length = 50)
{
  
  $s = preg_replace('/\s[^\s]+$/', '', mb_substr($string, 0, $length, 'UTF-8'));
  if(strlen($string) > strlen($s))
    $s .= ' ...';
  return $s;
    
  //return mb_substr($string,0,$length,'UTF-8');
}

// проверка корректности даты
function check_date($date,$format = 'd.m.Y',$out = true)
{
  if($date != '0000-00-00') {
    $date = date('Y-m-d',strtotime($date));
    $d = explode('-',$date);
    if(checkdate($d[1],$d[2],$d[0]))
    {
      return date($format,strtotime($date));
      //return strftime($format,strtotime($date));
    } else {
      if($out)
	return '<font color="red"><b>Неправильная дата</b></font>';
      else return '';
    }
  }
}

//	изменение размера изображения
function imageresize($outfile,$infile,$percents,$quality) {
    $im=imagecreatefromjpeg($infile);
    $w=imagesx($im)*$percents/100;
    $h=imagesy($im)*$percents/100;
    $im1=imagecreatetruecolor($w,$h);
    imagecopyresampled($im1,$im,0,0,0,0,$w,$h,imagesx($im),imagesy($im));

    imagejpeg($im1,$outfile,$quality);
    imagedestroy($im);
    imagedestroy($im1);
}
    
    
// подписание документов ЭЦП    
// $table - в какой таблице
// $id - каую запись
// $userid - какой польователь подписывает
function set_ecp($id,$table,$userid)
{
  $q = "SELECT * FROM `{$table}` WHERE `id`='{$id}'";
  $s = mysql_query($q) or die(mysql_error());
  $r = mysql_fetch_assoc($s);
  $str = '';
  unset($r['hash']);
  unset($r['hash_date']);
  unset($r['hash_user']);
    
  foreach($r as $k=>$v)
  {
    $str .= $v;
  }
  $md = md5($str);
  $d = date('Y-m-d H:i:s');
  $q = "UPDATE `{$table}` SET `hash` = '{$md}',`hash_date` ='{$d}',`hash_user` = '{$userid}' WHERE `id`='{$id}'";
  mysql_query($q) or die(mysql_error());
}

// проверить права группы пользователей
function check_access($user,$access)
{
  $ar = explode(',',$user->group);
  $ret = true;
  foreach($ar as $k=>$v)
  {
    /*foreach($access as $kk=>$vv)
      if($v == $v)
	$ret = true;*/
    $i = (int)$v;
    if(in_array($i,$access))
      $ret = false;
  }
  //var_dump($access);
  //var_dump($i);
  return $ret;
}


// Определяет с какого браузера открыта страница
function get_browser_info()
{
  $agent = $_SERVER['HTTP_USER_AGENT'];
if(strpos($agent,'Chrome')) return 'Chrome';
elseif(strpos($agent,'Firefox')) return 'Firefox';
elseif(strpos($agent,'Safari')) return 'Safari';
elseif(strpos($agent,'Opera')) return 'Opera';
elseif(strpos($agent,'MSIE 8.0')) return 'ie8';
elseif(strpos($agent,'MSIE 9.0')) return 'ie9';
elseif(strpos($agent,'MSIE 10.0')) return 'ie10';
elseif(strpos($agent,'Trident/7')) return 'ie11';
//return $agent; 
}

// получить ФИО из полного имени
function convertFIO($f,$i,$o)
{
    $sName = $f.' '.$i.' '.$o;
    return preg_replace('~^(\S++)\s++(\S)\S++\s++(\S)\S++$~u', '$1 $2.$3.', $sName);
}
?>
