<?php
include "config.php";
class syncClass extends Config{
  var $arrayFixIndexTable = array();
  var $blockListExtension = array(
      array('.bck'),
      array('bck'),
      array(',bck'),
      array('.BCK'),
      array('.backup'),
      array('.201'),
      array('201'),
      array('.php_'),
      array('.php_'),
      array('.bck'),
      array('.git'),
      array('_DOC'),
      array('.doc'),
      array('.xls'),
  );
  function __construct(){
    $options = getopt(null, array(
      "type:",
      "databaseFile:",
      "date:",
      "action:",
      "tableName:",
      "viewName:",
      "defaultValue:",
      "triggerName:",
      "routineName:",
      "dirName:",
      "fileName:",
      "checkResult:",

    ));
    foreach ($options as $key => $value) {
       $$key = $value;
    }
    switch ($type) {
      case 'showStruktur':
        echo  $this->showStruktur($tableName,$dirName);
        break;
      case 'showView':
        echo  $this->showView($viewName,$dirName);
        break;
      case 'showTrigger':
        echo  $this->showTrigger($triggerName,$dirName);
        break;
      case 'showRoutine':
        echo  $this->showRoutine($routineName,$dirName);
        break;
      case 'checkStruktur':
        echo  $this->checkStruktur($fileName,$checkResult);
        break;
      case 'checkView':
        echo  $this->checkView($fileName,$checkResult);
        break;
      case 'checkTrigger':
        echo  $this->checkTrigger($fileName,$checkResult);
        break;
      case 'checkRoutine':
        echo  $this->checkRoutine($fileName,$checkResult);
        break;
      case 'fixStruktur':
        echo  $this->fixStruktur($fileName,$checkResult);
        break;
      case 'fixView':
        echo  $this->fixView($fileName,$checkResult);
        break;
      case 'fixTrigger':
        echo  $this->fixTrigger($fileName,$checkResult);
        break;
      case 'fixRoutine':
        echo  $this->fixRoutine($fileName,$checkResult);
        break;
      case 'dumpTable':
        echo $this->dumpTable($tableName);
        break;
      default:
        echo "ATIS INSTALLER ";
        break;
    }

  }
  function filterExtension($word){
      $result = 0;
      for ($i=0; $i < sizeof($this->blockListExtension); $i++) {
        if(strpos($word, $this->blockListExtension[$i][0]) !== false){
          $result += 1;
        }
      }
      return $result;
  }

