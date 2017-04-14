<?php

$accountId = isset($_GET['accountId']) ? (int)$_GET['accountId'] : 1;
$month = isset($_GET['month']) && !empty($_GET['month']) ? $_GET['month'] : date('Y-m');

$startDateTime = new DateTime($month . '-01 00:00:00');
$endDateTime = clone $startDateTime;
$endDateTime->add(new DateInterval('P1M'));

$items = [];

$q = $db->prepare("select t.*, c.name categoryName from transactions t"
  . " left join transaction_categories c on c.id=t.categoryId"
  . " where t.accountId=$accountId"
  . " and (t.dateTime>=? and t.dateTime<=?)"
  . " order by c.name asc");
$q->bindValue(1, $startDateTime->format("Y-m-d H:i:s"));
$q->bindValue(2, $endDateTime->format("Y-m-d H:i:s"));
$q->execute();

$total = 0;
while ($item = $q->fetchObject()) {  
  if (!isset($items[$item->type][$item->categoryName]))
    $items[$item->type][$item->categoryName] = 0; 
  
  $items[$item->type][$item->categoryName] += $item->amount;
  
  $total += $item->amount;
}

$accounts = $db->query('select * from accounts order by name asc')->fetchAll(PDO::FETCH_OBJ);

render('report/monthly-by-category', [
  'accounts'  => $accounts,
  'accountId' => $accountId,
  'items'     => $items,
  'month'     => $month,
]);