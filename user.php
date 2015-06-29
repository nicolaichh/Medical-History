<?php
session_start();

include('mysql.php');
include('functions.php');
include('header.php');
include('class/CUser.php');
check_user();

// варианты отображения формата даты
$arr_d_f = array('d.m.Y'=>'д.м.Г',
		 'Y. m. d'=>'Г. м. д.',
		 'Y-m-d'=>'Г-м-д',
		 'Y/m/d'=>'Г/м/д',
		 'd-m-Y'=>'д-м-Г',
		 'd/m/Y'=>'д/м/Г',
		 'm/d/Y'=>'м/д/Г');

if(isset($_SESSION['user_class']))
{
  $user = unserialize($_SESSION['user_class']);
  // сохранение пользовательских настроек
  if(isset($_POST['btn_edit']))
  {
    $user->per_page = $_POST['per_page'];
    $user->colors = $_POST['color_theme'];
    $user->p_group = $_POST['p_group'];
    $user->date_format = $_POST['date_format'];
    $user->username = $_POST['vname'];
    $user->surname = $_POST['surname'];
    $user->otchestvo = $_POST['otchestvo'];
    $user->birdth = $_POST['birdth'];
    $user->save_settings();
    $user->get_settings();
    $_SESSION['user_class'] = serialize($user);
    header("Location: ".$user->index_page);
  }
        
  // удаление пользователя
  if(isset($_POST['del']))
  {
    $query = "DELETE FROM `users` WHERE `id`='{$_GET['del']}'";
    mysql_query($query) or die(mysql_error());
    header('Location: user.php?id='.$_GET['id'].'&mode=1');
  }

  if(isset($_GET['saveavatar']))
  {
    $uploaddir = 'files/avatars/';
    $tmp = time();
    $ext = pathinfo($uploadfile.$_FILES['avatar']['name']);
    //$uploadfile = $uploaddir.basename($_FILES['avatar']['name']);
    $uploadfile = $uploaddir.$tmp.'.'.$ext['extension'];
    if(move_uploaded_file($_FILES['avatar']['tmp_name'],$uploadfile))
    {
      //print $user->avatar;
      unlink($user->avatar);
      $file = 'files/avatars/'.$tmp.'.'.$ext['extension'];
      imageresize($file,$file,30,75);
      $user->avatar = $file;
      $user->save_settings();
      $user->get_settings();
      $_SESSION['user_class'] = serialize($user);

    }
    header('Location: user.php?id='.$_GET['id']);
   }
  $sc = '
  ';
  print_header("Профиль пользователя ".$user->username,$sc);
?>
    <script>
        function del_user(id,id_del,username)
        {
            if(confirm("Удалить пользователя "+username))
                document.location.href="<?php echo LPU_HOST;?>user.php?mode=1&id="+id+"&del="+id_del;
        }

        function change_avatar()
        {
            document.getElementById('frm_avatar').submit();
        }
    </script>
<?php
  print_username(false);
  print '<h2>Профиль: '.$user->login.'</h2>';
  //print get_browser_info();
  //echo $_SERVER['HTTP_USER_AGENT'];
  $arr = array(0=>'Светлая',1=>'Темная');
  print '
  <table border="0"><tr>
    <td>Аватарка</td>
    <td>
      <div class="gb_6 gbip"></div><br>
      <form enctype="multipart/form-data" method="post" action="user.php?saveavatar=true&id='.$user->id.'" id="frm_avatar">
      <!--<input type="hidden" name="id_user" value="'.$user->id.'">-->
      <input type="file" name="avatar" accept="image/*" onchange="change_avatar()">
      <!--<input type="submit" name="btn_save_avatar" value="Сохранить изображение">-->
      </form>
    </td>  
    </tr>';
    
  print '<form name="frm_user_settings" action="?id='.$user->id.'" method="post">
    <input type="hidden" value="'.$user->id.'" name="usr_id">
    
    <tr>
      <td><label for="id_per_page">Отображать записей на странице: </label></td>
      <td><input type="text" name="per_page" id="id_per_page" value="'.$user->per_page.'"></td>
    </tr>
    <tr>
      <td><label for="id_color_theme">Цветовая схема</label></td>
      <td><select name="color_theme">';
  foreach($arr as $k=>$v)
    if($user->colors == $k)
      print '<option value="'.$k.'" selected>'.$v.'</option>';
	else print '<option value="'.$k.'">'.$v.'</option>';
      print '</select></td>
      
    </tr>';
    

  print '<tr>
      <td>Формат даты</td>
      <td>
      <datalist id="list_date_format">';
    foreach($arr_d_f as $k=>$v)
      print '<option>'.$k.'</option>';
    print '</datalist>
    <input type="text" name="date_format" list="list_date_format" value="'.$user->date_format.'"></td></tr>
    <tr>
      <td>Имя</td>
      <td><input type="text" name="vname" value="'.$user->username.'"></td>
    </tr>
    <tr>
      <td>Фамилия</td>
      <td><input type="text" name="surname" value="'.$user->surname.'"></td>
    </tr>
    <tr>
      <td>Отчество</td>
      <td><input type="text" name="otchestvo" value="'.$user->otchestvo.'"></td>
    </tr>
    <tr>
      <td>Дата рождения</td>
      <td><input type="date" name="birdth" value="'.$user->birdth.'" class="date"></td>
    </tr>';
  
    
    // выбор отделения для врача и заведующего
    if(!check_access($user,array($GLOBALS['group_vrach'],$GLOBALS['group_gl_sister'],$GLOBALS['group_starsha_sister'],
	    $GLOBALS['group_post_sister'],$GLOBALS['group_procedur_sister'],$GLOBALS['group_zav_otd'])))
    {
        print '<tr><td>Отделение: </td><td><select name="p_group"><option value="0">Не выбрано</option>';
	    $qq = mysql_query("SELECT * FROM `p_group`");
	    while($i = mysql_fetch_assoc($qq))
	    {
	        if($user->p_group == $i['p_id'])
	            print '<option value="'.$i['p_id'].'" selected>'.$i['name'].'</option>';
	        else print '<option value="'.$i['p_id'].'">'.$i['name'].'</option>';
	    }
	    print '</select></td></tr>';
    }

    print '<tr>
      <td><input type="submit" value="Изменить" name="btn_edit"></td>
    </tr>     
    </table></form><br>';

  // установка на профилактику
  //if ($user->premission == $GLOBALS['g_admin'])
  if(!check_access($user,array($GLOBALS['group_admin'])))
  { 
     
  print '<p><a href="register.php">Добавление нового пользователя</a></p>
  <p><a href="user.php?id='.$user->id.'&mode=1">Список пользователей</a></p>
      <form name="frm_profit" method="post">
      <table border="1" cellspacing="0" width="50%">
      <tr>
	<td><input type="checkbox" name="profit" id="id_profit"><label for="id_profit">Поставить на профилактику</label></td>
      </tr>
      <tr>
	<td><input type="text" name="prichina" id="id_prichina"><label for="id_prichina">Причина профилактики</label></td>
      </tr>
      <tr>
	<td><input type="date" name="date" id="id_date" class="date"><label for="id_date">Дата проведения профелактики</label></td>
      </tr>
      <tr>
	<td><input type="submit" value="Профилактика" name="btn_profit"></td>
      </tr>
      </table></form>';
  }

  if(isset($_GET['mode']))
    if ($_GET['mode'] == 1)
    {
      echo "
      <script type=\"text/javascript\" src=\"".LPU_HOST."js/DataTables-1.10.4/media/js/jquery.js\"></script>
      <script type=\"text/javascript\" src=\"".LPU_HOST."js/DataTables-1.10.4/media/js/jquery.dataTables.js\"></script>
      <link rel=\"stylesheet\" type=\"text/css\" href=\"".LPU_HOST."js/DataTables-1.10.4/media/css/jquery.dataTables.css\">
      <script type=\"text/javascript\">
 $(document).ready(function() {
    $('#example').dataTable( {
        \"order\": [[ 3, \"desc\" ]]
    } );
} );
</script>";
      print '<table width="100%" cellspacing="0" border="0" id="example" class="display">';
      print '<thead>
                <th>№</th>
                <th>id</th>
                <th>В сети</th>
                <th>Логин</th>
                <th>IP</th>
		<th>Время</th>
                <th>Отображаемое имя</th>
                <th>Группа пользователя</th>
                <th>Операции</th>
             </thead>
             <tfoot>
		<th>№</th>
                <th>id</th>
                <th>В сети</th>
                <th>Логин</th>
                <th>IP</th>
		<th>Время</th>
                <th>Отображаемое имя</th>
                <th>Группа ползователя</th>
                <th>Операции</th>
             </tfoot>
             
             <tbody>';
      $j = 1;
      $sql = mysql_query("SELECT * FROM `users`") or die(mysql_error());
      $t = time()-300;
      while ($i = mysql_fetch_assoc($sql))
      {
	    print '<tr>';
	/*if ($j % 2 == 1)
	  print '<tr class="even hovr">';
          else print  '<tr class="odd hovr">';*/
        $sq = mysql_query("SELECT * FROM `online` WHERE `user_id`='{$i['id']}'") or die(mysql_error());
        $rq = mysql_fetch_assoc($sq);


        $ip = long2ip($rq['ip']);
        $time = date($user->date_format.' H:i', $rq['last_time']);
        if($rq['last_time']>$t)
	        $online = "<span style=\"color: green; \">да</span>&nbsp;&nbsp;";
            else $online = '&nbsp;&nbsp;';

        if(!check_access($user,array($GLOBALS['group_admin'],$GLOBALS['group_gl'])))
        {
            $ge = '<a href="user.php?mode=2&edit='.$i['id'].'"><img src="themes/img/b_edit.png" title="Редактировать"></a><a href="#" onclick="javascript: del_user(\''.$user->id.'\',\''.$i['id'].'\',\''.$i['name'].'\')"><img src="themes/img/b_drop.png" title="Удалить"></a>';
            $gu = '<span style="color: #006400; ">' .$arr_p[$i['premission']]. '</span>';
        } else {
            if ($_SESSION['user_id'] == $i['id'])
                $ge = '<a href="user.php?mode=2&edit='.$i['id'].'"><img src="themes/img/b_edit.png" title="Редактировать"></a>';
                else $ge = 'x';
            $gu = '<span style="color: black; ">' .$arr_p[$i['premission']]. '</span>';
        }

        print "<td>$j</td><td>{$i['id']}</td><td>$online</td><td>{$i['login']}</td><td>$ip</td><td>$time</td><td>{$i['name']}</td><td>$gu</td><td>$ge</td>";
        print '</tr>';
        $j++;
      }
      print '</tbody></table>';
   }
    end_html();
}
?>