  function showStruktur($tableName,$dirName){
    $arrayTableStruktur = array();
    if($tableName == "*"){
      $tableName = $this->listTable();
    }
    $explodeTableName = explode(",",$tableName);
    for ($i=0; $i < sizeof($explodeTableName); $i++) {
      if($this->filterExtension($explodeTableName[$i]) == 0 && $this->sqlRowCount($this->sqlQuery("SHOW CREATE VIEW ".$explodeTableName[$i])) == 0){
        $arrayStuktur = array();
        $getTableStrukTur = $this->sqlQuery("select TABLE_NAME,COLUMN_NAME,COLUMN_TYPE,CHARACTER_SET_NAME,COLLATION_NAME,IS_NULLABLE,COLUMN_DEFAULT from information_schema.COLUMNS where TABLE_SCHEMA= '$this->databaseName' and TABLE_NAME = '".$explodeTableName[$i]."'");
         while ($dataTableStruktur = $this->sqlArray($getTableStrukTur)) {
           $arrayStuktur[] = array(
             "COLUMN_NAME" => $dataTableStruktur['COLUMN_NAME'],
             "COLUMN_TYPE" => $dataTableStruktur['COLUMN_TYPE'],
             "CHARACTER_SET_NAME" => $dataTableStruktur['CHARACTER_SET_NAME'],
             "COLLATION_NAME" => $dataTableStruktur['COLLATION_NAME'],
             "IS_NULLABLE" => $dataTableStruktur['IS_NULLABLE'],
           );
         }
         $arrayIdexTable = array();
         $getIndexTable = $this->sqlQuery("SHOW INDEX FROM $this->databaseName.".$explodeTableName[$i]."");
         while ($dataIndex = $this->sqlArray($getIndexTable)) {
           $arrayIndexTable[] = array(
             "column_name" => $dataIndex["Column_name"],
             "key_name" => $dataIndex["Key_name"],
             "non_uniqe" => $dataIndex["Non_unique"],
           );
         }
         $arrayTableStruktur[] = array(
           "tableName" => $explodeTableName[$i],
           "index" => $arrayIndexTable,
           "colums" => $arrayStuktur
         );
         $options = getopt(null, array(
           "defaultValue:",
         ));
         foreach ($options as $key => $value) {
            $$key = $value;
         }
         if($defaultValue == "true"){
          $arrayDefaultValue = array();
          $getDataInsert =$this->sqlQuery("select * from ".$explodeTableName[$i]."");
           while ($dataInsert = $this->sqlArray($getDataInsert)) {
             $arrayKolomTable = array();
             $arrayValue = array();
             for ($iDefaultValue=0; $iDefaultValue < sizeof($arrayStuktur) ; $iDefaultValue++) {
               $arrayKolomTable[] = $arrayStuktur[$iDefaultValue]["COLUMN_NAME"];
               $arrayValue[] = "'".$dataInsert[$arrayStuktur[$iDefaultValue]["COLUMN_NAME"]]."'";
             }
             $arrayDefaultValue[] = "INSERT INTO ".$explodeTableName[$i]." (".implode(",",$arrayKolomTable).") values (".implode(",",$arrayValue).") ";
           }
         }
         $textJson = json_encode(array(
           "tableName" => $explodeTableName[$i],
           "dumpFile" =>$this->dumpTable($explodeTableName[$i]),
           "index" => $arrayIndexTable,
           "colums" => $arrayStuktur,
           "default_value" => $arrayDefaultValue
         ),JSON_PRETTY_PRINT);

         file_put_contents( $dirName."/".$explodeTableName[$i].".json", $textJson );
      }
    }

    return json_encode($arrayTableStruktur,JSON_PRETTY_PRINT);
  }
  function dumpTable($tableName) {
    $dumpString = shell_exec("mysqldump -u".$this->userMysql." -p".$this->passwordMysql." --no-data --skip-events --skip-routines --skip-triggers --skip-add-drop-table $this->databaseName $tableName");
    file_put_contents("dump/$tableName".".sql",$dumpString);
    return "dump/$tableName".".sql";
  }
  function showTrigger($triggerName,$dirName){
    $arrayTableStruktur = array();
    if($triggerName == "*"){
      $triggerName = $this->listTrigger();
    }
    $explodeTriggerName = explode(",",$triggerName);
    for ($i=0; $i < sizeof($explodeTriggerName); $i++) {
      if($this->filterExtension($explodeTriggerName[$i]) == 0){
        $arrayStuktur = array();
        $getDataTrigger = $this->sqlArray($this->sqlQuery("select TRIGGER_NAME,HEX(ACTION_STATEMENT),ACTION_TIMING,EVENT_MANIPULATION,EVENT_OBJECT_TABLE  from information_schema.TRIGGERS where TRIGGER_SCHEMA= '$this->databaseName' and TRIGGER_NAME = '".$explodeTriggerName[$i]."'"));
        $arrayTrigger[] = array(
          "TRIGGER_NAME" => $getDataTrigger['TRIGGER_NAME'],
          "ACTION_STATEMENT" => $getDataTrigger['HEX(ACTION_STATEMENT)'],
          "ACTION_TIMING" => $getDataTrigger['ACTION_TIMING'],
          "EVENT_MANIPULATION" => $getDataTrigger['EVENT_MANIPULATION'],
          "EVENT_OBJECT_TABLE" => $getDataTrigger['EVENT_OBJECT_TABLE'],
        );

         file_put_contents($dirName."/".$explodeTriggerName[$i].".json",json_encode(array(
           "TRIGGER_NAME" => $getDataTrigger['TRIGGER_NAME'],
           "ACTION_STATEMENT" => $getDataTrigger['HEX(ACTION_STATEMENT)'],
           "ACTION_TIMING" => $getDataTrigger['ACTION_TIMING'],
           "EVENT_MANIPULATION" => $getDataTrigger['EVENT_MANIPULATION'],
           "EVENT_OBJECT_TABLE" => $getDataTrigger['EVENT_OBJECT_TABLE'],
         ),JSON_PRETTY_PRINT));
      }
    }

    return json_encode($arrayTrigger,JSON_PRETTY_PRINT);
  }
  function showRoutine($routineName,$dirName){
    $arrayTableStruktur = array();
    if($routineName == "*"){
      $routineName = $this->listRoutine();
    }
    $explodeRoutineName = explode(",",$routineName);
    for ($i=0; $i < sizeof($explodeRoutineName); $i++) {
      if($this->filterExtension($explodeRoutineName[$i]) == 0){
        $arrayParameter = array();
        $getDataRoutine = $this->sqlArray($this->sqlQuery("select ROUTINE_NAME,HEX(ROUTINE_DEFINITION),ROUTINE_TYPE  from information_schema.ROUTINES where ROUTINE_SCHEMA= '$this->databaseName' and ROUTINE_NAME = '".$explodeRoutineName[$i]."'"));
        $getDataParameters = $this->sqlQuery("select PARAMETER_NAME,DTD_IDENTIFIER,ORDINAL_POSITION,CHARACTER_SET_NAME,COLLATION_NAME from information_schema.PARAMETERS where SPECIFIC_SCHEMA = '$this->databaseName' and SPECIFIC_NAME  = '".$explodeRoutineName[$i]."' order by ORDINAL_POSITION");
        while ($dataParameter = $this->sqlArray($getDataParameters)) {
          $arrayParameter[] = array(
            "PARAMETER_NAME" => $dataParameter['PARAMETER_NAME'],
            "DTD_IDENTIFIER" => $dataParameter['DTD_IDENTIFIER'],
            "ORDINAL_POSITION" => $dataParameter['ORDINAL_POSITION'],
            "CHARACTER_SET_NAME" => $dataParameter['CHARACTER_SET_NAME'],
            "COLLATION_NAME" => $dataParameter['COLLATION_NAME'],
          );
        }
        $arrayRoutine[] = array(
          "ROUTINE_NAME" => $getDataRoutine['ROUTINE_NAME'],
          "ROUTINE_DEFINITION" => $getDataRoutine['HEX(ROUTINE_DEFINITION)'],
          "ROUTINE_TYPE" => $getDataRoutine['ROUTINE_TYPE'],
          "PARAMETERS" => $arrayParameter,
        );

         file_put_contents($dirName."/".$explodeRoutineName[$i].".json",json_encode(array(
           "ROUTINE_NAME" => $getDataRoutine['ROUTINE_NAME'],
           "ROUTINE_DEFINITION" => $getDataRoutine['HEX(ROUTINE_DEFINITION)'],
           "ROUTINE_TYPE" => $getDataRoutine['ROUTINE_TYPE'],
           "PARAMETERS" => $arrayParameter,
         ),JSON_PRETTY_PRINT));
      }
    }

    return json_encode($arrayRoutine,JSON_PRETTY_PRINT);
  }
  function showView($viewName,$dirName){
    $arrayTableStruktur = array();
    if($viewName == "*"){
      $viewName = $this->listView();
    }
    $explodeViewName = explode(",",$viewName);
    for ($i=0; $i < sizeof($explodeViewName); $i++) {
      if($this->filterExtension($explodeViewName[$i]) == 0){
        $arrayParameter = array();
        $getDataView = $this->sqlArray($this->sqlQuery("SHOW CREATE VIEW ".$explodeViewName[$i]." "));
        $arrayView[] = array(
          "VIEW_NAME" => $getDataView['View'],
          "VIEW_SOURCE" => $getDataView['Create View'],
          "CHARSET" => $getDataView['character_set_client'],
          "COLLATION" => $getDataView['collation_connection'],
        );

         file_put_contents($dirName."/".$explodeViewName[$i].".json",json_encode(array(
           "VIEW_NAME" => $getDataView['View'],
           "VIEW_SOURCE" => $getDataView['Create View'],
           "CHARSET" => $getDataView['character_set_client'],
           "COLLATION" => $getDataView['collation_connection'],
         ),JSON_PRETTY_PRINT));
      }
    }

    return json_encode($arrayView,JSON_PRETTY_PRINT);
  }

