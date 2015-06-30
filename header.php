<?php

function print_header($title = ' ',$script = '',$subdir = false,$ismobile = false)
{
  $user = unserialize($_SESSION['user_class']);
  get_online($user->id);
  header("Cache-Control: no-store, no-cache, must-revalidate");
  ?>
<!doctype html>
<html lang="ru" dir="ltl">
<head>
<title><?php echo $title;?></title>
<meta charset="utf-8">
<?php
  /*if($subdir)
    {
      print '<link rel="stylesheet" type="text/css" href="../style.php?c='.$user->colors.'"/>';
    }
    else
    {
      print '<link rel="stylesheet" type="text/css" href="style.php?c='.$user->colors.'"/>';

    }*/
    
  print '<link rel="stylesheet" type="text/css" href="'.LPU_HOST.'style.php?c='.$user->colors.'"/>';
  switch(get_browser_info())
  {
    case 'Chrome':
    case 'Opera':
      break;
    default:
      echo '<link rel="stylesheet" type="text/css" href="'.LPU_HOST.'calendar/calendar.css"><script src="'.LPU_HOST.'calendar/calendar.js" type="text/javascript"></script>';
  }
  ?>
<script type="text/javascript">
<?php
    print $script;
?>
</script>    

<?php
  
  if($ismobile){
  print '<link rel="stylesheet" href="css/jquery.sidr.dark.css">';
    print '</head><body>
    <!-- Include jQuery -->
    <!--<script src="js/jquery.js"></script>-->
    <script src="js/jquery-2.1.1.js"></script>
    <!-- Include the Sidr JS -->
    <script src="js/jquery.sidr.min.js"></script>
    ';
  }else {
    
    print '</head><body>';
  }
  $start_time = microtime();
  $start_array = explode(" ",$start_time);
  $GLOBALS['start_time'] = $start_array[1] + $start_array[0];
}


