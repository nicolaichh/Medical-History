<?php
session_start();

include('mysql.php');
include('header.php');
include('functions.php');
include('class/CUser.php');
include('mobile_detect/Mobile_Detect.php');

check_user();

function get_count_nko()
{
  $d = date('Y').'-01-01';
  $q = "SELECT COUNT(id) FROM `amb_karta` WHERE `live`='1' AND `date_start` > '{$d}'";
  $s = mysql_query($q) or die(mysql_error());
  $ar = mysql_fetch_array($s);
  return $ar[0];
}

// получение количество пациентов в отделении
function get_count_otd($p_group)
{
  $q = "SELECT COUNT(id) FROM `history` WHERE `p_group` = '{$p_group}' AND `live`='1'";
  $s = mysql_query($q) or die(mysql_error());
  $ar = mysql_fetch_array($s);
  return $ar[0];
}

if(isset($_SESSION['user_class']))
{
  $user = unserialize($_SESSION['user_class']);
  if(check_access($user,array($GLOBALS['group_admin'],$GLOBALS['group_gl'])))
  {
    header('location: '.$user->index_page);
  }
    // получаем название организации из системных настроек
  $q = mysql_query("SELECT `config_value` FROM `settings` WHERE `config_key` = 'name_litl' AND `appid` = 'rekvezit_lpu' LIMIT 1");
  $r = mysql_fetch_assoc($q);

  print_header('Клиника '.$r['config_value']);
  print_username(false);
  if(check_access($user,array($GLOBALS['group_admin'],$GLOBALS['group_gl'])))
  {
    echo "<br><div align=\"center\"><span style=\"color: red; font-size: 20pt;\"><b>Доступ запрещен</b></span></div>";
    exit;
  }
  $detect = new Mobile_Detect;
  if ( $detect->isMobile() ) {
    echo "С мобильного телефона";
  }

    print "<h2>{$r['config_value']}</h2>";

    $d = date('Y-m-d');
    $query = "SELECT COUNT(id) FROM `vahta` WHERE `date`='{$d}'";
    $sql = mysql_query($query) or die(mysql_error());
    $row = mysql_fetch_array($sql);

    $dm = get_mounth(date('m'),date('Y'));
    $query = "SELECT COUNT(id) FROM `pacient` WHERE `date_reg`>='{$dm[0]}' AND `date_reg`<='{$dm[1]}' AND `inhospital`='1'";
    $sql_p = mysql_query($query) or die(mysql_error());
    $row_p = mysql_fetch_array($sql_p);

    $query = "SELECT COUNT(id) FROM `komissia` WHERE `date`>='{$dm[0]}' AND `date`<='{$dm[1]}'";
    $sql_komis = mysql_query($query) or die(mysql_error());
    $row_komis = mysql_fetch_array($sql_komis);
  ?>
  

<div align="center">
<div align="center" style="width: 70%;">
<table width="100%" cellspacing="10" border="0" >
<tr>
<td class="menucontainer hi" width="250px" height="167px" onClick="document.location='<?php echo LPU_HOST;?>history-1.php?pgroup=1'" style="cursor:pointer;cursor:hand;"><span style="font-size: 20px;">1 отделение</span><br>Сейчас в отделении: <?php echo get_count_otd(1); ?></td>
<td class="menucontainer hi" width="250px" height="167px" onClick="document.location='<?php echo LPU_HOST;?>history-1.php?pgroup=2'" style="cursor:pointer;cursor:hand;"><span style="font-size: 20px;">2 отделение</span><br>Сейчас в отделении: <?php echo get_count_otd(2); ?></td>
<td class="menucontainer hi" width="250px" height="167px" onClick="document.location='<?php echo LPU_HOST;?>history-1.php?pgroup=3'" style="cursor:pointer;cursor:hand;"><span style="font-size: 20px;">3 отделение</span><br>Сейчас в отделении: <?php echo get_count_otd(3); ?></td>
<td class="menucontainer hi" width="250px" height="167px"><span style="font-size: 20px;">НКО</span><br>Амбулаторных карт: <?php echo get_count_nko(); ?></td>
<td class="menucontainer hi" width="250px" height="167px"><span style="font-size: 20px;">ФИЗИО</span></td>
</tr>
<tr>
<td class="menucontainer hi" width="250px" height="167px"><span style="font-size: 20px;">УЗИ</span></td>
<td class="menucontainer hi" width="250px" height="167px"><span style="font-size: 20px;">ОФД</span></td>
<td class="menucontainer hi" width="250px" height="167px"><span style="font-size: 20px;">КДЛ</span></td>
<td class="menucontainer hi" width="250px" height="167px"><span style="font-size: 20px;">РЕНТГЕН</span></td>
<td class="menucontainer hi" width="250px" height="167px" onClick="document.location='<?php echo LPU_HOST;?>vahta.php?date=<?php echo $d;?>'" style="cursor:pointer;cursor:hand;"><span style="font-size: 20px;">Журнал дежурного</span><br>Посещений сегодня: <?php echo $row[0];?></td>
</tr>
<tr>
<td class="menucontainer hi" width="250px" height="167px" onClick="document.location='<?php echo LPU_HOST;?>pacient_statistic.php'" style="cursor:pointer;cursor:hand;"><span style="font-size: 20px;">Список пациентов</span><br>Пациентов в этом месяце: <?php echo $row_p[0];?></td>
<td class="menucontainer hi" width="250px" height="167px" onClick="document.location='<?php echo LPU_HOST;?>journal_index.php'" style="cursor:pointer;cursor:hand;"><span style="font-size: 20px;">Журнал экспертизы</span><br>В этом месяце: <?php echo $row_komis[0];?></td>
<?php
    // отображение склада запчастей только администратору
    if(!check_access($user,array($GLOBALS['group_admin'])))
    {
        print '<td class="menucontainer hi" width="250px" height="167px" onclick="document.location=\''.LPU_HOST.'service_repair.php?status=3\'" style="cursor:pointer;cursor:hand;">
        <span style="font-size: 20px;">Склад запчастей</span><br>';

        $s = mysql_query("SELECT COUNT(id) FROM `service_repair` WHERE `status` = '3'") or die(mysql_error());
        $ar = mysql_fetch_array($s);
        echo 'Храниться на складе: '.$ar[0].'<br>';

        $s = mysql_query("SELECT COUNT(id) FROM `service_repair` WHERE `status` = '1'") or die(mysql_error());
        $ar = mysql_fetch_array($s);
        echo 'Ждет покупки: '.$ar[0].'<br>';

        $s = mysql_query("SELECT COUNT(id) FROM `service` WHERE `status` = '2'") or die(mysql_error());
        $ar = mysql_fetch_array($s);
        echo '<a href="'.LPU_HOST.'service.php?status=2">Техники в ремонте: '.$ar[0].'</a>';
        print '</td>';
    } else print '<td class="menucontainer hi" width="250px" height="167px"><span style="font-size: 20px;"></td>';
?>
<td class="menucontainer hi" width="250px" height="167px"></td>
<td class="menucontainer hi" width="250px" height="167px"></td>
</tr>
</table></div></div><br>

<?php
}
end_html();
?>
