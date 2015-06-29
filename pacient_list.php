<?php
session_start();

include('mysql.php');
include('header.php');
include('functions.php');
include('class/CUser.php');

check_user();

$mounth = array("Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");


if(isset($_SESSION['user_class']))
{
    $user = unserialize($_SESSION['user_class']);
    /*if(isset($_GET['del']))
    {
        $query_p = "DELETE FROM `pacient` WHERE `id`='{$_GET['p']}'";
        $query_d = "DELETE FROM `document` WHERE `id`='{$_GET['d']}'";
        $query_k = "DELETE FROM `karta` WHERE `id`='{$_GET['k']}'";
        
        mysql_query($query_p) or die(mysql_error());
        mysql_query($query_d) or die(mysql_error());
        mysql_query($query_k) or die(mysql_error());
        header('Location: pacient_list.php');
    }*/
    
    $iswhile = false;
    print_header("Список пациентов");
    print_username();
    
  /*if(check_access($user,array($GLOBALS['group_admin'],$GLOBALS['group_gl'],$GLOBALS['group_san_propusk'],$GLOBALS['group_register'])))
  {
    echo '<br><div align="center"><span style="color: red; font-size: 20pt;"><b>Доступ запрещен</b></span></div>';
    exit;
  }*/
  print '<h2>Список пациентов</h2>';
  //if(!check_access($user,array($GLOBALS['group_admin'],$GLOBALS['group_gl'],$GLOBALS['group_san_propusk'],$GLOBALS['group_register'])))
  //{
    print '<table>
      <tr>
	<td>
	  <a href="pacient_edit.php"><img src="'.LPU_HOST.'themes/img/new_pacient.png" style="margin: 2px;vertical-align: middle">Новый пациент</a>
	</td>
      </tr>
      </table>';

    // если производиться поиск
    if(isset($_GET['q']))
      print_search_form($_GET['q']);
      else print_search_form();


    $dm = get_mounth(date('m'),date('Y'));
    
    if(isset($_GET['dview']))
      $d = $_GET['dview'];
      else $d = date('Y-m-d');;
      
    //$queryt = "SELECT COUNT(id) FROM `pacient` WHERE `date_reg`>='{$dm[0]}' AND `date_reg`<='{$dm[1]}'";
    // получаем количество зарегестрированных за выбранный день, для формирования количество страниц
    $sqlt = mysql_query("SELECT COUNT(id) FROM `pacient` WHERE `date_reg`='{$d}'") or die(mysql_error());
    $rowt = mysql_fetch_array($sqlt);
    $next = $rowt[0];
    $per_page = $user->per_page;    // Количество записей выводимых на странице
    $num_pages = ceil($next/$per_page);
        
    // Получаем номер страницы, при перелистывании на следующую страницу
    if (isset($_GET['page']))
      $page = ($_GET['page']-1);
      else $page=0;
      $start = abs($page*$per_page);// вычисляем первый оператор для LIMIT
        
    

    // поиск
    $search_pac = array();
    if(isset($_GET['q']))
    {
        $qstr = mysql_real_escape_string($_GET['q']);
        $qstr = htmlspecialchars($qstr);
      /*if(is_numeric($_GET['q'))
      {
	$query = "SELECT * FROM `polis` ";
      }*/
        // для поиска точно неизвестной фамилии, то в начале поисковой строки и в конце ставим процент
        $search_pac = explode(' ',$qstr);
       // print_r($search_pac);
	    //foreach($search_pac as $k=>$v)
	    {
		    /*if($k == 0) {
                //if(!stripos($v,'%'))
                    $str[$k] = "`surname` LIKE '%" . $v . "%'";
                //    else $str[$k] = "`surname` = '" . $v . "'";
            }
    		if($k == 1)
    			$str[$k] = "`name` LIKE '%".$v."%'";
	    	if($k == 2)
		    	$str[$k] = "`otchestvo` LIKE '%".$v."%'";*/
            //switch(count($search_pac))
            {
                //case 0:
                    //if($v != '')
                if(count($search_pac) == 1) {
                    $str[] = "`surname` LIKE '%" . $search_pac[0] . "%'";
                    $op = ' OR ';
                }
                /*    break;
                case 1:*/
                    /*if($v != '') */
                if(count($search_pac) == 2) {
                    $str[] = "`surname` = '" . $search_pac[0] . "'";
                    $str[] = "`name` = '" . $search_pac[1] . "'";
                    $op = ' AND ';
                }
                    /*break;
                case 2:*/
                    //if($v != '')
                if(count($search_pac) == 3) {
                    $str[] = "`otchestvo` LIKE '%" . $search_pac[2] . "%'";
                    $op  = ' OR ';
                }
                    //break;
                /*
                 * если введено ФАМИЛИЯ ОТЧЕСТВО до поиск с параметром AND
                 */
            }
	    }
        $query = "SELECT * FROM `pacient` WHERE ".implode($op,$str);
        //print_r($str);
        print count($search_pac).'<br>';
        print $query.'<br>';
    } else $query = "SELECT * FROM `pacient`  WHERE `date_reg`='{$d}' LIMIT $start,$per_page";
    //$query = "SELECT * FROM `pacient` WHERE `date_reg`>='{$dm[0]}' AND `date_reg`<='{$dm[1]}' LIMIT $start,$per_page";
    //$m = date('m')-1;


    /*if(get_browser_info() != 'Chrome')	// Добавление скрипта с календарем, если исползуеьтся браузер не Chrome
      echo '<link rel="stylesheet" type="text/css" href="calendar/calendar.css"><script src="calendar/calendar.js" type="text/javascript"></script>';*/
      
    print 'Отображение за период <form action="" method="get"><input name="dview" type="date" value="'.$d.'" class="date"><input type="submit" value="Показать"></form><br><br>
    <table width="100%" cellspacing="0" border="0">';
    print '<tr  class="cap">
        <th width="50px">№</th>
        <th>ФИО</th>
        <th>Дата рождения</th>
        <!--<th>Полис ОМС</th>
        <th>Полис ДМС</th>-->
        <th>Дата регистрации</th>
        <!--<th>Амбулаторная карта</th>-->
        <th>Операции</th>';
    if($user->premission == $GLOBALS['g_admin'])
        print '<td>Дополнительная информация</td>';
    print '</tr>';
    //$query = mysql_real_escape_string("SELECT * FROM `pacient` LIMIT $start,$per_page");
    $sql = mysql_query($query) or die(mysql_error());
    $j = $start+1;
    while ($row = mysql_fetch_assoc($sql))
    {
        if ($j % 2 == 1)
        {
	  if($row['isnko'] == $user->premission)
            print '<tr class="even hovr" style="color: green;">';
            else print '<tr class="even hovr">';
        }
        else
	{
	  if($row['isnko'] == $user->premission)
	    print  '<tr class="odd hovr" style="color: green;">';
	    else print  '<tr class="odd hovr">';
	}
        
            print '<td>'.$j.'</td>
            <td><a href="pacient.php?id='.$row['id'].'">'.$row['surname'].'&nbsp;'.$row['name'].'&nbsp;'.$row['otchestvo'].'</a></td>
            <td>'.check_date($row['birdth'],$user->date_format).'</td>';
            /*$queryd = mysql_real_escape_string("SELECT * FROM `document` WHERE `id`={$row['document']}");
            $sqld = mysql_query($queryd) or die(mysql_error());
            $rowd = mysql_fetch_assoc($sqld);
            $query_karta = "SELECT * FROM `karta` WHERE `pacient_id`='{$row['id']}'";
            $sql_karta = mysql_query($query_karta) or die(mysql_error());
            $row_karta = mysql_fetch_assoc($sql_karta);*/
            
            /*$query = "SELECT `name` FROM `strahov_oms` WHERE `id`='{$rowd['polis_name']}'";
            $sql_polis = mysql_query($query) or die(mysql_error());
            $row_polis = mysql_fetch_assoc($sql_polis);
            print '<td>'.$rowd['strahovoi'].' - '.$row_polis['name'].'</td>';
            $query = "SELECT `name` FROM `strahov_oms` WHERE `id`='{$rowd['dms_firma']}'";
            $sql_polis = mysql_query($query) or die(mysql_error());
            $row_polis = mysql_fetch_assoc($sql_polis);
            
            print '<td>'.$rowd['dms_serianomer'].' - '.$row_polis['name'].'</td>';*/
            /*if($row_karta['id'] != 0)
                $str = '<a href="karta.php?id='.$row_karta['id'].'">Просмотр карты</a>';
                else $str='Карты нету';*/
            
            print '<td>'.check_date($row['date_reg'],$user->date_format).' '.date('H:i',strtotime($row['time_reg'])).'</td>';
            
            print '<td><a href="pacient.php?id='.$row['id'].'"><img src="themes/img/b_edit.png" title="Редактировать"></a>  
                <!--<a href="javascript: del_confirm(\''.$j.'\',\''.$row_karta['id'].'\',\''.$row['document'].'\',\''.$row['id'].'\')"><img src="themes/img/b_drop.png" title="Удалить"></a>--></td>';
            if($user->premission == $GLOBALS['g_admin'])
            {
                print '<td>';
                $arr_p = $GLOBALS['arr_p'];
                if($row['isnko'] != 0)
                    print $arr_p[$row['isnko']];
                $query = "SELECT `id`,`login` FROM `users` WHERE `id`='{$row['user_id']}'";
                $sql_a = mysql_query($query) or die(mysql_error());
                $row_a = mysql_fetch_assoc($sql_a);
                print '; <font color="#30661d">'.$row_a['login'].'</font>';
                print '</td>';
            }
            print '</tr>';
        $j++;
        $iswhile = true;
    }
    print '</table>';
    /*if(isset($_GET['dview']))
      $argv = '&dview='.$_GET['dview'];
      else $argv = '';*/
    print_num_pages($num_pages,$page);
    if (!$iswhile)
        print '<br><div align="center"><font color="#8B0000"><b>Нет записей</b></font></div>';
    end_html();
}
?>