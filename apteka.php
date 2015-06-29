<?php
session_start();
//ini_set('session.save_path', $_SERVER['DOCUMENT_ROOT'].'/sessions');
include('mysql.php');
include('header.php');
include('functions.php');
include('class/CUser.php');
check_user();

if (isset($_SESSION['user_id']))
{

    $sc = 'function del_j(id)
        {
            if(confirm(\'Удалить препарат?\'))
                window.location = "apteka_add.php?del="+id;
        }';
    $user = unserialize($_SESSION['user_class']);
    print_header('Аптека',$sc);
    print_username(false);
  $arr_ed = array();
  $arr_ed[] = '';
  $query = "SELECT `name` FROM `apteka_ed`";
  $sql = mysql_query($query) or die(mysql_error());
  while($i = mysql_fetch_assoc($sql))
  {
    $arr_ed[] = $i['name'];
  }
  $curd = date('Y-m-d');
  $d1 = strtotime($curd);
  
  //$arr = array(1=>'1otd_apteka',2=>'2otd_apteka',3=>'3otd_apteka',4=>'ofd_apteka',5=>'fizio_apteka',6=>'rentgen_apteka',7=>'kdl_apteka',8=>'uzi_apteka',9=>'nko_apteka');
  //$arr_otd = array(1=>'1 Отделение',2=>'2 Отделение',3=>'3 отделение',4=>'ОФД',5=>'Физио',6=>'Рентген',7=>'КДЛ',8=>'УЗИ',9=>'НКО');
  print '<h2>Аптека</h2>';
  print '<p><a href="apteka_add.php">Новое поступление</a> | <a href="apteka_firma.php">Поставшики</a></p><br>';
  //----------------------------------------------------------------------------------------------------------------------------------
  //								Просмотр препаратов по отделениям
  if(isset($_GET['view_otd']))
  {
    $opend = 'open';
    $sel = $_GET['view_otd'];
    $hr = '<br><hr><br>';
    
  }
  else
  {
    $opend = '';
    $sel = '';
    $hr = '';
  }
  
  print '<details '.$opend.'><summary>Препапарты на скалде у отделений</summary>
    <form method="get" name="frm_otd_view">
      <select name="view_otd" onChange="document.frm_otd_view.submit()">
      <option selected disabled>Выбрать отделение...</option>';
     foreach($arr_otd_names as $k=>$v)
     {
      if($sel == $v)
	print '<option value="'.$k.'" selected>'.$v.'</option>';
     	else print '<option value="'.$k.'">'.$v.'</option>';
     }
     
    print '</select></form>';
   // if($opend == '')
   //   print '</details>';
  if(isset($_GET['view_otd']))
  {
 // if($_GET['view_otd'] != 0)
  {
    //$tmp = $arr[$_GET['view_otd']];
    $query = "SELECT * FROM `apteka_otd` WHERE `otd` = '{$_GET['view_otd']}' AND `count`>=1";
    $sql = mysql_query($query) or die(mysql_error());
    print '<table width="100%" cellspacing="0" border="0">
  <tr class="cap">
    <td>№</td>
    <td>Наименование</td>
    <td>Дата поступления</td>
    <td>Еденица измерения</td>
    <td width="130px">Количество</td>
    <td>Цена за еденицу</td>
    <td>Обшая сумма</td>
    <td>Срок годности</td>
  </tr>';
    $j=1;
    $tt = 0;
    while($i = mysql_fetch_assoc($sql))
    {
    if ($j % 2 == 1)
      print '<tr class="even hovr">';
      else print  '<tr class="odd hovr">';
    print '<td>'.$j.'</td>';
    print '<td>'.$i['name'].'</td>';
    print '<td>'.check_date($i['date'],$user->date_format).'</td>';
    print '<td>'.$arr_ed[$i['ed']].'</td>';
		$p = ($i['count']*100)/$i['h_count'];
	if($p == 100)
			$cl = '00ff00&to=98fb98';
		elseif($p >= 75)
			$cl = '66ff00&to=98fb98';
		elseif($p >=50)
			$cl = 'a4ff00&to=98fb98';
		elseif($p >=25)
			$cl = 'ffff00&to=98fb98';
		elseif($p >=0)
			$cl = 'ff4f00&to=98fb98';
			
    print '<td background="./themes/svg_gradient.php?from='.$cl.'">'.$i['count'].' / '.$i['h_count'].'  ('.round($p).' %)</td>';
    print '<td>'.$i['cena'].' руб. </td>';
    $t = $i['cena']*$i['count'];
    $tt = $tt+$t;
    print '<td>'.$t.' руб. </td>';
    // выделить цветом до оканчания срока годности
    $d2 = strtotime($i['date_end'])-604800; // неделя
    if($d1 > $d2)
      print '<td background="./themes/svg_gradient.php?from=ff0000&to=ffffff" title="Срок годности скоро закончиться">'.check_date($i['date_end'],$user->date_format).'</td>';
      else print '<td>'.check_date($i['date_end'],$user->date_format).'</td>';
    print '</tr>';
    $j++;
    }
    print '<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td class="hovr">Общее: '.$tt.' руб.</td>
    <td></td>
      </tr>';
    print '</table>';
    //if($opend != '')
      print '</details>';
    
  }}
 // if(isset($_GET['view_otd']))
    print '</details>';
  print $hr;
  
  //----------------------------------------------------------------------------------------------------------------------------------
  
  print '
  <p>Склад аптеки</p><table width="100%" cellspacing="0" border="0">
  <tr class="cap">
    <td>№ /<br> Накладная</td>
    <td>№<br> в базе</td>
    <td>Наименование</td>
    <td>Дата поступления</td>
    <td>Еденица измерения</td>
    <td width="130px">Остаток / Всего</td>
    <td>Цена за еденицу</td>
    <td>Обшая сумма</td>
    <td>Срок годности</td>
    <td>Операции</td>
	<td>Полка</td>
	<td>Ячейка</td>
  </tr>';
  
  $j=1;
  $tt = 0;
  //print_r($arr_ed);

  $query = "SELECT * FROM `apteka` WHERE `count` >= '1'";
  $sql = mysql_query($query) or die(mysql_error());
  while($i = mysql_fetch_assoc($sql))
  {
    if ($j % 2 == 1)
      print '<tr class="even hovr">';
      else print  '<tr class="odd hovr">';
    print "<td>{$j} / {$i['nomer_factura']}</td><td>{$i['id']}</td>";
    print '<td><a href="apteka_add.php?id='.$i['id'].'">'.$i['name'].'</a></td>';
    print '<td>'.check_date($i['date'],$user->date_format).'</td>';
    print '<td>'.$arr_ed[$i['ed']].'</td>';
	$p = ($i['count']*100)/$i['h_count'];
	if($p == 100)
			$cl = '00ff00&to=ffffff';
		elseif($p >= 75)
			$cl = '66ff00&to=ffffff';
		elseif($p >=50)
			$cl = 'a4ff00&to=ffffff';
		elseif($p >=25)
			$cl = 'ffff00&to=ffffff';
		elseif($p >=0)
			$cl = 'ff4f00&to=ffffff';
			
    print '<td background="./themes/svg_gradient.php?from='.$cl.'">'.$i['count'].' / '.$i['h_count'].'  ('.round($p).' %)</td>';
    print '<td>'.money_format("%i",$i['cena']).' руб.</td>';
    $t = $i['cena']*$i['count'];
    $tt = $tt+$t;
    //print '<td>'.$t.' руб.</td>';
    print '<td>'.money_format("%i",$t).' руб.</td>';
    // выделить цветом до оканчания срока годности
    $d2 = strtotime($i['date_end'])-604800; // неделя
    $d3 = strtotime($i['date_end']);
    if($d1 > $d2)
      print '<td background="./themes/svg_gradient.php?from=ff0000&to=ffffff" title="Срок годности скоро закончиться">'.check_date($i['date_end'],$user->date_format).'</td>';
      else print '<td>'.check_date($i['date_end'],$user->date_format).'</td>';
    print '<td><a href="apteka_traffic.php?id='.$i['id'].'"><img src="./themes/img/b_tblexport.png" title="Переместить в отеделение"></a> | <a href="#" onclick="del_j(\''.$i['id'].'\')"><img src="./themes/img/b_drop.png" title="Удалить"></a></td>';
	print '<td>'.$i['polka'].'</td><td>'.$i['icheika'].'</td>';
    print '</tr>';
    $j++;
  }
  print '<tr>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td class="hovr">Общее: '.money_format("%i",$tt).' руб. </td>
    <td></td>
      </tr>';
  print '</table>';
  $year = date("Y");
  $arr_m = get_mounth(date('m'),$year);
  //$query = "SELECT * FROM `apteka` WHERE `count` >= '1' AND `date`>='{$arr_m[0]}' AND `date`<='{$arr_m[1]}'";
  $query = "SELECT * FROM `apteka` WHERE `count` >= '1'";
  $sql = mysql_query($query) or die(mysql_error());
  $tmp=0;
  while($i = mysql_fetch_assoc($sql))
  {
    $tmp=$tmp+$i['count'];
  }
  print '<p>Остаток на текущий месяц: '.$tmp.' едениц</p>';
}
end_html();
?>