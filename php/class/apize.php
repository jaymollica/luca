<?php

  class apize {

    protected $_db;

    public function __construct(PDO $db,$whitelist) {
      $this->_db = $db;
      $this->whitelist = $whitelist;

      foreach($whitelist AS $w) {
        $this->whitetables[] = $w->table;
      }
    }

    public function tables() {

      $sql = $this->_db->prepare("SHOW TABLES");
      $sql->execute();
      if($sql->rowCount() > 0) {
        $tables = $sql->fetchAll(PDO::FETCH_ASSOC);
      }

      foreach($tables AS $table) {
        $tv = array_values($table);
        if(in_array($tv[0], $this->whitetables)) {
          $t[] = $tv[0];
        }
      }

      return $t;

    }

    public function getColumnNames($table) {

      $sql = $this->_db->prepare("SHOW FULL COLUMNS FROM " . $table);
      $sql->execute();
      if($sql->rowCount() > 0) {
        $columns[$table] = $sql->fetchAll(PDO::FETCH_ASSOC);
      }

      foreach($this->whitelist AS $w) {
        if($w->table == $table) {
          $whitefields = $w->fields;
        }
      }

      foreach($columns[$table] AS $c) {

        if(in_array($c['Field'], $whitefields)) {
          $white_columns[$table][] = $c;
        }

      }

      return $white_columns;

    }

    public function getArguments($fieldArray) {

      //print '<pre>'; print_r($fieldArray); print '</pre>';

      foreach($fieldArray AS &$fa) {

        foreach($fa AS &$field) {

          $field['description'] = $this->addDescription($field);

        }

      }

      return $fieldArray;

    }

    public function addDescription($field) {

      include $_SERVER['DOCUMENT_ROOT'] . '/php/includes/field_descriptions.php';

      $parts = explode("(", $field['Type']);
      $type = $parts[0];

      if(array_key_exists($type, $fieldDescriptions)) {
        return $fieldDescriptions[$type];
      }

    }

  }

?>
