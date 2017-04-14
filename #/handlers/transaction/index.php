<?php

require CORELIB_PATH . '/FinanceAccount.php';
require CORELIB_PATH . '/FinanceTransaction.php';

if (!isset($_SESSION['FINANCE_TRANSACTION_MANAGER'])) $_SESSION['FINANCE_TRANSACTION_MANAGER'] = [];
if (!isset($_SESSION['FINANCE_TRANSACTION_MANAGER']['date'])) $_SESSION['FINANCE_TRANSACTION_MANAGER']['date'] = 'today';
if (!isset($_SESSION['FINANCE_TRANSACTION_MANAGER']['categoryId'])) $_SESSION['FINANCE_TRANSACTION_MANAGER']['categoryId'] = -1;
if (!isset($_SESSION['FINANCE_TRANSACTION_MANAGER']['accountId'])) $_SESSION['FINANCE_TRANSACTION_MANAGER']['accountId'] = -1;

$filter = [];
$filter['date'] = isset($_GET['date']) ? (string)$_GET['date'] : $_SESSION['FINANCE_TRANSACTION_MANAGER']['date'];
$filter['categoryId'] = isset($_GET['categoryId']) ? (int)$_GET['categoryId'] : $_SESSION['FINANCE_TRANSACTION_MANAGER']['categoryId'];
$filter['accountId'] = isset($_GET['accountId']) ? (int)$_GET['accountId'] : $_SESSION['FINANCE_TRANSACTION_MANAGER']['accountId'];

$_SESSION['FINANCE_TRANSACTION_MANAGER'] = $filter;

$sql = "SELECT t.*, c.name categoryName, u.username username, a.name accountName"
  . " FROM transactions t"
  . " INNER JOIN transaction_categories c on c.id=t.categoryId"
  . " INNER JOIN accounts a on a.id=t.accountId"
  . " INNER JOIN users u on u.id=t.userId";
  
$endDateTime = null;

if ($filter['date'] !== 'anytime') {
  $today = new DateTime(date('Y-m-d 00:00:00'));
  $startDateTime = clone $today;

  if ($filter['date'] === 'today') {
    $dayNum = $today->format('N');
    $endDateTime = clone $startDateTime;
    $endDateTime->add(new DateInterval('P1D'));
    $endDateTime->sub(new DateInterval('PT1S'));
  }
  else if (str_starts_with($filter['date'], '-')) {
    $dayNum = $today->format('N');
    $startDateTime->sub(new DateInterval('P' . abs($filter['date']). 'D'));
    $endDateTime = clone $startDateTime;
    $endDateTime->add(new DateInterval('P1D'));
    $endDateTime->sub(new DateInterval('PT1S'));
  }
  else if ($filter['date'] === 'thisweek') {
    $dayNum = $today->format('N');
    $startDateTime->sub(new DateInterval('P' . ($dayNum - 1) . 'D'));
    $endDateTime = clone $startDateTime;
    $endDateTime->add(new DateInterval('P6D'));
  }
  else if ($filter['date'] === 'prevweek') {
    $dayNum = $today->format('N');
    $startDateTime->sub(new DateInterval('P' . (7 + $dayNum - 1) . 'D'));
    $endDateTime = clone $startDateTime;
    $endDateTime->add(new DateInterval('P6D'));
  }
  else if ($filter['date'] === 'thismonth') {
    $startDateTime = new DateTime($today->format('Y-m-01 00:00:00'));
    $endDateTime = clone $startDateTime;
    $endDateTime->add(new DateInterval('P1M'));
    $endDateTime->sub(new DateInterval('P1D'));
  }
  else if ($filter['date'] === 'prevmonth') {
    $startDateTime = new DateTime($today->format('Y-m-01 00:00:00'));
    $startDateTime->sub(new DateInterval('P1M'));
    $endDateTime = clone $startDateTime;
    $endDateTime->add(new DateInterval('P1M'));
    $endDateTime->sub(new DateInterval('P1D'));
  }
  else {
    // bad request
    header('Location: ?date=today');
    exit;
  }
  
  $startDateTime = $startDateTime->format('Y-m-d H:i:s');
  $endDateTime = $endDateTime->format('Y-m-d H:i:s');
  
  $where[] = "(t.dateTime>='$startDateTime' and t.dateTime<='$endDateTime')";
}
if ($filter['categoryId'] > 0) {
  $where[] = "(t.categoryId={$filter['categoryId']})";
}
if ($filter['accountId'] > 0) {
  $where[] = "(t.accountId={$filter['accountId']})";
}

$where = implode(' and ', $where);
$sql .= " where $where order by t.dateTime desc";

$q = $db->query($sql);
$items = $q->fetchAll(PDO::FETCH_CLASS, FinanceTransaction::class);

$q = $db->query("select sum(amount)"
  . " from transactions"
  . " where"
  . ($filter['accountId'] > 0 ? " accountId={$filter['accountId']} and " : "")
  . " dateTime<='$endDateTime'");
$lastBalance = $q->fetchColumn();

render('transaction/index', [
  'lastBalance' => $lastBalance,
  'categories' => $db->query('select * from transaction_categories order by name asc')->fetchAll(PDO::FETCH_OBJ),
  'accounts' => $db->query('select * from accounts order by name asc')->fetchAll(PDO::FETCH_OBJ),
  'items'  => $items,
  'filter' => $filter,
]);
