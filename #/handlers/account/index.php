<?php

$items = $db->query('select * from accounts')->fetchAll(PDO::FETCH_OBJ);

render('account/index', [
  'items' => $items,
]);