  function checkStruktur($fileName,$checkResult){
    $jsonFile = file_get_contents($fileName);
    $decodeJsonFile = json_decode($jsonFile);
    $databaseName = $decodeJsonFile[0]->databaseName;
    $configDatabase = $this->setConfig();
    $this->connnectionCheckDB = mysqli_connect("localhost",$configDatabase["USER"], $configDatabase["PASSWORD"], $databaseName);
    $arrayTable = $decodeJsonFile[0]->tables;
    for ($i=0; $i < sizeof($arrayTable); $i++) {
      $arrayKolomDatabase = array();
      $arrayKolomCheckTable = array();
      $arrayStrukturTableFix = json_decode(file_get_contents($arrayTable[$i]->json_file));
      $arrayStruktur = $this->getStruktur($databaseName,$arrayTable[$i]->table_name);
      $arrayIndexTable = $this->getIndexTable($databaseName,$arrayTable[$i]->table_name);
      if($this->sqlRowCount($this->sqlQueryCheckDB("SHOW TABLES like '".$arrayStrukturTableFix->tableName."'")) != 0){
        for ($a=0; $a < sizeof($arrayStrukturTableFix->colums); $a++) {
          if(array_search($arrayStrukturTableFix->colums[$a]->COLUMN_NAME, array_column($arrayStruktur, 'COLUMN_NAME')) !== false){
            $statusCheckColomn = " OK ";
            // $keyIndex = array_search($arrayStrukturTableFix->colums[$a]->COLUMN_NAME, array_column($arrayStruktur, 'COLUMN_NAME'));
            $columnNameFix = $arrayStrukturTableFix->colums[$a]->COLUMN_NAME;
            $typeDataFix = $arrayStrukturTableFix->colums[$a]->COLUMN_TYPE;
            $charsetFix = $arrayStrukturTableFix->colums[$a]->CHARACTER_SET_NAME;
            $colattionFix = $arrayStrukturTableFix->colums[$a]->COLLATION_NAME;
            $isNUllAbleFix = $arrayStrukturTableFix->colums[$a]->IS_NULLABLE;
            $columnNameSource = $arrayStruktur[$a]['COLUMN_NAME'];
            $typeDataSource = $arrayStruktur[$a]['COLUMN_TYPE'];
            $charsetSource = $arrayStruktur[$a]['CHARACTER_SET_NAME'];
            $colattionSource = $arrayStruktur[$a]['COLLATION_NAME'];
            $isNUllAbleSource = $arrayStruktur[$a]['IS_NULLABLE'];
            $error = array();

            if($typeDataFix != $typeDataSource){
               $error[]= "TYPE DATA DIFFERENT";
            }
            if($charsetFix != $charsetSource){
               $error[]= "CHARSET DIFFERENT";
            }
            if($colattionFix != $colattionSource){
               $error[]= "COLLATION DIFFERENT";
            }
            if($isNUllAbleFix != $isNUllAbleSource){
               $error[]= "NULL ABLE DIFFERENT";
            }
            if(sizeof($error) !=0){
              $statusCheckColomn = implode(", ",$error);
            }
          }else{
            $statusCheckColomn = " ERROR COLUMN NOT EXIST ";
          }
          $arrayKolomCheckTable[] =  $arrayStrukturTableFix->colums[$a]->COLUMN_NAME." => $statusCheckColomn";
        }
        $listCheckColomn= "";
        for ($zx=0; $zx < sizeof($arrayKolomCheckTable); $zx++) {
          $listCheckColomn .="\n \t\t- ".$arrayKolomCheckTable[$zx];
        }
        $arrayKolomCheckIndex = array();
        for ($abc=0; $abc < sizeof($arrayStrukturTableFix->index); $abc++) {
          if(array_search($arrayStrukturTableFix->index[$abc]->column_name, array_column($arrayIndexTable, 'column_name')) !== false){
            $statusCheckIndex = " OK ";
            $nameColumnFix = $arrayStrukturTableFix->index[$abc]->column_name;
            $keyNameFix = $arrayStrukturTableFix->index[$abc]->key_name;
            $nonUniqueFix = $arrayStrukturTableFix->index[$abc]->non_uniqe;
            $nameColumnSource = $arrayIndexTable[$abc]['column_name'];
            $keyNameSource = $arrayIndexTable[$abc]['key_name'];
            $nonUniqueSource = $arrayIndexTable[$abc]['non_uniqe'];
            $error = array();
            if($keyNameFix != $keyNameSource){
              $error[] = "KEY NAME DIFFERENT";
            }
            if($nonUniqueFix != $nonUniqueSource){
              $error[] = "NON UNIQUE DIFFERENT";
            }
            if(sizeof($error)){
              $statusCheckIndex = implode(", ",$error);
            }
          }else{
            $statusCheckIndex = "ERROR INDEX NOT FOUND  ";
          }
          $listCheckIndex .= "\n \t \t INDEX => ". $arrayStrukturTableFix->index[$abc]->column_name."  $statusCheckIndex";
        }
        $namaTable .= $arrayTable[$i]->table_name." =>  ".$arrayTable[$i]->json_file." $listCheckColomn $listCheckIndex
         \n";
      }else{
        $namaTable.= $arrayTable[$i]->table_name." => TABLE NOT EXITS ";
      }


    }

    return $namaTable;
  }
  function checkTrigger($fileName,$checkResult){
    $jsonFile = file_get_contents($fileName);
    $decodeJsonFile = json_decode($jsonFile);
    $databaseName = $decodeJsonFile[0]->databaseName;
    $configDatabase = $this->setConfig();
    $this->connnectionCheckDB = mysqli_connect("localhost",$configDatabase["USER"], $configDatabase["PASSWORD"], $databaseName);
    $arrayTrigger = $decodeJsonFile[0]->triggers;
    for ($i=0; $i < sizeof($arrayTrigger); $i++) {
      $arrayError = array();
      $arrayTriggerFix = json_decode(file_get_contents($arrayTrigger[$i]->json_file));
      $arrayTriggerSource = $this->getTrigger($databaseName,$arrayTriggerFix->TRIGGER_NAME);
      if($this->sqlRowCount($this->sqlQueryCheckDB("select TRIGGER_NAME,HEX(ACTION_STATEMENT),ACTION_TIMING,EVENT_MANIPULATION,EVENT_OBJECT_TABLE  from information_schema.TRIGGERS where TRIGGER_SCHEMA= '$databaseName' and TRIGGER_NAME = '".$arrayTriggerFix->TRIGGER_NAME."'")) != 0){
        $triggerNameFix = $arrayTriggerFix->TRIGGER_NAME;
        $hexActionStatementFix = $arrayTriggerFix->ACTION_STATEMENT;
        $actionTimingFix = $arrayTriggerFix->ACTION_TIMING;
        $eventManipulationFix = $arrayTriggerFix->EVENT_MANIPULATION;
        $eventObjectTableFix = $arrayTriggerFix->EVENT_OBJECT_TABLE;
        $triggerNameSource = $arrayTriggerSource['TRIGGER_NAME'];
        $hexActionStatementSource = $arrayTriggerSource['ACTION_STATEMENT'];
        $actionTimingSource = $arrayTriggerSource['ACTION_TIMING'];
        $eventManipulationSource = $arrayTriggerSource['EVENT_MANIPULATION'];
        $eventObjectTableSource = $arrayTriggerSource['EVENT_OBJECT_TABLE'];
        if($hexActionStatementSource != $hexActionStatementFix){
          $arrayError[] = "Source TRIGGER DIFFERENT ";
        }
        if($actionTimingSource != $actionTimingFix){
          $arrayError[] = "ACTION TIMMING TRIGGER DIFFERENT ";
        }
        if($eventManipulationFix != $eventManipulationSource){
          $arrayError[] = "EVENT MANIPULATION TRIGGER DIFFERENT ";
        }
        if($eventObjectTableSource != $eventObjectTableFix){
          $arrayError[] = "EVENT OBJECT TABLE TRIGGER DIFFERENT ";
        }
        if(sizeof($arrayError) !=0){
          $err .= $arrayTriggerFix->TRIGGER_NAME." => ".implode(", ",$arrayError)." \n";
        }else{
          $err .= $arrayTriggerFix->TRIGGER_NAME." => "."OK \n";
        }
      }else{
        $err .= $arrayTriggerFix->TRIGGER_NAME." => TRIGGER NOT EXITS \n";
      }
    }
    return $err;
  }
  function checkView($fileName,$checkResult){
    $jsonFile = file_get_contents($fileName);
    $decodeJsonFile = json_decode($jsonFile);
    $databaseName = $decodeJsonFile[0]->databaseName;
    $configDatabase = $this->setConfig();
    $this->connnectionCheckDB = mysqli_connect("localhost",$configDatabase["USER"], $configDatabase["PASSWORD"], $databaseName);
    $arrayView = $decodeJsonFile[0]->views;
    for ($i=0; $i < sizeof($arrayView); $i++) {
      $arrayError = array();
      $arrayViewFix = json_decode(file_get_contents($arrayView[$i]->json_file));
      $arrayViewSource = $this->getView($databaseName,$arrayViewFix->VIEW_NAME);
      if($this->sqlRowCount($this->sqlQueryCheckDB("SHOW CREATE VIEW ".$arrayViewFix->VIEW_NAME." ")) != 0){
        $viewNameFix = $arrayViewFix->VIEW_NAME;
        $viewLogicFix = $arrayViewFix->VIEW_SOURCE;
        $viewCharsetFix = $arrayViewFix->CHARSET;
        $viewCollationFix = $arrayViewFix->COLLATION;

        $viewNameSource = $arrayViewSource['VIEW_NAME'];
        $viewLogicSource = $arrayViewSource['VIEW_SOURCE'];
        $viewCharsetSource = $arrayViewSource['CHARSET'];
        $viewCollationSource = $arrayViewSource['COLLATION'];
        if($viewLogicFix != $viewLogicSource){
          $arrayError[] = "Source VIEW DIFFERENT  ";
        }
        if(sizeof($arrayError) !=0){
          $err .= $arrayViewFix->VIEW_NAME." => ".implode(", ",$arrayError)." \n";
        }else{
          $err .= $arrayViewFix->VIEW_NAME." => "."OK \n";
        }
      }else{
        $err .= $arrayViewFix->VIEW_NAME." => VIEW NOT EXITS \n";
      }
    }
    return $err;
  }
  function fixView($fileName,$checkResult){
    $jsonFile = file_get_contents($fileName);
    $decodeJsonFile = json_decode($jsonFile);
    $databaseName = $decodeJsonFile[0]->databaseName;
    $configDatabase = $this->setConfig();
    $this->connnectionCheckDB = mysqli_connect("localhost",$configDatabase["USER"], $configDatabase["PASSWORD"], $databaseName);
    $arrayView = $decodeJsonFile[0]->views;
    for ($i=0; $i < sizeof($arrayView); $i++) {
      $arrayError = array();
      $arrayViewFix = json_decode(file_get_contents($arrayView[$i]->json_file));
      $arrayViewSource = $this->getView($databaseName,$arrayViewFix->VIEW_NAME);
      if($this->sqlRowCount($this->sqlQueryCheckDB("SHOW CREATE VIEW ".$arrayViewFix->VIEW_NAME." ")) != 0){
        $viewNameFix = $arrayViewFix->VIEW_NAME;
        $viewLogicFix = $arrayViewFix->VIEW_SOURCE;
        $viewCharsetFix = $arrayViewFix->CHARSET;
        $viewCollationFix = $arrayViewFix->COLLATION;

        $viewNameSource = $arrayViewSource['VIEW_NAME'];
        $viewLogicSource = $arrayViewSource['VIEW_SOURCE'];
        $viewCharsetSource = $arrayViewSource['CHARSET'];
        $viewCollationSource = $arrayViewSource['COLLATION'];
        if($viewLogicFix != $viewLogicSource){
          $arrayError[] = "Source VIEW DIFFERENT ";
        }
        if(sizeof($arrayError) !=0){
          $err .= $arrayViewFix->VIEW_NAME." => FIXED \n";
          $this->createView($databaseName,$arrayViewFix);
        }else{
          $err .= $arrayViewFix->VIEW_NAME." => "."OK \n";
        }
      }else{
        $this->createView($databaseName,$arrayViewFix);
        $err .= $arrayViewFix->VIEW_NAME." => FIXED \n";
      }
    }
    return $err;
  }
  function createView($databaseName,$arrayViewFix){
    $viewNameFix = $arrayViewFix->VIEW_NAME;
    $viewLogicFix = $arrayViewFix->VIEW_SOURCE;
    $viewCharsetFix = $arrayViewFix->CHARSET;
    $viewCollationFix = $arrayViewFix->COLLATION;
    $this->sqlQueryCheckDB("DROP VIEW $databaseName.$viewNameFix");
    $command = $viewLogicFix;
    $this->sqlQueryCheckDB($command);
    return $command;
  }
  function checkRoutine($fileName,$checkResult){
    $jsonFile = file_get_contents($fileName);
    $decodeJsonFile = json_decode($jsonFile);
    $databaseName = $decodeJsonFile[0]->databaseName;
    $configDatabase = $this->setConfig();
    $this->connnectionCheckDB = mysqli_connect("localhost",$configDatabase["USER"], $configDatabase["PASSWORD"], $databaseName);
    $arrayRoutine = $decodeJsonFile[0]->routines;
    for ($i=0; $i < sizeof($arrayRoutine); $i++) {
      $arrayError = array();
      $arrayRoutineFix = json_decode(file_get_contents($arrayRoutine[$i]->json_file));
      $arrayRoutineSource = $this->getRoutine($databaseName,$arrayRoutineFix->ROUTINE_NAME);
      if($this->sqlRowCount($this->sqlQueryCheckDB("select ROUTINE_NAME,HEX(ROUTINE_DEFINITION),ROUTINE_TYPE  from information_schema.ROUTINES where ROUTINE_SCHEMA= '$databaseName' and ROUTINE_NAME = '".$arrayRoutineFix->ROUTINE_NAME."'")) != 0){
        $arrayParameterFix = array();
        $arrayParameterSource= array();
        $routineNameFix = $arrayRoutineFix->ROUTINE_NAME;
        $routineDefinitionFix = $arrayRoutineFix->ROUTINE_DEFINITION;
        $routineTypeFix = $arrayRoutineFix->ROUTINE_TYPE;
        $routineParametersFix = $arrayRoutineFix->PARAMETERS;
        for ($iRoutineFixParams=0; $iRoutineFixParams < sizeof($routineParametersFix) ; $iRoutineFixParams++) {
          $arrayParameterFix[] = array(
            "PARAMETER_NAME" => $routineParametersFix[$iRoutineFixParams]->PARAMETER_NAME,
            "DTD_IDENTIFIER" => $routineParametersFix[$iRoutineFixParams]->DTD_IDENTIFIER,
            "ORDINAL_POSITION" => $routineParametersFix[$iRoutineFixParams]->ORDINAL_POSITION,
            // "CHARACTER_SET_NAME" => $routineParametersFix[$iRoutineFixParams]->CHARACTER_SET_NAME,
            // "COLLATION_NAME" => $routineParametersFix[$iRoutineFixParams]->COLLATION_NAME,
          );
        }

        $routineNameSource = $arrayRoutineSource['ROUTINE_NAME'];
        $routineDefinitionSource = $arrayRoutineSource['ROUTINE_DEFINITION'];
        $routineTypeSource = $arrayRoutineSource['ROUTINE_TYPE'];
        $routineParametersSource = $arrayRoutineSource['PARAMETERS'];
        for ($iRoutineSourceParams=0; $iRoutineSourceParams < sizeof($routineParametersSource) ; $iRoutineSourceParams++) {
          $arrayParameterSource[] = array(
            "PARAMETER_NAME" => $routineParametersSource[$iRoutineSourceParams]['PARAMETER_NAME'],
            "DTD_IDENTIFIER" => $routineParametersSource[$iRoutineSourceParams]['DTD_IDENTIFIER'],
            "ORDINAL_POSITION" => $routineParametersSource[$iRoutineSourceParams]['ORDINAL_POSITION'],
            // "CHARACTER_SET_NAME" => $routineParametersSource[$iRoutineSourceParams]['CHARACTER_SET_NAME'],
            // "COLLATION_NAME" => $routineParametersSource[$iRoutineSourceParams]['COLLATION_NAME'],
          );
        }

        if($arrayParameterFix != $arrayParameterSource ){
          $arrayError[] = "PARAMETERS ROUTINE DIFFERENT ";
        }

        if($routineDefinitionFix != $routineDefinitionSource){
          $arrayError[] = "Source ROUTINE DIFFERENT ";
        }
        if($routineTypeFix != $routineTypeSource){
          $arrayError[] = "TYPE ROUTINE DIFFERENT ";
        }

        if(sizeof($arrayError) !=0){
          $err .= $arrayRoutineFix->ROUTINE_NAME." => ".implode(", ",$arrayError)." \n";
        }else{
          $err .= $arrayRoutineFix->ROUTINE_NAME." => "."OK \n";
        }
      }else{
        $err .= $arrayRoutineFix->ROUTINE_NAME." => ROUTINE NOT EXITS \n";
      }
    }
    return $err;
  }
  function fixRoutine($fileName,$checkResult){
    $jsonFile = file_get_contents($fileName);
    $decodeJsonFile = json_decode($jsonFile);
    $databaseName = $decodeJsonFile[0]->databaseName;
    $configDatabase = $this->setConfig();
    $this->connnectionCheckDB = mysqli_connect("localhost",$configDatabase["USER"], $configDatabase["PASSWORD"], $databaseName);
    $arrayRoutine = $decodeJsonFile[0]->routines;
    for ($i=0; $i < sizeof($arrayRoutine); $i++) {
      $errorStatus = 0;
      $arrayError = array();
      $arrayRoutineFix = json_decode(file_get_contents($arrayRoutine[$i]->json_file));
      $arrayRoutineSource = $this->getRoutine($databaseName,$arrayRoutineFix->ROUTINE_NAME);
      if($this->sqlRowCount($this->sqlQueryCheckDB("select ROUTINE_NAME,HEX(ROUTINE_DEFINITION),ROUTINE_TYPE  from information_schema.ROUTINES where ROUTINE_SCHEMA= '$databaseName' and ROUTINE_NAME = '".$arrayRoutineFix->ROUTINE_NAME."'")) != 0){
        $arrayParameterFix = array();
        $arrayParameterSource= array();
        $routineNameFix = $arrayRoutineFix->ROUTINE_NAME;
        $routineDefinitionFix = $arrayRoutineFix->ROUTINE_DEFINITION;
        $routineTypeFix = $arrayRoutineFix->ROUTINE_TYPE;
        $routineParametersFix = $arrayRoutineFix->PARAMETERS;
        for ($iRoutineFixParams=0; $iRoutineFixParams < sizeof($routineParametersFix) ; $iRoutineFixParams++) {
          $arrayParameterFix[] = array(
            "PARAMETER_NAME" => $routineParametersFix[$iRoutineFixParams]->PARAMETER_NAME,
            "DTD_IDENTIFIER" => $routineParametersFix[$iRoutineFixParams]->DTD_IDENTIFIER,
            "ORDINAL_POSITION" => $routineParametersFix[$iRoutineFixParams]->ORDINAL_POSITION,
            // "CHARACTER_SET_NAME" => $routineParametersFix[$iRoutineFixParams]->CHARACTER_SET_NAME,
            // "COLLATION_NAME" => $routineParametersFix[$iRoutineFixParams]->COLLATION_NAME,
          );
        }

        $routineNameSource = $arrayRoutineSource['ROUTINE_NAME'];
        $routineDefinitionSource = $arrayRoutineSource['ROUTINE_DEFINITION'];
        $routineTypeSource = $arrayRoutineSource['ROUTINE_TYPE'];
        $routineParametersSource = $arrayRoutineSource['PARAMETERS'];
        for ($iRoutineSourceParams=0; $iRoutineSourceParams < sizeof($routineParametersSource) ; $iRoutineSourceParams++) {
          $arrayParameterSource[] = array(
            "PARAMETER_NAME" => $routineParametersSource[$iRoutineSourceParams]['PARAMETER_NAME'],
            "DTD_IDENTIFIER" => $routineParametersSource[$iRoutineSourceParams]['DTD_IDENTIFIER'],
            "ORDINAL_POSITION" => $routineParametersSource[$iRoutineSourceParams]['ORDINAL_POSITION'],
            // "CHARACTER_SET_NAME" => $routineParametersSource[$iRoutineSourceParams]['CHARACTER_SET_NAME'],
            // "COLLATION_NAME" => $routineParametersSource[$iRoutineSourceParams]['COLLATION_NAME'],
          );
        }

        if($arrayParameterFix != $arrayParameterSource ){
          $arrayError[] = "PARAMETERS ROUTINE DIFFERENT ";
        }

        if($routineDefinitionFix != $routineDefinitionSource){
          $arrayError[] = "Source ROUTINE DIFFERENT ";
        }
        if($routineTypeFix != $routineTypeSource){
          $arrayError[] = "TYPE ROUTINE DIFFERENT ";
        }

        if(sizeof($arrayError) !=0){
          $errorStatus = 1;
          $err .= $arrayRoutineFix->ROUTINE_NAME." => FIXED \n";
        }else{
          $err .= $arrayRoutineFix->ROUTINE_NAME." => "."OK \n";
          $errorStatus = 0;
        }
      }else{
        $errorStatus = 1;
        $err .= $arrayRoutineFix->ROUTINE_NAME." => FIXED \n";
      }
      if($errorStatus == 1){
        if($arrayRoutineFix->ROUTINE_TYPE == "FUNCTION"){
          $this->createRoutineFunction($databaseName,$arrayRoutineFix);
        }else{
          $this->createRoutineStoreProcedure($databaseName,$arrayRoutineFix);

        }
      }

    }
    return $err;
  }

