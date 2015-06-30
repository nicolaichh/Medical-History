<?php
session_start();

include('mysql.php');
include('header.php');
include('functions.php');
include('class/CUser.php');
include "class/CPacient.php";

check_user();

$mounth = array("Январь","Февраль","Март","Апрель","Май","Июнь","Июль","Август","Сентябрь","Октябрь","Ноябрь","Декабрь");


if(isset($_SESSION['user_class']))
{
    $user = unserialize($_SESSION['user_class']);

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
	  <a href="'.LPU_HOST.'pacient_edit.php"><img src="'.LPU_HOST.'themes/img/new_pacient.png" style="margin: 2px;vertical-align: middle">Новый пациент</a>
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
        $query = "SELECT `id` FROM `pacient` WHERE ".implode($op,$str);
        //print_r($str);


    } else $query = "SELECT `id` FROM `pacient`  WHERE `date_reg`='{$d}' LIMIT $start,$per_page";
    //$query = "SELECT * FROM `pacient` WHERE `date_reg`>='{$dm[0]}' AND `date_reg`<='{$dm[1]}' LIMIT $start,$per_page";
    //$m = date('m')-1;


    print 'Отображение за период <form action="" method="get"><input name="dview" type="date" value="'.$d.'" class="date"><input type="submit" value="Показать"></form><br><br>
    <table width="100%" cellspacing="0" border="0">';
    print '<tr  class="cap">
        <th width="50px">№</th>
        <th>ФИО</th>
        <th>Дата рождения</th>
        <th>Дата регистрации</th>
        <th>Операции</th>';
    if(!check_access($user,array($GLOBALS['group_admin'],$GLOBALS['group_gl'])))
        print '<td>Дополнительная информация</td>';
    print '</tr>';
    $sql = mysql_query($query) or die(mysql_error());
    $j = $start+1;
    $pacient = new CPacient();
    while ($row = mysql_fetch_assoc($sql))
    {
        $pacient->load($row['id']);
        if ($j % 2 == 1)
        {
            print '<tr class="even hovr">';
        }
        else
	    {
            print  '<tr class="odd hovr">';
	    }
        $birdth = check_date($pacient->birdth,$user->date_format);
        print "<td>$j</td><td><a href=\"".LPU_HOST."pacient.php?id={$pacient->id}\">{$pacient->getPacientName()}</a></td><td>$birdth</td>";
        print '<td>'.check_date($pacient->date_reg,$user->date_format).' '.date('H:i',strtotime($pacient->time_reg)).'</td>';

        print '<td><a href="'.LPU_HOST.'pacient.php?id='.$pacient_.id.'"><img src="'.LPU_HOST.'themes/img/b_edit.png" title="Редактировать"></a></td>';

        // для администрации
        if(!check_access($user,array($GLOBALS['group_admin'],$GLOBALS['group_gl'])))
        {
            //$arr_p = $GLOBALS['arr_p'];
            $sql_a = mysql_query("SELECT `name`,`surname`,`otchestvo` FROM `users` WHERE `id`='{$pacient->user_id}' LIMIT 1") or die(mysql_error());
            $row_a = mysql_fetch_assoc($sql_a);
            print '<td><span style="color: green; ">';
            //echo convertFIO($row_a['surname'],$row_a['name'],$row_a['otchestvo']);
            echo $row_a['surname'],$row_a['name'],$row_a['otchestvo'];
            print '</span></td>';
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
        print '<br><div align="center"><span style="color: #ff0000; "><b>Нет записей</b></span></div>';
    end_html();
}
?>