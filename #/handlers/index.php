<?php

$data['accounts'] = $db->query('select * from accounts order by name asc')
  ->fetchAll(PDO::FETCH_OBJ);

render('index', $data);