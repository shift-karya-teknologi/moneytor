<?php

class FinanceTransaction {
  const Adjustment = 0;
  const Income = 1;
  const Expense = 2;
  const Transfer = 3;
  
  public $id;
  public $type;
  public $accountId;
  public $dateTime;
  public $description;
  public $amount = 0;
  public $refId;
  public $refType;
  public $userId;
  public $categoryId;
  
  public static function findByReference($type, $id) {
    global $db;
    $q = $db->prepare("select *"
      . " from finance_transactions"
      . " where refType=? and refId=?");
    $q->bindValue(1, $type);
    $q->bindValue(2, $id);
    $q->execute();
    return $q->fetchObject(FinanceTransaction::class);
  }
  
  public static function findById($id) {
    global $db;
    $q = $db->prepare("select *"
      . " from finance_transactions"
      . " where id=?");
    $q->bindValue(1, $id);
    $q->execute();
    return $q->fetchObject(FinanceTransaction::class);    
  }
  
  public static function save(FinanceTransaction $transaction) {
    global $db;
    
    if (!$transaction->id) {
      $q = $db->prepare('insert into transactions'
        . ' ( type, accountId, description, amount, dateTime,'
        . '   userId, categoryId)'
        . ' values'
        . ' (:type,:accountId,:description,:amount,:dateTime,'
        . '  :userId,:categoryId)'
      );
    }
    else {
      $q = $db->prepare('update transactions set'
        . ' type=:type, accountId=:accountId, description=:description, amount=:amount, dateTime=:dateTime,'
        . ' userId=:userId, categoryId=:categoryId'
        . ' where id=' . (int)$transaction->id
      );
    }
        
    $q->bindValue(':type', $transaction->type);
    $q->bindValue(':accountId', $transaction->accountId);
    $q->bindValue(':description', $transaction->description);
    $q->bindValue(':amount', $transaction->amount);
    $q->bindValue(':dateTime', $transaction->dateTime);
    $q->bindValue(':userId', $transaction->userId);
    $q->bindValue(':categoryId', $transaction->categoryId ? (int)$transaction->categoryId : null);
    $q->execute();
    
    if (!$transaction->id)
      $transaction->id = $db->lastInsertId();
    
    return $q->rowCount();
  }
  
  public static function delete($id) {
    global $db;
    $id = (int)$id;
    $db->query("delete from transactions where id=$id");
  }
}


