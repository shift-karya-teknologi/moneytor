<?php

/**
 * Mengenkripsi password
 * @param string $plain Password yang akan dienkripsi
 * @return string Password yang sudah terenkripsi
 */
function encrypt_password($plain) {
  return sha1($plain);
}

/**
 * Render template 
 * @param string $args[0] Nama template, lokasi relatif dari TEMPLATE_PATH
 * @param array  $args[1] Data yang akan dikirim ke template
 * @param bool   $args[2] Opsi output buffer, set true jika ingin hasil render disimpan ke buffer dan dikembalikan oleh
 *                        fungsi ini
 * @return null|string Apabila parameter ke tiga diset true maka fungsi akan mengembalikan string hasil buffer
 */
function render() {
  if (func_num_args() == 2) {
    if (is_bool(func_get_arg(1))) {
      ob_start();
    }
    else {
      extract(func_get_arg(1));
    }
  }
  else if (func_num_args() == 3 && func_get_arg(2) === true) {
    extract(func_get_arg(1));
    ob_start();
  }
  
  include TEMPLATE_PATH . DIRECTORY_SEPARATOR . func_get_arg(0) . '.phtml';
  
  if (func_num_args() == 2 && func_get_arg(1) === true || func_num_args() == 3 && func_get_arg(2) === true) {
    return ob_get_clean();
  }
}

/**
 * Fungsi untuk mengakses variabel global
 * @param string $name  Nama variabel global.
 * @param mixed $default Nilai default jika variabel tidak ditemukan.
 * @return mixed Nilai variabel atau nilai default jika nama variabel tidak ditemukan.
 */
function g($name, $default = null) {
  return isset($GLOBALS[$name]) ? $GLOBALS[$name] : $default;
}

/**
 * Shorthand untuk escape elemen atau atribut html 
 * @param string $str String yang akan di escape
 * @return string String hasil escape
 */
function e($str) {
  return htmlentities((string)$str, ENT_COMPAT | ENT_HTML5, 'UTF-8', true);
}

function str_starts_with($str, $prefix) {
  return mb_substr($str, 0, mb_strlen($prefix)) === $prefix;
}

function format_number($value, $decimal = 0) {
  return number_format((float)$value, $decimal, ',', '.');
}

function format_decimal($amount, $decimal) {
  return number_format((float)$amount, $decimal, ',', '.');
}

function format_integer($amount) {
  return number_format((float)$amount, 0, ',', '.');
}

function format_money($amount) {
  return 'Rp. ' . format_integer($amount, 0, ',', '.');
}

function format_duration($duration) {
  $a = floor($duration / 60);
  $b = $duration - ($a * 60);
  return str_pad((string)$a, 2, "0", STR_PAD_LEFT) . ':' . str_pad((string)$b, 2, "0", STR_PAD_LEFT);
}

function get_current_user_password() {
  return g('db')
    ->query('select password from users where id=' . $_SESSION['CURRENT_USER']->id)
    ->fetch(PDO::FETCH_COLUMN);
}

function finance_get_sum_amount($accountId, $dateTime) {
  $q = g('db')->prepare('select sum(amount) from finance_transactions where accountId=?'
    . 'and dateTime<=?');
  $q->bindValue(1, $accountId);
  $q->bindValue(2, $dateTime->format('Y-m-d H:i:s'));
  $q->execute();
  return $q->fetch(PDO::FETCH_COLUMN);
}

function extract_locale_date($date, $sep = '/') {
  $a = explode($sep, $date);
  return [isset($a[0]) ? $a[0] : '00', isset($a[1]) ? $a[1] : '00', isset($a[2]) ? $a[2] : '0000'];
}

function to_mysql_date($date) {
  list($dd, $mm, $yyyy) = extract_locale_date($date);
  if (!checkdate((int)$mm, (int)$dd, (int)$yyyy)) {
    return false;
  }
  return "$yyyy-$mm-$dd";
}

function to_mysql_datetime($datetime) {
  $a = explode(' ', $datetime);
  if (count($a) != 2)
    return false;
  
  list($dd, $mm, $yyyy) = extract_locale_date($a[0]);
  if (!checkdate((int)$mm, (int)$dd, (int)$yyyy)) {
    return false;
  }
  
  if (!preg_match("/(2[0-3]|[01][0-9]):([0-5][0-9])(:[0-5][0-9])?/", $a[1])) {
    return false;
  }
  
  if (strlen($a[1]) == 5)
    $a[1] .= ':00';
  
  return "$yyyy-$mm-$dd $a[1]";
}

function to_locale_date($mysql_date) {
  list($y, $m, $d) = explode('-', $mysql_date);
  return "$d/$m/$y";  
}

function to_locale_datetime($mysql_datetime) {
  list($date, $time) = explode(' ', $mysql_datetime);
  list($y, $m, $d) = explode('-', $date);
  return "$d/$m/$y $time";
}

function from_locale_number($number) {
  return (int)str_replace('.', '', $number);
}

function check_mysql_date($date)
{
    $matches = null;
    if (preg_match( '#^(?P<year>\d{2}|\d{4})([- /.])(?P<month>\d{1,2})\2(?P<day>\d{1,2})$#', $date, $matches )
           && checkdate($matches['month'],$matches['day'],$matches['year']))
      return $matches;
    return false;
}