  function createRoutineFunction($databaseName,$arrayRoutineFix) {
    $routineNameFix = $arrayRoutineFix->ROUTINE_NAME;
    $routineDefinitionFix = $arrayRoutineFix->ROUTINE_DEFINITION;
    $routineTypeFix = $arrayRoutineFix->ROUTINE_TYPE;
    $routineParametersFix = $arrayRoutineFix->PARAMETERS;
    for ($iRoutineFixParams=0; $iRoutineFixParams < sizeof($routineParametersFix) ; $iRoutineFixParams++) {
        if($routineParametersFix[$iRoutineFixParams]->ORDINAL_POSITION == '0' ){
          $returnFunction = "RETURNS ".$routineParametersFix[$iRoutineFixParams]->DTD_IDENTIFIER;
        }else{
          $arrayParameter[] =$routineParametersFix[$iRoutineFixParams]->PARAMETER_NAME." ".$routineParametersFix[$iRoutineFixParams]->DTD_IDENTIFIER." ";
        }
    }
    shell_exec("mysql -u".$this->userMysql." -p".$this->passwordMysql." -D $databaseName  -s -e 'DROP FUNCTION IF EXISTS $routineNameFix' ");
    file_put_contents("temp.proc","DELIMITER ;; \n CREATE FUNCTION `$routineNameFix` (".implode(",",$arrayParameter).") $returnFunction \n".$this->hexDecode($routineDefinitionFix).";;");
    $command = "mysql -u".$this->userMysql." -p".$this->passwordMysql." -f -c $databaseName < temp.proc";
    shell_exec($command);
    return $comand;
  }
  function createRoutineStoreProcedure($databaseName,$arrayRoutineFix) {
    $routineNameFix = $arrayRoutineFix->ROUTINE_NAME;
    $routineDefinitionFix = $arrayRoutineFix->ROUTINE_DEFINITION;
    $routineTypeFix = $arrayRoutineFix->ROUTINE_TYPE;
    $routineParametersFix = $arrayRoutineFix->PARAMETERS;
    for ($iRoutineFixParams=0; $iRoutineFixParams < sizeof($routineParametersFix) ; $iRoutineFixParams++) {
        $arrayParameter[] =$routineParametersFix[$iRoutineFixParams]->PARAMETER_NAME." ".$routineParametersFix[$iRoutineFixParams]->DTD_IDENTIFIER." ";
    }
    shell_exec("mysql -u".$this->userMysql." -p".$this->passwordMysql." -D $databaseName  -s -e 'DROP PROCEDURE IF EXISTS $routineNameFix' ");
    file_put_contents("temp.proc","DELIMITER ;; \n CREATE PROCEDURE `$routineNameFix` (".implode(",",$arrayParameter).")  \n".$this->hexDecode($routineDefinitionFix).";;");
    $command = "mysql -u".$this->userMysql." -p".$this->passwordMysql." -f -c $databaseName < temp.proc";
    shell_exec($command);
    return $comand;
  }
  function fixTrigger($fileName,$checkResult){
    $jsonFile = file_get_contents($fileName);
    $decodeJsonFile = json_decode($jsonFile);
    $databaseName = $decodeJsonFile[0]->databaseName;
    $configDatabase = $this->setConfig();
    $this->connnectionCheckDB = mysqli_connect("localhost",$configDatabase["USER"], $configDatabase["PASSWORD"], $databaseName);
    $arrayTrigger = $decodeJsonFile[0]->triggers;
    for ($i=0; $i < sizeof($arrayTrigger); $i++) {
      $arrayError = array();
      $arrayTriggerFix = json_decode(file_get_contents($arrayTrigger[$i]->json_file));
      $arrayTriggerSource = $this->getTrigger($databaseName,$arrayTriggerFix->TRIGGER_NAME);
      if($this->sqlRowCount($this->sqlQueryCheckDB("select TRIGGER_NAME,HEX(ACTION_STATEMENT),ACTION_TIMING,EVENT_MANIPULATION,EVENT_OBJECT_TABLE  from information_schema.TRIGGERS where TRIGGER_SCHEMA= '$databaseName' and TRIGGER_NAME = '".$arrayTriggerFix->TRIGGER_NAME."'")) != 0){
        $triggerNameFix = $arrayTriggerFix->TRIGGER_NAME;
        $hexActionStatementFix = $arrayTriggerFix->ACTION_STATEMENT;
        $actionTimingFix = $arrayTriggerFix->ACTION_TIMING;
        $eventManipulationFix = $arrayTriggerFix->EVENT_MANIPULATION;
        $eventObjectTableFix = $arrayTriggerFix->EVENT_OBJECT_TABLE;
        $triggerNameSource = $arrayTriggerSource['TRIGGER_NAME'];
        $hexActionStatementSource = $arrayTriggerSource['ACTION_STATEMENT'];
        $actionTimingSource = $arrayTriggerSource['ACTION_TIMING'];
        $eventManipulationSource = $arrayTriggerSource['EVENT_MANIPULATION'];
        $eventObjectTableSource = $arrayTriggerSource['EVENT_OBJECT_TABLE'];
        if($hexActionStatementSource != $hexActionStatementFix){
          $arrayError[] = "Source TRIGGER DIFFERENT ";
        }
        if($actionTimingSource != $actionTimingFix){
          $arrayError[] = "ACTION TIMMING TRIGGER DIFFERENT ";
        }
        if($eventManipulationFix != $eventManipulationSource){
          $arrayError[] = "EVENT MANIPULATION TRIGGER DIFFERENT ";
        }
        if($eventObjectTableSource != $eventObjectTableFix){
          $arrayError[] = "EVENT OBJECT TABLE TRIGGER DIFFERENT ";
        }
        if(sizeof($arrayError) !=0){
          $err .= $arrayTriggerFix->TRIGGER_NAME." => FIXED \n";
          $this->replaceTrigger($databaseName,$arrayTriggerFix);
        }else{
          $err .= $arrayTriggerFix->TRIGGER_NAME." => "."OK \n";
        }
      }else{
        $err .= $arrayTriggerFix->TRIGGER_NAME." => FIXED \n";
        $this->replaceTrigger($databaseName,$arrayTriggerFix);
      }
    }
    return $err;
  }
  function replaceTrigger($databaseName,$arrayTriggerFix){
    $triggerNameFix = $arrayTriggerFix->TRIGGER_NAME;
    $hexActionStatementFix = $arrayTriggerFix->ACTION_STATEMENT;
    $actionTimingFix = $arrayTriggerFix->ACTION_TIMING;
    $eventManipulationFix = $arrayTriggerFix->EVENT_MANIPULATION;
    $eventObjectTableFix = $arrayTriggerFix->EVENT_OBJECT_TABLE;
    $this->sqlQueryCheckDB("DROP TRIGGER $triggerNameFix");
    file_put_contents("temp.proc","DELIMITER ;; \n CREATE TRIGGER `$triggerNameFix` $actionTimingFix $eventManipulationFix ON `$eventObjectTableFix` FOR EACH ROW \n".$this->hexDecode($hexActionStatementFix).";;");
    $command = "mysql -u".$this->userMysql." -p".$this->passwordMysql." -f -c $databaseName <  temp.proc";
    shell_exec($command);
    return $command;
  }

