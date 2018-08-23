<?php
/**
 * This file is part of the O2System PHP Framework package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author         Steeve Andrian Salim
 * @copyright      Copyright (c) Steeve Andrian Salim
 */
// ------------------------------------------------------------------------

$database[ 'default' ] = [
    'driver'       => 'mysql',
    'dsn'          => '',
    'hostname'     => '127.0.0.1',
    'port'         => 3306,
    'username'     => 'root',
    'password'     => 'rf09thebye',
    'database'     => 'bonus_pulsa',
    'charset'      => 'utf8',
    'collate'      => 'utf8_general_ci',
    'tablePrefix'  => '',
    'strictOn'     => false,
    'encrypt'      => false,
    'compress'     => false,
    'buffered'     => false,
    'persistent'   => true,
    'transEnable' => false,
    'cacheEnable' => false,
    'debugEnable' => true,
];


          header("Access-Control-Allow-Origin: *");
          header("Access-Control-Allow-Methods: *");
          header('Access-Control-Allow-Credentials: true');


function connection(){
  return mysqli_connect("localhost", "root", "rf09thebye", "ujiwawasan");
}
 function sqlQuery($script){
  return mysqli_query(connection(), $script);
}

function sqlInsert($table, $data){
      if (is_array($data)) {
          $key   = array_keys($data);
          $kolom = implode(',', $key);
          $v     = array();
          for ($i = 0; $i < count($data); $i++) {
              array_push($v, "'" . $data[$key[$i]] . "'");
          }
          $values = implode(',', $v);
          $query  = "INSERT INTO $table ($kolom) VALUES ($values)";
      } else {
          $query = "INSERT INTO $table $data";
      }
      return $query;

  }

function sqlUpdate($table, $data, $where){
    if (is_array($data)) {
        $key   = array_keys($data);
        $kolom = implode(',', $key);
        $v     = array();
        for ($i = 0; $i < count($data); $i++) {
            array_push($v, $key[$i] . " = '" . $data[$key[$i]] . "'");
        }
        $values = implode(',', $v);
        $query  = "UPDATE $table SET $values WHERE $where";
    } else {
        $query = "UPDATE $table SET $data WHERE $where";
    }

   return $query;
}

function sqlArray($sqlQuery){
    return mysqli_fetch_assoc($sqlQuery);
}

function sqlRowCount($sqlQuery){
    return mysqli_num_rows($sqlQuery);
}
