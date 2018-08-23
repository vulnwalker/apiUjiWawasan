-- SAVE STRUKTUR FROM SOURCE
php setup.php --type=showStruktur --tableName=buku_induk,tablename,tablename --dirName=struktur
//SAVE VALUE OF TABLE
php setup.php --type=showStruktur --tableName=buku_induk,tablename,tablename --dirName=struktur --defaultValue=true

-- SAVE TRIGGER FROM SOURCE
php setup.php --type=showTrigger --tableName=triggerName,triggerName --dirName=trigger

-- SAVE ROUTINE FROM SOURCE (FUNCTION, STORE PROCEDURE)
php setup.php --type=showRoutine --tableName=routineName,routineName --dirName=routine

//database.json are reference comparison file for checking table, triggers, routine etc

-- CHECK STRUKTUR FROM COMPARISON FILE
php setup.php --type=checkStruktur --fileName=database.json

-- CHECK TRIGGER FROM COMPARISON FILE
php setup.php --type=checkTrigger --fileName=database.json

-- CHECK ROUTINE FROM COMPARISON FILE
php setup.php --type=checkRoutine --fileName=database.json

-- FIX STRUKTUR FROM COMPARISON FILE
php setup.php --type=fixStruktur --fileName=database.json
//RESTORE DEFAULT VALUE FROM COMPARISON FILE
php setup.php --type=fixStruktur --fileName=database.json --defaultValue=true

-- FIX TRIGGER FROM COMPARISON FILE
php setup.php --type=fixTrigger --fileName=database.json

-- FIX ROUTINE FROM COMPARISON FILE
php setup.php --type=fixRoutine --fileName=database.json
