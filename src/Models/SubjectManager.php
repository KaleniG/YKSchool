<?php

namespace App\Models;

use App\Config\LogManager;
use App\Config\Model;

class SubjectManager extends Model
{
  private static $prepared = false;

  public function prepareAll()
  {
    if (SubjectManager::$prepared) return;

    pg_prepare(
      Model::getConn(),
      "get_all_subjects",
      "SELECT * FROM subjects ORDER BY id ASC"
    );

    pg_prepare(
      Model::getConn(),
      "get_teacher_subjects",
      "SELECT subject_id AS id FROM subject_teachers WHERE teacher_id=$1 ORDER BY id ASC"
    );

    pg_prepare(
      Model::getConn(),
      "update_subject",
      "UPDATE subjects SET name=$1 WHERE id=$2"
    );

    pg_prepare(
      Model::getConn(),
      "delete_subject",
      "DELETE FROM subjects WHERE id=$1"
    );

    pg_prepare(
      Model::getConn(),
      "add_subject",
      "INSERT INTO subjects (name) VALUES ($1)"
    );

    SubjectManager::$prepared = true;
  }

  public function getAllSubjects()
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "get_all_subjects",
      []
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function getTeacherSubjects($id)
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "get_teacher_subjects",
      [$id]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());

    return pg_fetch_all($result);
  }

  public function update($changes)
  {
    $this->prepareAll();

    if (!isset($changes["id"], $changes["name"]))
      LogManager::error("Invalid student update parameters");

    $id = $changes["id"];
    $changed_name = $changes["name"] ?? "";

    $result = pg_execute(
      Model::getConn(),
      "update_subject",
      [$changed_name, $id]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());
  }

  public function delete($id)
  {
    $this->prepareAll();

    if (!isset($id))
      LogManager::error("Invalid subject delete parameters");

    $result = pg_execute(
      Model::getConn(),
      "delete_subject",
      [$id]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());
  }

  public function add(Subject $subject)
  {
    $this->prepareAll();

    $result = pg_execute(
      Model::getConn(),
      "add_subject",
      [$subject->name]
    );

    if (!$result) LogManager::error("Query failed: " . Model::getError());
  }
}
