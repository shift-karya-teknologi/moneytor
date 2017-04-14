<?php

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

if ($id) {
  $category = $db->query('select * from transaction_categories where id=' . $id)->fetchObject();
  if (!$category) {
    $_SESSION['FLASH_MESSAGE'] = 'Kategori tidak ditemukan';
    header('Location: ./');
    exit;
  }
}
else {
  $category = new stdClass();
  $category->id = null;
  $category->name = '';
  $category->type = 2;
}
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = isset($_POST['action']) ? (string)$_POST['action'] : 'save';
  if ($action === 'delete') {
    try {
      $db->query('delete from transaction_categories where id=' . $category->id);
    }
    catch (Exception $ex) {
      $_SESSION['FLASH_MESSAGE'] = 'Kategori tidak dapat dihapus.';
      header('Location: ?id=' . $category->id);
      exit;  
    }
    
    $_SESSION['FLASH_MESSAGE'] = 'Kategori ' . $category->name . ' telah dihapus.';
    header('Location: ./');
    exit;
  }
  
  $category->name = isset($_POST['name']) ? (string)$_POST['name'] : '';
  $category->type = isset($_POST['type']) ? (int)$_POST['type'] : 2;
  
  if (empty($category->name))
    $errors['name'] = 'Nama kategori harus diisi.';
    
  if (empty($errors)) {
    if ($category->id == 0) {
      $q = $db->prepare("select count(0) from transaction_categories where name=:name and type={$category->type}");
    }
    else {
      $q = $db->prepare("select count(0) from transaction_categories where name=:name and id<>:id and type={$category->type}");
      $q->bindValue(':id', $category->id);
    }
    $q->bindValue(':name', $category->name);
    $q->execute();
    
    if ($q->fetch(PDO::FETCH_COLUMN) > 0) {
      $errors['name'] = 'Nama kategori sudah digunakan.';
    }
    else {      
      if ($category->id == 0) {
        $q = $db->prepare('insert into transaction_categories (name, type) values(:name,:type)');
      }
      else {
        $q = $db->prepare('update transaction_categories set name=:name, type=:type where id=:id');
        $q->bindValue(':id', $category->id);
      }
      $q->bindValue(':name', $category->name);
      $q->bindValue(':type', $category->type);
      $q->execute();
      
      $_SESSION['FLASH_MESSAGE'] = 'Kategori ' . $category->name . ' telah disimpan.';
      header('Location: ./');
      exit;
    }
  }
}

render('transaction-category/editor', [
  'category' => $category,
  'errors'   => $errors,
]);