  function fixStruktur($fileName,$checkResult){
    $jsonFile = file_get_contents($fileName);
    $decodeJsonFile = json_decode($jsonFile);
    $databaseName = $decodeJsonFile[0]->databaseName;
    $configDatabase = $this->setConfig();
    $this->connnectionCheckDB = mysqli_connect("localhost",$configDatabase["USER"], $configDatabase["PASSWORD"], $databaseName);
    $arrayTable = $decodeJsonFile[0]->tables;
    for ($i=0; $i < sizeof($arrayTable); $i++) {
      $arrayKolomDatabase = array();
      $arrayKolomCheckTable = array();
      $arrayStrukturTableFix = json_decode(file_get_contents($arrayTable[$i]->json_file));
      $arrayStruktur = $this->getStruktur($databaseName,$arrayTable[$i]->table_name);
      $arrayIndexTable = $this->getIndexTable($databaseName,$arrayTable[$i]->table_name);
      if($this->sqlRowCount($this->sqlQueryCheckDB("SHOW TABLES like '".$arrayStrukturTableFix->tableName."'")) != 0){
        for ($a=0; $a < sizeof($arrayStrukturTableFix->colums); $a++) {
          $error = array();
          $errorCode = array();
          if(array_search($arrayStrukturTableFix->colums[$a]->COLUMN_NAME, array_column($arrayStruktur, 'COLUMN_NAME')) !== false){
            $columnNameFix = $arrayStrukturTableFix->colums[$a]->COLUMN_NAME;
            $typeDataFix = $arrayStrukturTableFix->colums[$a]->COLUMN_TYPE;
            $charsetFix = $arrayStrukturTableFix->colums[$a]->CHARACTER_SET_NAME;
            $colattionFix = $arrayStrukturTableFix->colums[$a]->COLLATION_NAME;
            $isNUllAbleFix = $arrayStrukturTableFix->colums[$a]->IS_NULLABLE;
            $columnNameSource = $arrayStruktur[$a]['COLUMN_NAME'];
            $typeDataSource = $arrayStruktur[$a]['COLUMN_TYPE'];
            $charsetSource = $arrayStruktur[$a]['CHARACTER_SET_NAME'];
            $colattionSource = $arrayStruktur[$a]['COLLATION_NAME'];
            $isNUllAbleSource = $arrayStruktur[$a]['IS_NULLABLE'];
            if($typeDataFix != $typeDataSource){
               $errorCode[]= 1; //TYPE DATA
            }
            if($charsetFix != $charsetSource){
               $errorCode[]= 2; //CHARSET
            }
            if($colattionFix != $colattionSource){
               $errorCode[]= 3; // COLLATION
            }
            if($isNUllAbleFix != $isNUllAbleSource){
               $errorCode[]= 4;//NULL ABLE
            }
            if(sizeof($errorCode)!=0){
              $this->fixColumn($databaseName,$arrayStrukturTableFix->tableName,$arrayStrukturTableFix->colums[$a]);
            }
          }else{
             $this->addColumn($databaseName,$arrayStrukturTableFix->tableName,$arrayStrukturTableFix->colums[$a])."\n";
          }
          $arrayKolomCheckTable[] =  $arrayStrukturTableFix->colums[$a]->COLUMN_NAME." => FIXED";
        }
        $listCheckColomn= "";
        for ($zx=0; $zx < sizeof($arrayKolomCheckTable); $zx++) {
          $listCheckColomn .="\n \t\t- ".$arrayKolomCheckTable[$zx];
        }
        $arrayKolomCheckIndex = array();
        $listCheckIndex = "";
        for ($abc=0; $abc < sizeof($arrayStrukturTableFix->index); $abc++) {
          $statusCheckIndex = " OK ";
          $nameColumnFix = $arrayStrukturTableFix->index[$abc]->column_name;
          $keyNameFix = $arrayStrukturTableFix->index[$abc]->key_name;
          $nonUniqueFix = $arrayStrukturTableFix->index[$abc]->non_uniqe;
          $nameColumnSource = $arrayIndexTable[$abc]['column_name'];
          $keyNameSource = $arrayIndexTable[$abc]['key_name'];
          $nonUniqueSource = $arrayIndexTable[$abc]['non_uniqe'];
          if(!empty($nameColumnSource)){
            $error = array();
            if($keyNameFix != $keyNameSource){
              $error[] = "KEY NAME DIFFERENT $keyNameSource";
            }
            if($nonUniqueFix != $nonUniqueSource){
              $error[] = "NON UNIQUE DIFFERENT $nonUniqueSource";
            }
            if(sizeof($error) != 0){
              $statusCheckIndex = implode(", ",$error);
              $statusCheckIndex = $this->fixIndex($databaseName,$arrayStrukturTableFix->tableName,$arrayStrukturTableFix->index[$abc],$arrayStrukturTableFix->index);
            }
          }else{

            $statusCheckIndex = $this->addIndex($databaseName,$arrayStrukturTableFix->tableName,$arrayStrukturTableFix->index[$abc],$arrayStrukturTableFix->index);
          }
          $listCheckIndex .= "\n \t \t INDEX => ".$keyNameFix ."  $statusCheckIndex";

        }
      }else{
         $this->importDatabase($databaseName,$arrayStrukturTableFix->dumpFile);
      }
      $options = getopt(null, array(
        "defaultValue:",
      ));
      foreach ($options as $key => $value) {
         $$key = $value;
      }
      if($defaultValue == "true"){
        $arrayDefaultValue = $arrayStrukturTableFix->default_value;
        for ($iDefaultValue=0; $iDefaultValue < sizeof($arrayDefaultValue) ; $iDefaultValue++) {
          $this->sqlQueryCheckDB($arrayDefaultValue[$iDefaultValue]);
        }
      }
      $namaTable .= $arrayTable[$i]->table_name." =>  ".$arrayTable[$i]->json_file." $listCheckColomn $listCheckIndex
       \n";

    }

    return $namaTable;
  }
  function fixColumn($databaseName,$tableName,$arrayFixColumn){
    if($arrayFixColumn->CHARACTER_SET_NAME != null){
      $charSet = "CHARACTER SET ".$arrayFixColumn->CHARACTER_SET_NAME." COLLATE ".$arrayFixColumn->COLLATION_NAME."";
    }
    if($arrayFixColumn->IS_NULLABLE == "NO"){
      $notNull = "NOT NULL";
    }
    $command = "ALTER TABLE $databaseName.$tableName MODIFY ".$arrayFixColumn->COLUMN_NAME." ".$arrayFixColumn->COLUMN_TYPE." $charSet $notNull ;";
    $this->sqlQuery($command);
    return $command;
  }
  function addColumn($databaseName,$tableName,$arrayFixColumn){
    if($arrayFixColumn->CHARACTER_SET_NAME != null){
      $charSet = "CHARACTER SET ".$arrayFixColumn->CHARACTER_SET_NAME." COLLATE ".$arrayFixColumn->COLLATION_NAME."";
    }
    if($arrayFixColumn->IS_NULLABLE == "NO"){
      $notNull = "NOT NULL";
    }
    $command = "ALTER TABLE $databaseName.$tableName ADD `".$arrayFixColumn->COLUMN_NAME."` ".$arrayFixColumn->COLUMN_TYPE." $charSet $notNull ;";
    $this->sqlQuery($command);
    return $command;
  }
  function addIndex($databaseName,$tableName,$arrayFixIndex,$arrayIndexTableFix){
    $localArrayIndex =array();
    if(!in_array($arrayFixIndex->key_name,$this->arrayFixIndexTable)){
      foreach($arrayIndexTableFix as $item) {
        if($item->key_name === $arrayFixIndex->key_name) {
            $this->arrayFixIndexTable[] = $item->key_name ;
            $localArrayIndex[] = "`".$item->column_name."`";
        }
      }
      if(sizeof($localArrayIndex) > 1){
        if($arrayFixIndex->key_name == "PRIMARY"){
          $indexType = "PRIMARY KEY";
          $indexName = $arrayFixIndex->column_name;
        }elseif($arrayFixIndex->non_uniqe != "1"){
          $indexType = "UNIQUE INDEX ";
          $indexName = $arrayFixIndex->key_name;
        }else{
          $indexType = "INDEX ";
          $indexName = $arrayFixIndex->key_name;
        }
        $command = "ALTER TABLE $databaseName.$tableName ADD $indexType `$indexName` (".implode(",",$localArrayIndex).")";
      }else{
        if($arrayFixIndex->key_name == "PRIMARY"){
          $indexType = "PRIMARY KEY";
          $indexName = $arrayFixIndex->column_name;
        }elseif($arrayFixIndex->non_uniqe != "1"){
          $indexType = "UNIQUE INDEX ";
          $indexName = $arrayFixIndex->key_name;
        }else{
          $indexType = "INDEX ";
          $indexName = $arrayFixIndex->key_name;
        }
        $command = "ALTER TABLE $databaseName.$tableName ADD $indexType `$indexName` (`$arrayFixIndex->column_name`)";

      }
      $this->sqlQuery($command);
    }
    return "FIXED";
  }
  function fixIndex($databaseName,$tableName,$arrayFixIndex,$arrayIndexTableFix){
    $localArrayIndex =array();
    if(!in_array($arrayFixIndex->key_name,$this->arrayFixIndexTable)){
      $cek = "ALTER TABLE $databaseName.$tableName DROP INDEX `$arrayFixIndex->key_name`";
      $this->sqlQuery("ALTER TABLE $databaseName.$tableName DROP INDEX `$arrayFixIndex->key_name`");
      foreach($arrayIndexTableFix as $item) {
        if($item->key_name === $arrayFixIndex->key_name) {
            $this->arrayFixIndexTable[] = $item->key_name ;
            $localArrayIndex[] = "`".$item->column_name."`";
        }
      }
      if(sizeof($localArrayIndex) > 1){
        if($arrayFixIndex->key_name == "PRIMARY"){
          $indexType = "PRIMARY KEY";
          $indexName = $arrayFixIndex->column_name;
        }elseif($arrayFixIndex->non_uniqe !="1"){
          $indexType = "UNIQUE INDEX ";
          $indexName = $arrayFixIndex->key_name;
        }else{
          $indexType = "INDEX ";
          $indexName = $arrayFixIndex->key_name;
        }
        $command = "ALTER TABLE $databaseName.$tableName ADD $indexType `$indexName` (".implode(",",$localArrayIndex).")";
      }else{
        if($arrayFixIndex->key_name == "PRIMARY"){
          $indexType = "PRIMARY KEY";
          $indexName = $arrayFixIndex->column_name;
        }elseif($arrayFixIndex->non_uniqe != "1"){
          $indexType = "UNIQUE INDEX ";
          $indexName = $arrayFixIndex->key_name;
        }else{
          $indexType = "INDEX ";
          $indexName = $arrayFixIndex->key_name;
        }
        $command = "ALTER TABLE $databaseName.$tableName ADD $indexType `$indexName` (`$arrayFixIndex->column_name`)";
      }
      $this->sqlQuery($command);
      $cek .= $command;
    }

    return $cek;
  }
  function importDatabase($databaseName,$fileName){
      return shell_exec("mysql -u".$this->userMysql." -p".$this->passwordMysql." $databaseName < $fileName ");
  }

