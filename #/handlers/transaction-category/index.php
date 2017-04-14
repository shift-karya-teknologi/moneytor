<?php

$incomes = $expenses = [];

$q = $db->query("select *"
  . " from transaction_categories"
  . " order by name asc");
  
while ($item = $q->fetchObject()) {
  if ($item->type == 1)
    $incomes[] = $item;
  else if ($item->type == 2)
    $expenses[] = $item;
}

render('transaction-category/list', [
  'incomes'  => $incomes,
  'expenses' => $expenses,
]);
