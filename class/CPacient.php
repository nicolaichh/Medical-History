<?php

class CPacient
{
  public $id;
  public $name;
  public $surname;
  public $otchestvo;
  //public $document_id;
  public $birdth;
  public $gender;
  public $date_reg;
  public $date_end;
  public $time_reg;
  public $snils;
  public $inn;
  public $telephone;
  public $isnko;
  public $user_id;
  //public $docuemnt;
  public $home_telephone;
  public $social;	// соиальный статус
  public $village;	// постоянное местожительства
  public $job;	// место работы
  public $prof;	// профессия
  public $job_telephone;	// рабочий телефон
  public $prof_group;	// группа профессии
  public $obl;
  public $city;
  public $street;
  public $home;
  public $kvartira;
  public $uid;	// Уникальный идентификатор пациента
  
  
  // генерация уникального значения
  function generate_uid()
  {
    $s = '123456789';
    $str = '';
    for($i=0;$i<12;$i++)
      $str .= $s{rand(0,9)};
    return $str;
  }
  
  public function save()
  {
    $snils = str_replace(' ','',$this->snils);
    $snils = str_replace('-','',$snils);
    //$prof = str_replace('"','&quot;',$this->prof);
    //$prof = str_replace('\\','',$prof);
    $prof = mysql_escape_string($this->prof);
    
    // генерация уникального значения
    //$s = '0123456789';
    //$str = '';
    //for($i=0;$i<12;$i++)
    //  $str .= $s{rand(0,9)};
    //----------------
    $str = $this->generate_uid();
    $q = mysql_query("SELECT `uid` FROM `pacient` WHERE `uid`='{$str}'") or die(mysql_error());
    while(mysql_num_rows($q)>0)	// ищем в базе совпадение нашего числа
    {
      $str = $this->generate_uid();
      $q = mysql_query("SELECT `uid` FROM `pacient` WHERE `uid`='{$str}'") or die(mysql_error());
    }
    $this->uid = $str;
    
    $query = "INSERT `pacient` SET
	`name` = '{$this->name}',
	`surname` = '{$this->surname}',
	`otchestvo` = '{$this->otchestvo}',
	`birdth` = '{$this->birdth}',
    `gender` = '{$this->gender}',
    `date_reg` = '{$this->date_reg}',
    `time_reg` = '{$this->time_reg}',
    `snils` = '{$snils}',
    `inn` = '{$this->inn}',
	`date_end` = '{$this->date_end}',
	`telephone` = '{$this->telephone}',
	`home_telephone` = '{$this->home_telephone}',
    `inhospital` = '1',
    `social` = '{$this->social}',
    `isnko` = '{$this->isnko}',
    `village` = '{$this->village}',
    `job` = '{$this->job}',
    `prof` = '{$prof}',
    `job_telephone` = '{$this->job_telephone}',
    `prof_group` = '{$this->prof_group}',
    `user_id` = '{$this->user_id}',
    `obl` = '{$this->obl}',
    `city` = '{$this->city}',
    `street` = '{$this->street}',
    `home` = '{$this->home}',
    `kvartira` = '{$this->kvartira}',
    `uid` = '{$this->uid}'";
    mysql_query($query) or die(mysql_error());
    $this->id = mysql_insert_id();
  }
  
  public function update($id)
  {
    $this->id = $id;
    $snils = str_replace(' ','',$this->snils);
    $snils = str_replace('-','',$snils);
    
    //$prof = str_replace('"','&quot;',$this->prof);
    //$prof = str_replace('\\','',$prof);
    
    $prof = mysql_real_escape_string($this->prof);
    $query = "UPDATE `pacient` SET
      `name` = '{$this->name}',
      `surname` = '{$this->surname}',
      `otchestvo` = '{$this->otchestvo}',
      `birdth` = '{$this->birdth}',
      `gender` = '{$this->gender}',
      `snils` = '{$snils}',
      `inn` = '{$this->inn}',
      `date_end` = '{$this->date_end}',
      `telephone` = '{$this->telephone}',
      `social` = '{$this->social}',
      `village` = '{$this->village}',
      `job` = '{$this->job}',
      `prof` = '{$prof}',
      `job_telephone` = '{$this->job_telephone}',
      `prof_group` = '{$this->prof_group}',
      `home_telephone` = '{$this->home_telephone}',
      `obl` = '{$this->obl}',
      `city` = '{$this->city}',
      `street` = '{$this->street}',
      `home` = '{$this->home}',
      `kvartira` = '{$this->kvartira}' WHERE `id` = '{$id}'";
    mysql_query($query) or die(mysql_error());
  }
  
  public function getPacientName()
  {
    return $this->surname.' '.$this->name.' '.$this->otchestvo;
  }

    // получить имя отчество пациента
  public function convertFIO()
  {
      $result = $this->surname;
      $result .= substr($this->name,0,1).'.';
      $result .= substr($this->otchestvo,0,1).'.';
      return $result;
  }
  
  // вычислить возраст
  // @ $date = до какой даты вычислять, по умолчанию текущая дата
  public function getAge($date = '')
  {
    if($date == '')
      $date = date('Y-m-d');
    $sec = 0;
    $min = 0;
    $hour = 0;
    $b = explode('-',$this->birdth);
    $birthdate_unix = mktime($hour, $min, $sec, $b[1], $b[2], $b[0]);
    $current_unix = strtotime($date);
    $period_unix=$current_unix - $birthdate_unix;
    return floor($period_unix / (365*24*60*60));
  }
  
  public function load($id)
  {
    $query = "SELECT * FROM `pacient` WHERE `id`='{$id}' LIMIT 1";
    $sql = mysql_query($query) or die(mysql_error());
    $row = mysql_fetch_assoc($sql);
    $this->id = $id;
    $this->name = $row['name'];
    $this->surname = $row['surname'];
    $this->otchestvo = $row['otchestvo'];
    $this->birdth = $row['birdth'];
    $this->gender = $row['gender'];
    $this->date_reg = $row['date_reg'];
    $this->date_end = $row['date_end'];
    $this->time_reg = $row['time_reg'];
    $this->snils = $row['snils'];
    $this->inn = $row['inn'];
    $this->telephone = $row['telephone'];
    $this->home_telephone = $row['home_telephone'];
    $this->isnko = $row['isnko'];
    $this->user_id = $row['user_id'];
    $this->social = $row['social'];
    $this->village = $row['village'];
    $this->job = $row['job'];
    $this->prof = $row['prof'];
    $this->job_telephone = $row['job_telephone'];
    $this->prof_group = $row['prof_group'];
    $this->obl = $row['obl'];
    $this->city = $row['city'];
    $this->street = $row['street'];
    $this->home = $row['home'];
    $this->kvartira = $row['kvartira'];
    $this->uid = $row['uid'];
    //$this->document->load($row['document']);
  }
  
  function __construct()
  {
    $this->gender=0;
    $this->birdth = '0000-00-00';
    $this->obl = 'Кемеровская обл.';
    $this->uid = 'генерируеться автоматически';
    //$this->document = new CDocument;
  }
}

?>