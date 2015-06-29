<?php
session_start();
//рипорпорпорпор
/*

   Добавление новой комиссии

*/

include('mysql.php');
include('class/CUser.php');
include('header.php');
include('functions.php');

check_user();
$isedit = false;

if (isset($_SESSION['user_id']))
{
    $user = unserialize($_SESSION['user_class']);
    if(isset($_POST['btn_ok']))
    {
        $query = "INSERT INTO `komissia` SET
            `nomer` = '{$_POST['nomer']}',
            `general` = '{$_POST['general']}',
            `visitors` = '{$_POST['visitors']}',
            `sekretar` = '{$_POST['sekretar']}',
            `date` = '{$_POST['date']}'";
        mysql_query($query) or die(mysql_error());
        $id = mysql_insert_id();
        header("Location: journal.php?id=".$_POST['nomer']);
    }

    print_header("Добавление новой комиссии");
    print_username(false);
    
    if(check_access($user,array($GLOBALS['group_admin'],$GLOBALS['group_vk'],$GLOBALS['group_gl'])))
    {
      echo '<br><div align="center"><span style="color: red; font-size: 20pt;"><b>Доступ запрещен</b></span></div>';
      exit;
    }
  
	$query = "SELECT * FROM `komissia` ORDER BY `id` DESC LIMIT 1";	// выборка из таблицы последней записи по ID
	$sql = mysql_query($query) or die(mysql_error());
	$row = mysql_fetch_assoc($sql);
	$nextk = $row['nomer']+1;
	$d = date('Y-m-d');
    print '<h2>Новая комиссии</h2>';
    
    ?>

<form action="addk.php" method="post"><table border="0">
        <tr>
            <td>Номер комиссии:</td>
            <td><input type="text" name="nomer" value="<?php echo $nextk;?>"></td>
        </tr>
        <tr>
            <td>Дата проведения:</td>
            <td><input type="date" class="date" name="date" value="<?php echo $d;?>"></td>
        </tr>
        <tr>
            <td>Председатель:</td>
            <td><input type="text" name="general" value="Матвеева Оксана Владимировна"></td>
        </tr>
        <tr>
            <td>Секретарь:</td>
            <td><input type="text" name="sekretar" value="Мальцева Ирина Витальевна"></td>
        </tr>
        <tr>
            <td>Приглашенные:</td>
            <td><input type="text" name="visitors"></td>
        </tr>
        <tr>
            <td></td>
            <td><input type="submit" value="Сохранить" name="btn_ok"></td>
        </tr>
        </table></form>';
<?php
    end_html();
}
?>