  function getStruktur($databaseName,$tableName){
    $getTableStrukTur = $this->sqlQueryCheckDB("select TABLE_NAME,COLUMN_NAME,COLUMN_TYPE,CHARACTER_SET_NAME,COLLATION_NAME,IS_NULLABLE,COLUMN_DEFAULT from information_schema.COLUMNS where TABLE_SCHEMA= '$databaseName' and TABLE_NAME = '".$tableName."'");
     while ($dataTableStruktur = $this->sqlArrayCheckDB($getTableStrukTur)) {
       $arrayStuktur[] = array(
         "COLUMN_NAME" => $dataTableStruktur['COLUMN_NAME'],
         "COLUMN_TYPE" => $dataTableStruktur['COLUMN_TYPE'],
         "CHARACTER_SET_NAME" => $dataTableStruktur['CHARACTER_SET_NAME'],
         "COLLATION_NAME" => $dataTableStruktur['COLLATION_NAME'],
         "IS_NULLABLE" => $dataTableStruktur['IS_NULLABLE'],
       );
     }
     return $arrayStuktur;
  }
  function getTrigger($databaseName,$triggerName){
    $getDataTrigger = $this->sqlArray($this->sqlQuery("select TRIGGER_NAME,HEX(ACTION_STATEMENT),ACTION_TIMING,EVENT_MANIPULATION,EVENT_OBJECT_TABLE  from information_schema.TRIGGERS where TRIGGER_SCHEMA= '$databaseName' and TRIGGER_NAME = '".$triggerName."'"));
    $arrayTrigger = array(
      "TRIGGER_NAME" => $getDataTrigger['TRIGGER_NAME'],
      "ACTION_STATEMENT" => $getDataTrigger['HEX(ACTION_STATEMENT)'],
      "ACTION_TIMING" => $getDataTrigger['ACTION_TIMING'],
      "EVENT_MANIPULATION" => $getDataTrigger['EVENT_MANIPULATION'],
      "EVENT_OBJECT_TABLE" => $getDataTrigger['EVENT_OBJECT_TABLE'],
    );
    return $arrayTrigger;
  }
  function getView($databaseName,$viewName){
    $getDataView = $this->sqlArray($this->sqlQueryCheckDB("SHOW CREATE VIEW $databaseName.$viewName"));
    $arrayView = array(
      "VIEW_NAME" => $getDataView['View'],
      "VIEW_SOURCE" => $getDataView['Create View'],
      "CHARSET" => $getDataView['character_set_client'],
      "COLLATION" => $getDataView['collation_connection'],
    );
    return $arrayView;
  }
  function getRoutine($databaseName,$routineName){
    $getDataRoutine = $this->sqlArray($this->sqlQuery("select ROUTINE_NAME,HEX(ROUTINE_DEFINITION),ROUTINE_TYPE  from information_schema.ROUTINES where ROUTINE_SCHEMA= '$databaseName' and ROUTINE_NAME = '".$routineName."'"));
    $getDataParameters = $this->sqlQuery("select PARAMETER_NAME,DTD_IDENTIFIER,ORDINAL_POSITION,CHARACTER_SET_NAME,COLLATION_NAME from information_schema.PARAMETERS where SPECIFIC_SCHEMA = '$databaseName' and SPECIFIC_NAME  = '$routineName' order by ORDINAL_POSITION");
    while ($dataParameter = $this->sqlArray($getDataParameters)) {
      $arrayParameter[] = array(
        "PARAMETER_NAME" => $dataParameter['PARAMETER_NAME'],
        "DTD_IDENTIFIER" => $dataParameter['DTD_IDENTIFIER'],
        "ORDINAL_POSITION" => $dataParameter['ORDINAL_POSITION'],
        "CHARACTER_SET_NAME" => $dataParameter['CHARACTER_SET_NAME'],
        "COLLATION_NAME" => $dataParameter['COLLATION_NAME'],
      );
    }
    $arrayRoutine = array(
      "ROUTINE_NAME" => $getDataRoutine['ROUTINE_NAME'],
      "ROUTINE_DEFINITION" => $getDataRoutine['HEX(ROUTINE_DEFINITION)'],
      "ROUTINE_TYPE" => $getDataRoutine['ROUTINE_TYPE'],
      "PARAMETERS" => $arrayParameter,
    );
    return $arrayRoutine;
  }
  function getIndexTable($databaseName,$tableName){
    $getTableIndex = $this->sqlQuery("SHOW INDEX FROM $databaseName.".$tableName."");
     while ($dataTableIndex = $this->sqlArray($getTableIndex)) {
       $arrayTableIndex[] = array(
         "column_name" => $dataTableIndex["Column_name"],
         "key_name" => $dataTableIndex["Key_name"],
         "non_uniqe" => $dataTableIndex["Non_unique"],
       );
     }
     return $arrayTableIndex;
  }


}

$syncClass = new syncClass();


 ?>
