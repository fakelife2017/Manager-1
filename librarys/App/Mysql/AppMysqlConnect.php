<?php

    namespace Librarys\App\Mysql;

    use Librarys\Boot;
    use Librarys\Database\DatabaseConnect;

    final class AppMysqlConnect extends DatabaseConnect
    {

        private $isDatabaseNameCustom;
        private $mysqlQueryStringCurrent;

        public function __construct(Boot $boot)
        {
            parent::__construct($boot);
        }

        public function setDatabaseNameCustom($isDatabaseNameCustom)
        {
            $this->isDatabaseNameCustom = $isDatabaseNameCustom;
        }

        public function isDatabaseNameCustom()
        {
            return $this->isDatabaseNameCustom;
        }

        public function query($sql)
        {
            return parent::query($this->mysqlQueryStringCurrent = $sql);
        }

        public function getMysqlQueryExecStringCurrent()
        {
            return $this->getHost() . '@' . $this->getUsername() . ' > ' . $this->mysqlQueryStringCurrent;
        }

        public function isDatabasenameExists($databaseName, $databaseNameIgone = null, $isStringLowerCase = false, &$bufferOutput = false)
        {
            if ($isStringLowerCase) {
                $databaseName = strtolower($databaseName);

                if ($databaseNameIgone != null)
                    $databaseNameIgone = strtolower($databaseNameIgone);
            }

            $query = $this->query('SHOW DATABASES');

            if ($this->isResource($query)) {
                while ($assoc = $this->fetchAssoc($query)) {
                    $databaseNameCurrentLoop = $assoc['Database'];

                    if ($isStringLowerCase)
                        $databaseNameCurrentLoop = strtolower($databaseNameCurrentLoop);

                    if ($databaseName == $databaseNameCurrentLoop) {
                        if ($assoc != false)
                            $bufferOutput = $assoc;

                        if ($databaseNameIgone == null || $databaseNameIgone != $databaseNameCurrentLoop)
                            return true;
                    }
                }
            }

            return false;
        }

        public function isTableNameExists($tableName, $tableNameIgone = null, $isStringLowerCase = false, &$bufferOutput = false)
        {
            if ($isStringLowerCase) {
                $tableName = strtolower($tableName);

                if ($tableNameIgone != null)
                    $tableNameIgone = strtolower($tableNameIgone);
            }

            $query = $this->query('SHOW TABLE STATUS');

            if ($this->isResource($query)) {
                while ($assoc = $this->fetchAssoc($query)) {
                    $tableNameCurrentLoop = $assoc['Name'];

                    if ($isStringLowerCase)
                        $tableNameCurrentLoop = strtolower($tableNameCurrentLoop);

                    if ($tableName == $tableNameCurrentLoop) {
                        if ($assoc != false)
                            $bufferOutput = $assoc;

                        if ($tableNameIgone == null || $tableNameIgone != $tableNameCurrentLoop)
                            return true;
                    }
                }
            }

            return false;
        }

        public function isColumnNameExists($columnName, $table, $columnNameIgone = null, $isStringLowerCase = false, &$bufferOutput = false)
        {
            if ($isStringLowerCase) {
                $columnName = strtolower($columnName);

                if ($columnNameIgone != null)
                    $columnNameIgone = strtolower($columnNameIgone);
            }

            $query = $this->query('SHOW COLUMNS FROM `' . addslashes($table) . '`');

            if ($this->isResource($query)) {
                while ($assoc = $this->fetchAssoc($query)) {
                    $columnNameCurrentLoop = $assoc['Field'];

                    if ($isStringLowerCase)
                        $columnNameCurrentLoop = strtolower($columnNameCurrentLoop);

                    if ($columnName == $columnNameCurrentLoop) {
                        if ($assoc != false)
                            $bufferOutput = $assoc;

                        if ($columnNameIgone == null || $columnNameIgone != $tableNameCurrentLoop)
                            return true;
                    }
                }
            }

            return false;
        }
    }

?>