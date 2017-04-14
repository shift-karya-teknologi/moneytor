<?php

require CORELIB_PATH . '/FinanceAccount.php';
require CORELIB_PATH . '/FinanceTransaction.php';

$id = isset($_REQUEST['id']) ? (int)$_REQUEST['id'] : 0;

if ($id) {
  $transaction = $db->query('select * from transactions where id=' . $id)->fetchObject(FinanceTransaction::class);
  if (!$transaction) {
    $_SESSION['FLASH_MESSAGE'] = 'Transaksi tidak ditemukan';
    header('Location: ./');
    exit;
  }
}
else {
  $transaction = new FinanceTransaction();
  $transaction->refType = '';
  $transaction->refId = '';
  $transaction->dateTime = date('Y-m-d H:i:s');
}

$categories = $db->query("select *"
  . " from transaction_categories"
  . " order by type desc, name asc")
  ->fetchAll(PDO::FETCH_OBJ);
$errors = [];

$categoryByIds = [];
foreach ($categories as $category) {
  $categoryByIds[$category->id] = $category;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = isset($_POST['action']) ? trim($_POST['action']) : '';
  if ($action === 'delete') {
    $db->beginTransaction();
    FinanceTransaction::delete($transaction->id);
    $db->commit();
    $_SESSION['FLASH_MESSAGE'] = 'Transaksi telah dihapus.';
    header('Location: ./?accountId=' . $transaction->accountId);
    exit;
  }
  else if ($action === 'save') {
    $transaction->amount = isset($_POST['amount']) ? (int) trim(str_replace('.', '', $_POST['amount'])) : 0;
    $transaction->description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $transaction->categoryId = isset($_POST['categoryId']) ? (int)$_POST['categoryId'] : 0;
    $transaction->accountId = isset($_POST['accountId']) ? (int)$_POST['accountId'] : 0;
    $transaction->dateTime = to_mysql_datetime(isset($_POST['dateTime']) ? trim((string)$_POST['dateTime']) : '');    
    
    if (empty($transaction->amount))
      $errors['amount'] = 'Jumlah uang harus diisi.';

    if (empty($transaction->description))
      $errors['description'] = 'Deskripsi harus diisi.';
    
    if (!empty($categories) && !$transaction->categoryId)
      $errors['categoryId'] = 'Pilih kategori transaksi.';

    if (empty($errors)) {
      $category = $categoryByIds[$transaction->categoryId];
      $transaction->type = $category->type;
      
      $transaction->userId = $_SESSION['CURRENT_USER']->id;
      if ($transaction->type == FinanceTransaction::Expense)
        $transaction->amount = -$transaction->amount;

      $db->beginTransaction();
      FinanceTransaction::save($transaction);
      $db->commit();

      $_SESSION['FLASH_MESSAGE'] = 'Transaksi kas disimpan.';
      header('Location: ./?accountId=' . $transaction->accountId);
      exit;
    }
  }
}

render('transaction/editor', [
  'accounts' => $db->query('select * from accounts order by name asc')->fetchAll(PDO::FETCH_OBJ),
  'transaction' => $transaction,
  'categories' => $categories,
  'errors'   => $errors,
]);