// вывод на экран сообшения что доступ закрыт, и показ формы для ввода логина и пароля
function print_access_denaid($invalidlogin = false)
{
$start_time = microtime();
$start_array = explode(" ",$start_time);
$GLOBALS['start_time'] = $start_array[1] + $start_array[0];
    ?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="<?php echo LPU_HOST;?>style.php?c=0"/>
<script type="text/javascript">
//<![CDATA[
// show login form in top frame
if (top != self) {
window.top.location.href=location;
}
//]]>
</script>    
</head><body class="loginform">
<div class="container">
<?php
if($invalidlogin)
	print '<h1><font color="red">Неправельный логин или пароль</font></h1>';
	else print '<h1>Доступ закрыт.<br>Необходима авторизация</h1>';
?>

<form action="login.php" method="post" name="login_form" class="login">
<fieldset>
<legend>Авторизация</legend>
<div class="item">
<label for="input_username">Пользователь:</label>
<input type="text" name="login" id="input_username" size="24" class="textfield"/>
</div><br>
<div class="item">
<label for="input_password">Пароль:</label>
<input type="password" name="password" id="input_password" size="24" class="textfield"/>
</div>
<input type="hidden" name="server" value="1"/>
<input type="hidden" name="remember" value="1" />
</fieldset>
<fieldset class="tblFooters">
<input value="Войти" type="submit" id="input_go"

<?php 
  if(LPU_MAIL_YANDEX == 'true')
    print 'onclick="send_mail()"';
print '/></fieldset>
</form>
</div>';


  if(LPU_MAIL_YANDEX == 'true'){
  
    print '<script type="text/javascript">
  function send_mail()
  {
    document.getElementById(\'mail_login\').value = document.getElementById(\'input_username\').value;
    document.getElementById(\'mail_pass\').value = document.getElementById(\'input_password\').value;
    document.getElementById(\'mail_ya\').submit();
  }
  </script>
  <div style="display: none;"> <form method="post" action="https://passport.yandex.ru/for/nii-nk.ru?mode=auth" id="mail_ya"> 

  <div class="label">Логин:</div>
  <input type="text" name="login" value="" tabindex="1" id="mail_login"/>
  <div class="label">Пароль:</div>
  <input type="hidden" name="retpath" value="http://mail.yandex.ru/for/nii-nk.ru">
  <input type="password" name="passwd" value="" maxlength="100" tabindex="2" id="mail_pass"/> <br>
  
  <label for="a"><input type="checkbox" name="twoweeks" id="a" value="yes" tabindex="4"/>запомнить
  меня</label> (<a href="http://help.yandex.ru/passport/?id=922493">что это</a>)

  <input type="submit" name="In" value="Войти" tabindex="5"/> </form>
</div>';
  }
    
}

function end_html($ismobile = false)
{
  if(!empty($_SESSION['user_class']))
  {
    $user = unserialize($_SESSION['user_class']);
    //$version = 'альфа версия 0.0.4.1';
    $version = '';
    $end_time = microtime();
    $end_array = explode(" ",$end_time);
    $end_time = $end_array[1] + $end_array[0];
    // вычитаем из конечного времени начальное
    $time = $end_time - $GLOBALS['start_time'];
    // выводим в выходной поток (броузер) время генерации страницы
  
    if($ismobile)
      print '</div></div></div>';
    else {
      print '<footer><table border="0" width="100%"><tr><td width="80%"></td><td>';
      if($user->premission == $GLOBALS['g_admin'])
	printf("Генерация страницы %f секунд",$time);
      print "</td></tr><tr><td width=\"80%\"></td><td align=\"right\">$version</td></tr></table></footer></body></html>";
    }
  }
}

function print_username($subdir=false)
{
  //global $user;
  $user = unserialize($_SESSION['user_class']);
  if($user->avatar == '')
    $savimg = LPU_HOST.'themes/no_ava_200x200.png';
    else $savimg = LPU_HOST.$user->avatar;

  //if(!file_exists(LPU_HOST.$savimg))
  //  $savimg = LPU_HOST.'themes/no_ava_200x200.png';
  
    print '<style>
.header-user-pic {
border-radius: 50%;
background-size: cover;
box-shadow: inset 0 0 0 1px rgba(0, 0, 0, 0.15);
width: 42px;
height: 42px;
}
.b-mail-dropdown__handle {
display: inline;
display: inline-block;
cursor: pointer;
}
.header-user-name {
margin-right: 10px;
margin-left: 6px;
display: inline-block;
white-space: normal;
vertical-align: middle;
text-align: right;
}


ul.dropdown li { position: relative; }
 ul.dropdown,
 ul.dropdown-inside {
 list-style-type: none;
 padding: 0;
 }
 ul.dropdown-inside {
 position: absolute;
 left: -9999px;
 }
 ul.dropdown li.dropdown-top {
 display: inline;
 float: right;
 margin: 0 1px 0 0;
 }
 ul.dropdown li.dropdown-top a {
 padding: 3px 10px 4px;
 display: block;
 }
 ul.dropdown a.dropdown-top { background: #efefef; }
 ul.dropdown a.dropdown-top:hover { padding: 2px 10px 5px; }
 ul.dropdown li.dropdown-top:hover .dropdown-inside {
 display: block;
 left: 0;
 }
 ul.dropdown .dropdown-inside { background: #fff; }
 ul.dropdown .dropdown-inside a:hover { background: #efefef; }

.gb_6 {
background-size: 96px 96px;
border: none;
vertical-align: top;
height: 96px;
width: 96px;
}

.gbip {
background-image: url('.$savimg.');
}

li.menu-tem {
  text-align: left;
  
}
</style>
<header>
<table width="100%" class="menucontainer" border="0" height="50px" cellspacing="0">';
  if(LPU_MAIL_YANDEX == 'true')
  {
    $cmd = "curl -H 'PddToken: ".LPU_MAIL_TOKEN."' 'https://pddimp.yandex.ru/api2/admin/email/counters?domain=nii-nk.ru&login=".$user->login."'";
    $arr = array();
    exec($cmd,$arr);
    $ar = json_decode($arr[0]);
    //print_r($ar);
    //print '<!-- '.$ar->counters->unread.' -->';
    print '<tr>
      <td></td><td>Писем: '.$ar->counters->unread.'</td>
    </tr>';
  }
  
  print '<tr><td valign="top">
    <a href="'.LPU_HOST.$user->index_page.'"><img src="'.LPU_HOST.'themes/img/b_home.png" title="Домашняя страница"></a>';
    $d = date("Y-m-d");
 $ar = explode(',',$user->group);
 foreach($ar as $k=>$v)
// switch($user->group)
  switch($v)
  {
    case $GLOBALS['group_admin']:	// Админ
      print ' | <a href="'.LPU_HOST.'pacient_visit.php?date='.$d.'">Регистратура</a>';
      print ' | <a href="'.LPU_HOST.'history-1.php">Истории болезни</a>';
      print ' | <a href="'.LPU_HOST.'vahta.php?date='.$d.'">Журнал дежурного</a>';
      print ' | <a href="'.LPU_HOST.'pacient_list.php">Список пациентов</a>';
      print '<!-- | <a href="'.LPU_HOST.'statistic_dms-1.php">Статистика по ДМС</a>-->
	    | <a href="'.LPU_HOST.'dms_reestr.php">Реестр ДМС</a>
	    | <a href="'.LPU_HOST.'oms.php">Реестр ОМС</a>';
      print ' | <a href="'.LPU_HOST.'tests.php?q=1">Тесты</a>';
      print ' | <a href="'.LPU_HOST.'apteka.php"><img src="'.LPU_HOST.'themes/img/apteka.png" width="16" height="16">Аптека</a>';
      print ' | <a href="'.LPU_HOST.'service.php">Инвентарная книга запчастей</a>';
      print ' | <a href="'.LPU_HOST.'zapiska.php">Счета и служебные записки</a>';
      print ' | <a href="'.LPU_HOST.'stat_history.php">Статистика</a>';
      print ' | <a href="'.LPU_HOST.'buh_bank_document.php">Банковские документы</a>';
      break;
    case $GLOBALS['group_vk']:	// Журнал экспертизы
      print ' | <a href="'.LPU_HOST.'pacient_edit.php">Регистрация пациента</a>';
      print ' | <a href="'.LPU_HOST.'pacient_list.php">Список пациентов</a>';
      print ' | <a href="'.LPU_HOST.'export_journal_period.php">Экспорт журнала за период</a>';
      break;    
    case $GLOBALS['group_otd']:		// 1 Отделение
      print ' | <a href="'.LPU_HOST.'pacient_edit.php">Регистрация пациента</a>';
      break;
    case $GLOBALS['group_register']:	// Регистратура
      print ' | <a href="'.LPU_HOST.'pacient_list.php">Список пациентов</a>';
      break;
    
    case $GLOBALS['group_vahta']:	// Вахта
      //print ' -> Журнал администратора';
      break;    
    
    case $GLOBALS['group_fes']:	// Экономисты
      print '<!-- | <a href="'.LPU_HOST.'statistic_dms-1.php">Статистика по ДМС</a> -->
       | <a href="'.LPU_HOST.'dms_reestr.php">Реестр ДМС</a>
       | <a href="'.LPU_HOST.'pacient_list.php">Список пациентов</a>';
       print ' | <a href="'.LPU_HOST.'stat_history.php">Статистика</a>';
      break;
    case $GLOBALS['group_gl']:		// Глав врач
      print ' | <a href="'.LPU_HOST.'pacient_visit.php?date='.$d.'">Регистратура</a>';
      print ' | <a href="'.LPU_HOST.'vahta.php?date='.$d.'">Журнал дежурного</a>';
      print ' | <a href="'.LPU_HOST.'pacient_list.php">Список пациентов</a>
      | <a href="'.LPU_HOST.'dms_reestr.php">Реестр ДМС</a>';
      print ' | <a href="'.LPU_HOST.'stat_history.php">Статистика</a>';
      //print ' | <a href="'.LPU_HOST.'statistic_dms-1.php">Статистика по ДМС</a>';
      print ' | <a href="'.LPU_HOST.'apteka.php"><img src="'.LPU_HOST.'themes/img/apteka.png" width="16" height="16">Аптека</a>';
      //print ' | <a href="'.$sd.'tests.php?q=1">Тесты</a>';
      break;
    case $GLOBALS['group_apteka']:
      //print '| <a href="'.LPU_HOST.'apteka.php"><img src="'.LPU_HOST.'themes/img/apteka.png" width="16" height="16">Аптека</a>';
      break;
    case $GLOBALS['group_vrach']:
      print ' | <a href="'.LPU_HOST.'pacient_list.php">Спсисок пациентов</a>';
      break;
    case $GLOBALS['group_zav_otd']:
      print ' | <a href="'.LPU_HOST.'pacient_list.php">Список пациентов</a>
      | <a href="'.LPU_HOST.'pacient_edit.php">Регистрация пациента</a>
      | <a href="'.LPU_HOST.'dms_reestr.php">Реестр ДМС</a>';
      
      break;
    }

    print ' | <a href="'.LPU_HOST.'ecp.php">Электронно-цифровая подпись</a></td>';
    print '<td align="right" valign="top">';
  /*if($user->avatar == '')
    print 'Пользователь,';
    else{*/
      //print '<img width="64px" height="64px" src="'.LPU_HOST.$user->avatar.'">';

	
      print '
      <ul class="dropdown">
	<li class="dropdown-top">
	<span class="header-user-name js-header-user-name">'.$user->username;
  if($user->p_group != 0)
      print ' - '.$user->otd_name;
    elseif(($user->premission == $GLOBALS['g_vrach']) or ($user->premission == $GLOBALS['g_zav']))	// если у врача или заведующего не выбрано отделение
      print ' <a href="'.LPU_HOST.'user.php?id='.$user->id.'"><font color="red">отделение не выбрано</font></a>';
    print  '</span>
	<span class="header-user-pic b-mail-dropdown__handle" style="background-image: url('.$savimg;
     
      print ');"></span>
	  <ul class="dropdown-inside">
	    <li class="menu-tem"><a href="'.LPU_HOST.'user.php?id='.$user->id.'"><div class="gb_6 gbip" title="Фото профиля"></div></a></li>
	    <li class="menu-tem"><a href="'.LPU_HOST.'user.php?id='.$user->id.'">&nbsp;Настройки аккаунта</a></li>
	    <li class="menu-tem"><a href="'.LPU_HOST.'settings.php"><img src="'.LPU_HOST.'themes/img/s_cog.png" title="Настройки программы">&nbsp;Настройки программы</a></li>
	    <li class="menu-tem"><a href="'.LPU_HOST.'login.php?logout"><img src="'.LPU_HOST.'themes/img/s_loggoff.png">&nbsp;Выход</a></li>
	  </ul>
	</li>
      </ul>';
      //print '<div style="display">qwe</div>';
    //}
  //print '<span><b><a href="'.LPU_HOST.'user.php?id='.$user->id.'">'.$user->username.'</a>';
    
    print '</td>';
    print '</table></header>';
}

function print_search_form($q = '',$plcholder= 'Введите ФИО для поиска пациента')
{

?>
<div align="center"> <form action="" method="get" name="frm_search">
<table cellspacing="0" cellpadding="0" style="height: 27px; padding: 0px;" width="50%">
<tr>
<td style="height: 30px;" width="100px"><input type="text" spellcheck="true" name="q" placeholder="<?php echo $plcholder; ?>" style="width: 100%; outline: none; z-index: 6; border-radius: 0px; padding-left: 10px; margin: 0px; height: 20px;" value="<?php echo $q;?>"></td>
<td width="30px" height="15px"><button class="gbqfb" id="gbqfb"> <!-- name="btn_search"-->Искать</button></td>
</tr>
</table>
</form></div><br>
<?php
}

function print_username_mobile($subdir=false)
{
    $user = unserialize($_SESSION['user_class']);
    print '<a id="sidr-left" href= "#left-menu" >Toggle menu</a> 
<div id="left-menu">
  <ul>
    <li><a href="#">List 1</a></li>
    <li class="active"><a href="#">List 2</a></li>
    <li><a href="#">List 3</a></li>
  </ul>
</div>
<script>
$(document).ready(function() {
    $(\'#left-menu\').sidr({
      name: \'sidr-left\',
      side: \'left\' // By default
    });
});
</script>';
}

function print_ecp($date,$dateformat,$userid)
{
  $q = "SELECT `name`,`surname`,`otchestvo` FROM `users` WHERE `id`='{$userid}' LIMIT 1";
  $s = mysql_query($q) or die(mysql_error());
  $r = mysql_fetch_assoc($s);
 print '<br><div align="center"><div style="background: #f1feff;border-radius: 4px; border: 1px solid #8aacb3;width: 600px;"><div style="display:inline-block;vertical-align:middle;height: 50px;"><img src="'.LPU_HOST.'themes/img/ecp.png"></div><div style="display:inline-block;vertical-align:middle;left: 50px;">Электронно цифровая подпись. Документ подписан и не может быть изменен.<br>Документ подписал(а): '.convertFIO($r['surname'],$r['name'],$r['otchestvo']).'<br>
      Дата и время подписания: '.date($dateformat.' H:i',strtotime($date)).'</div></div></div>';
}

function print_ecp_button($url)
{
?>
  <div align="center"><form action="<?php echo $url;?>" method="post"><button style=""><img src="<?php echo LPU_HOST; ?>themes/img/loocked_big.png" style="margin: 2px;vertical-align: middle">Подписать ЭЦП&nbsp;&nbsp;</button></form> <a href="<?php echo LPU_HOST; ?>help/index.php?target=ecp.html" target="_blank"><img src="<?php echo LPU_HOST; ?>themes/img/b_help.png" title="Документация"></a></div>
<?php
}

/* вывод на экран панели инструментов
$btn = array(array('url'=>1,'name'=>2,'img'=>3))
  url: ссылка на действие
  namr: имя
  img: адрес картинки
  name_p: имя параметра
  value: значение параметра
  method: post, get, по умолчанию post
*/
function print_tool_bar($btn)
{

  print '<div align="center"><table border="0"><tr>';
  foreach($btn as $k=>$v)
  {
    if(isset($v['img']))
      $img = "<img src=\"".LPU_HOST."themes/img/{$v['img']}.png\">&nbsp;";
      else $img = '';
      
    if(isset($v['method']))
      $m = $v['method'];
      else $m = 'post';
    if(isset($v['value']))
        $val = "<input type=\"hidden\" name=\"{$v['name_p']}\" value=\"{$v['value']}\">";
      else $val = '';
    print "<td><form action=\"{$v['url']}\" method=\"{$m}\">{$val}<button class=\"td_button\" style=\"height: 32px;\">{$img}{$v['name']}</button></form></td>";
  }
  print '</tr></table></div>';
}

// вывести меню для статистических данных
function print_stat_menu()
{
  print '<p><a href="stat_history.php">Сведения для архива</a> | 
    <a href="'.LPU_HOST.'stat_bolnoi.php">Анализ пролеченных больных</a> | 
    <a href="'.LPU_HOST.'stat_year_amb.php">Годовые амбулаторные карты</a> | 
    <a href="'.LPU_HOST.'stat_forma14.php">Форма №14</a> |
    <a href="'.LPU_HOST.'export_journal_period.php">Журнал впервые выявленные</a></p>';
}
?>
