<?php

namespace App\Models;

use App\Config\LogManager;
use App\Config\Model;

class SubjectManager extends Model
{
  public function getAllSubjects()
  {
    $query = "SELECT * FROM subjects";
    $result = pg_query(Model::getConn(), $query);

    if (!$result)
      LogManager::error("Query failed");

    return pg_fetch_all($result);
  }

  public function updateChanges($changes)
  {
    pg_prepare(
      Model::getConn(),
      "subjects_update",
      "UPDATE subjects SET subject=$1 WHERE id=$2"
    );
    foreach ($changes as $id => $fields) {
      $subject = htmlspecialchars($fields['subject']);
      pg_execute(Model::getConn(), "subjects_update", array($subject, $id));
    }
  }

  public function delete($id)
  {
    pg_prepare(Model::getConn(), "subjects_delete", "DELETE FROM subjects WHERE id=$1");
    pg_execute(Model::getConn(), "subjects_delete", array($id));
  }

  public function add(Subject $subject)
  {
    pg_prepare(Model::getConn(), "subjects_add", "INSERT INTO subjects (subject) VALUES ($1)");
    pg_execute(Model::getConn(), "subjects_add", array($subject->subject));
  }
}
