<?php
/** @noinspection PhpUnhandledExceptionInspection
 * @noinspection DisconnectedForeachInstructionInspection
 * @noinspection PhpUnused
 * @noinspection NullPointerExceptionInspection
 * @noinspection PhpUndefinedMethodInspection
 * @noinspection PhpUndefinedClassConstantInspection
 */


namespace eftec;


use Exception;
use PDOStatement;

/**
 * Class _BaseRepo
 *
 * @version       4.4 2020-05-31
 * @package       eftec
 * @author        Jorge Castro Castillo
 * @copyright (c) Jorge Castro C. MIT License  https://github.com/EFTEC/PdoOne
 */
abstract class _BasePdoOneRepo
{
    /** @var PdoOne */
    public static $pdoOne;
    /** @var array $gQuery =[['columns'=>[],'joins'=>[],'where'=>[]] */
    public static $gQuery = [];
    public static $gQueryCounter = 0;
    /** @var bool if true then it returns a false on error. If false, it throw an exception in case of error */
    protected static $falseOnError = false;
    public static $lastException = '';

    /**
     * @var null|bool|int $ttl If <b>null</b> then the cache never expires.<br>
     *                         If <b>false</b> then we don't use cache.<br>
     *                         If <b>int</b> then it is the duration of the
     *     cache
     *                         (in seconds)
     */
    private static $useCache = false;

    /** @var null|string the unique id generate by sha256 and based in the query, arguments, type and methods */
    private static $uid;
    /** @var string [optional] It is the family or group of the cache */
    private static $cacheFamily = '';

    /**
     * If true then it returns false on exception. Otherwise, it throws an exception.
     * 
     * @param bool $falseOnError
     *
     * @return mixed
     */
    public static function setFalseOnError($falseOnError=true) {
        self::$falseOnError=$falseOnError;
        return static::ME;
    }
    

    /**
     * It creates a new table<br>
     * If the table exists then the operation is ignored (and it returns false)
     *
     * @param null $extra
     *
     * @return array|bool|PDOStatement
     * @throws Exception
     */
    public static function createTable($extra = null)
    {
        try {
            if (!self::getPdoOne()->tableExist(static::TABLE)) {
                return self::getPdoOne()
                    ->createTable(static::TABLE, $definition = static::getDef('sql'), static::getDefKey(), $extra);
            }
        } catch (Exception $exception) {
            if (self::$falseOnError) {
                self::reset();
                self::$lastException = $exception->getMessage();
                return false;
            }
            self::reset();
            throw $exception;
        }
        self::reset();
        return false; // table already exist
    }

    /**
     * It creates foreign keys of the table using the definition previously defined.
     *
     * @throws Exception
     */
    public static function createForeignKeys()
    {
        try {
            $def = static::getDefFK(true);
            $def2 = static::getDefFK(false);
            foreach ($def as $k => $v) {
                $sql = 'ALTER TABLE ' . self::getPdoOne()->addQuote(static::TABLE) . ' ADD CONSTRAINT '
                    . self::getPdoOne()->addQuote($def2[$k]['name']) . ' ' . $v;
                $sql = str_ireplace('FOREIGN KEY REFERENCES',
                    'FOREIGN KEY(' . self::getPdoOne()->addQuote($k) . ') REFERENCES', $sql);
                self::getPdoOne()->runRawQuery($sql, [], true);
            }
        } catch (Exception $exception) {
            if (self::$falseOnError) {
                self::reset();
                self::$lastException = $exception->getMessage();
                return false;
            }
            self::reset();
            throw $exception;
        }
        self::reset();
        return true;
    }

    /**
     * It runs a query and returns an array, value or false if error.<br>
     * The this command does not stack with other operators (such as where(),sort(),etc.)
     * <br><b>Example</b>:<br>
     * <pre>
     * $values=$con->query('select * from table where id=?',["i",20]',true);
     * </pr>
     *
     * @param string     $sql   The query to run
     * @param array|null $param [Optional] The arguments of the query in the form [type,value,type2,value2..]
     *
     * @return array|bool|false|null
     * @throws Exception
     */
    public static function query($sql, $param = null)
    {
        try {
            $pdoOne = self::getPdoOne();

            if (self::$useCache && $pdoOne->getCacheService() !== null) {
                self::$uid = $pdoOne->buildUniqueID([$sql, $param], 'query');
                $getCache = $pdoOne->getCacheService()->getCache(self::$uid, static::TABLE);
                if ($getCache !== false) {
                    self::reset();
                    return $getCache;
                }
                $recursiveClass = static::getRecursiveClass();
                $usingCache = true;
            } else {
                $recursiveClass = null;
                $usingCache = false;
            }
            $rowc = self::getPdoOne()->runRawQuery($sql, $param, true);
            if ($rowc !== false && $usingCache) {
                $pdoOne->getCacheService()->setCache(self::$uid, $recursiveClass, $rowc, self::$useCache);
                self::reset();
            }
        } catch (Exception $exception) {
            if (self::$falseOnError) {
                self::reset();
                self::$lastException = $exception->getMessage();
                return false;
            }
            self::reset();
            throw $exception;
        }
        self::reset();
        return $rowc;
    }


    /**
     * It is used for DI.<br>
     * If the field is not null, it returns the field self::$pdoOne<br>
     * If the global function pdoOne exists, then it is used<br>
     * if the global variable $pdoOne exists, then it is used<br>
     * Otherwise, it returns null
     *
     * @return PdoOne
     */
    protected static function getPdoOne()
    {
        if (self::$pdoOne !== null) {
            return self::$pdoOne;
        }
        if (function_exists('pdoOne')) {
            return pdoOne();
        }
        if (isset($GLOBALS['pdoOne'])) {
            return $GLOBALS['pdoOne'];
        }
        return null;
    }

    /**
     * It sets the field self::$pdoOne
     *
     * @param $pdoOne
     */
    public static function setPdoOne($pdoOne)
    {
        self::$pdoOne = $pdoOne;
    }

    /**
     * It creates a foreign keys<br>
     *
     * @return array|bool|PDOStatement
     * @throws Exception
     */
    public static function createFk()
    {
        try {
            return self::getPdoOne()->createFk(static::TABLE, static::getDefFk());
        } catch (Exception $exception) {
            if (self::$falseOnError) {
                self::$lastException = $exception->getMessage();
                return false;
            }
            throw $exception;
        }
    }

    /**
     * It validates the table and returns an associative array with the errors.
     *
     * @return array If valid then it returns an empty array
     * @throws Exception
     */
    public static function validTable()
    {
        try {
            return self::getPdoOne()
                ->validateDefTable(static::TABLE, static::getDef('sql'), static::getDefKey(), static::getDefFk());
        } catch (Exception $exception) {
            if (self::$falseOnError) {
                self::reset();
                self::$lastException = $exception->getMessage();
                return ['exception' => $exception->getMessage()];
            }
            self::reset();
            throw $exception;
        }
    }

    /**
     * It cleans the whole table (delete all rows)
     *
     * @return array|bool|PDOStatement
     * @throws Exception
     */
    public static function truncate()
    {
        try {
            return self::getPdoOne()->truncate(static::TABLE);
        } catch (Exception $exception) {
            if (self::$falseOnError) {
                self::$lastException = $exception->getMessage();
                return false;
            }
            throw $exception;
        }
    }

    /**
     * It drops the table (structure and values)
     *
     * @return array|bool|PDOStatement
     * @throws Exception
     */
    public static function dropTable()
    {
        try {
            if (!self::getPdoOne()->tableExist(static::TABLE)) {
                return self::getPdoOne()->dropTable(static::TABLE);
            }
        } catch (Exception $exception) {
            if (self::$falseOnError) {
                self::reset();
                self::$lastException = $exception->getMessage();
                return false;
            }
            self::reset();
            throw $exception;
        }
        self::reset();
        return false; // table does not exist
    }


    /*protected static function _merge($entity) {
        $entity = self::intersectArrays($entity,static::getDefName());
        $identities = static::getDefIdentity();
        foreach ($entity as $k => $v) {
            // identities are not inserted
            if (in_array($k, $identities, true)) {
                unset($entity[$k]);
            }
        }
        //$pdo->select('1')->from(static::TABLE)->where()
    }
    */

    protected static function _exist($entity)
    {
        try {
            $pks = static::PK;
            if (is_array($entity)) {
                foreach ($entity as $k => $v) { // we keep the pks
                    if (!in_array($k, $pks, true)) {
                        unset($entity[$k]);
                    }
                }
            } elseif (is_array($pks) && count($pks)) {
                $entity = [$pks[0] => $entity];
            } else {
                self::getPdoOne()->throwError('exist: entity not specified as an array or table lacks of PKs', $entity);
                return false;
            }
            $r = self::getPdoOne()->genError(false)->select('1')->from(static::TABLE)->where($entity)->firstScalar();
            self::getPdoOne()->genError(true);
            self::reset();
            return ($r === '1');
        } catch (Exception $exception) {
            if (self::$falseOnError) {
                self::reset();
                self::$lastException = $exception->getMessage();
                return false;
            }
            self::reset();
            throw $exception;
        }
    }

    /**
     * It converts ['aaa.bbb'=>'v'] into ['aaa']['bbb']='v';
     *
     * @param array $data
     *
     * @return array
     */
    protected static function convertRow($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        $row = [];
        foreach ($data as $k => $v) {
            if (strpos($k, '.') === false) {
                $row[$k] = $v;
            } else {
                $ar = explode('.', $k);
                switch (count($ar)) {
                    case 2:
                        // 'aaa.bb' => ['aaa']['bbb']
                        $row[$ar[0]][$ar[1]] = $v;
                        break;
                    case 3:
                        // 'aaa.bb.cc' => ['aaa']['bbb']['ccc']
                        $row[$ar[0]][$ar[1]][$ar[2]] = $v;
                        break;
                    case 4:
                        $row[$ar[0]][$ar[1]][$ar[2]][$ar[3]] = $v;
                        break;
                    case 5:
                        $row[$ar[0]][$ar[1]][$ar[2]][$ar[3]][$ar[4]] = $v;
                        break;
                    case 6:
                        $row[$ar[0]][$ar[1]][$ar[2]][$ar[3]][$ar[4]][$ar[5]] = $v;
                        break;
                    case 7:
                        $row[$ar[0]][$ar[1]][$ar[2]][$ar[3]][$ar[4]][$ar[5]][$ar[6]] = $v;
                        break;
                    case 8:
                        $row[$ar[0]][$ar[1]][$ar[2]][$ar[3]][$ar[4]][$ar[5]][$ar[6]][$ar[7]] = $v;
                        break;
                }
            }
        }
        return $row;
    }

    protected static function _toList($filter, $filterValue)
    {
        return self::generationStart('toList', $filter, $filterValue);
    }

    protected static function generationStart($type, $filter = null, $filterValue = null)
    {
        try {
            static::$gQuery = [];
            static::$gQueryCounter = 0;

            /** @var PdoOne $pdoOne instance of PdoOne */
            $pdoOne = self::getPdoOne();
            if (self::$useCache && $pdoOne->getCacheService() !== null) {
                self::$uid = $pdoOne->buildUniqueID([$filter, $filterValue], static::TABLE . '::' . $type);
                $getCache = $pdoOne->getCacheService()->getCache(self::$uid, static::TABLE);
                if ($getCache !== false) {
                    self::reset();
                    return $getCache;
                }
                $recursiveClass = static::getRecursiveClass();
                $usingCache = true;
            } else {
                $recursiveClass = null;
                $usingCache = false;
            }


            $newQuery = [];
            $newQuery['type'] = 'QUERY';
            static::$gQuery[0] =& $newQuery;
            $newQuery['joins'] = static::TABLE . ' as ' . static::TABLE . " \n";


            // we build the query
            static::generationRecursive($newQuery, static::TABLE . '.', '', '', false);


            $rows = false;
            foreach (static::$gQuery as $query) {
                if ($query['type'] === 'QUERY') {
                    $from = $query['joins'];
                    $cols = implode(',', $query['columns']);
                    switch ($type) {
                        case 'toList':
                            $rows = $pdoOne->select($cols)->from($from)->where($filter, $filterValue)->toList();
                            break;
                        case 'first':
                            $pdoOne->builderReset();
                            $rows = [
                                $pdoOne->select($cols)->from($from)->where($filter)->first()
                            ];
                            break;
                        default:
                            trigger_error('Repo: method $type not defined');
                            self::reset();
                            return false;
                    }
                }
                foreach ($rows as &$row) {
                    if ($query['type'] === 'ONETOMANY') {
                        $from = $query['joins'];
                        $cols = implode(',', $query['columns']);
                        $partialRows = $pdoOne->select($cols)->from($from)->where($query['where'], $row[$query['col']])
                            ->toList();
                        //->genError(false)
                        foreach ($partialRows as $k => $rowP) {
                            $row2 = self::convertRow($rowP);
                            $partialRows[$k] = $row2;
                        }
                        //$row['/' . $query['table']] = $partialRows;
                        $row[$query['col2']] = $partialRows;
                    }
                }
            }
            if (!is_array($rows)) {
                $rowc = $rows;
            } else {
                $c = count($rows);
                $rowc = [];
                for ($i = 0; $i < $c; $i++) {
                    $rowc[$i] = self::convertRow($rows[$i]);
                }
                self::convertSQLValueInit($rowc, true);
            }
            if ($rowc !== false && $usingCache) {
                $pdoOne->getCacheService()->setCache(self::$uid, $recursiveClass, $rowc, self::$useCache);
            }
            self::reset();
            return $rowc;
        } catch (Exception $exception) {
            if (self::$falseOnError) {
                self::reset();
                self::$lastException = $exception->getMessage();
                return false;
            }
            self::reset();
            throw $exception;
        }
    }

    /**
     * With the recursive arrays, it gets all the classes related to the query (including this class) without the
     * namespace<br>
     * <b>Example:</b><br>
     * <pre>
     * CityRepo::setRecursive(['/countryFK'])::getRecursiveClass(); // ['CityRepo','TableFk']
     * </pre>
     *
     * @param null|array $final  It is used internally for recursivity, it keeps the values.
     * @param string     $prefix It is used internally for recursivity.
     *
     * @return array|null
     */
    public static function getRecursiveClass(&$final = null, $prefix = '')
    {
        $recs = self::getPdoOne()->getRecursive();
        $keyRels = static::getDefFK(false);
        $ns = self::getNamespace();
        $postfix = self::getPostfix();
        if ($final === null) {
            // we start the chain
            $final = [];
            $final[] = PdoOne::camelize(static::TABLE) . $postfix;
        }
        foreach ($recs as $rec) {
            $keyr = $prefix . $rec;

            if (isset($keyRels[$keyr])) {
                $className = PdoOne::camelize($keyRels[$keyr]['reftable']) . $postfix;
                $class = $ns . $className;
                if (!in_array($className, $final, true)) {
                    $final[] = $className;
                }

                $class::getRecursiveClass($final, $keyr);
                if ($keyRels[$keyr]['key'] === 'MANYTOMANY') {
                    $className = PdoOne::camelize($keyRels[$keyr]['table2']) . $postfix;
                    $class = $ns . $className;
                    if (!in_array($className, $final, true)) {
                        $final[] = $className;
                    }
                    $class::getRecursiveClass($final, $keyr);
                }
            }
        }
        return $final;
    }

    protected static function generationRecursive(
        &$newQuery,
        $pTable = '',
        $pColumn = '',
        $recursiveInit = '',
        $new = false
    ) {
        $cols = static::getDefName();
        $keyRels = static::getDefFK(false);
        //$newQuery=[];
        // add columns of the current table
        foreach ($cols as $col) {
            $newQuery['columns'][] = $pTable . $col . ' as ' . self::getPdoOne()->addQuote($pColumn . $col);
        }
        $ns = self::getNamespace();
        $postfix = self::getPostfix();

        foreach ($keyRels as $nameCol => $keyRel) {
            $type = $keyRel['key'];
            $nameColClean = trim($nameCol, '/');
            if (self::getPdoOne()->hasRecursive($recursiveInit . $nameCol)) {
                // type='PARENT' is n
                switch ($type) {
                    case 'MANYTOONE':
                    case 'ONETOONE':
                        static::$gQueryCounter++;
                        $tableRelAlias = 't' . static::$gQueryCounter; //$prefixtable.$nameColClean;
                        //$tableRelAlias =trim($recursiveInit.'_'.$nameColClean,'/'); //str_replace(['/'],['.'],$recursiveInit.'.'.$nameColClean);
                        $colRelAlias = $pColumn . $nameCol;
                        $class = $ns . PdoOne::camelize($keyRel['reftable']) . $postfix;
                        $refCol = $keyRel['refcol'];
                        $newQuery['joins'] .= " left join {$keyRel['reftable']} as $tableRelAlias "
                            . "on $pTable$nameColClean=$tableRelAlias.$refCol \n"; // $recursiveInit$nameCol\n"; // adds a query to the current query
                        $class::generationRecursive($newQuery, $tableRelAlias . '.', $colRelAlias . '.',
                            $recursiveInit . $nameCol, false);
                        break;
                    case 'ONETOMANY':
                    case 'MANYTOMANY':
                        if ($type === 'MANYTOMANY') {
                            $rec = self::getPdoOne()->getRecursive();
                            // automatically we add recursive.
                            $rec[] = $recursiveInit . $nameCol . $keyRel['refcol2'];
                            self::getPdoOne()->recursive($rec);
                        }

                        //$tableRelAlias = ''; //'t' . static::$gQueryCounter;
                        $other = [];
                        $refColClean = trim($keyRel['refcol'], '/');
                        $other['type'] = 'ONETOMANY';
                        $other['table'] = $keyRel['reftable'];
                        $other['where'] = $refColClean;
                        $other['joins'] = " {$keyRel['reftable']} \n";
                        //$tableRelAlias = '*2';
                        $other['col'] = $pColumn . $keyRel['col']; //***
                        $other['col2'] = $pColumn . $nameCol;
                        $other['name'] = $nameCol;
                        $other['data'] = $keyRel;
                        //self::$gQuery[]=$other;
                        $class = $ns . PdoOne::camelize($keyRel['reftable']) . $postfix;

                        $class::generationRecursive($other, '', '', $pColumn . $recursiveInit . $nameCol, false);

                        if ($type === 'MANYTOMANY') {
                            // we reduce a level
                            $columns = $other['columns'];
                            $columnFinal = [];
                            // convert /somefk.column -> column
                            // convert /anything.column -> (deleted)
                            foreach ($columns as $vc) {
                                $findme = $keyRel['refcol2'] . '.';
                                if (strpos($vc, $findme) !== false) {
                                    $columnFinal[] = str_replace($findme, '', $vc);
                                }
                            }
                            $other['columns'] = $columnFinal;
                        }
                        self::$gQuery[] = $other;
                        break;
                    case 'PARENT':
                        // parent does not load recursively information.
                        break;
                    default:
                        trigger_error(static::TABLE . "Repo : type [$type] not defined.");
                }
            }
        }
        if ($new) {
            self::$gQuery[] = $newQuery;
        }
    }

    /**
     * Insert an new row
     *
     * @param array $entity =static::factory()
     *
     * @param bool  $transaction
     *
     * @return mixed
     * @throws Exception
     */
    protected static function _insert(&$entity, $transaction = true)
    {
        try {
            $pdoOne = self::getPdoOne();
            $defTable = static::getDef('conversion');
            self::convertPHPValue($entity, $defTable);

            self::invalidateCache();
            $recursiveBack = $pdoOne->getRecursive();  // recursive is deleted by insertObject
            // only the fields that are defined are inserted
            $entityCopy = self::intersectArrays($entity, static::getDefName());
            $entityCopy = self::diffArrays($entityCopy, static::getDefNoInsert()); // discard some columns
            if ($transaction) {
                $pdoOne->startTransaction();
            }
            $insert = $pdoOne->insertObject(static::TABLE, $entityCopy);
            $pks = $pdoOne->getDefTableKeys(static::TABLE, true, 'PRIMARY KEY');
            if (count($pks) > 0) {
                // we update the identity of $entity ($entityCopy is already updated).
                $entity[array_keys($pks)[0]] = $insert;
            }
            $defs = static::getDefFK();
            $ns = self::getNamespace();
            $postfix = self::getPostfix();

            foreach ($defs as $key => $def) { // ['/tablaparentxcategory']=['key'=>...]
                if ($def['key'] === 'MANYTOMANY' && isset($entity[$key]) && is_array($entity[$key])) {
                    $class2 = $ns . PdoOne::camelize($def['table2']) . $postfix;
                    foreach ($entity[$key] as $item) {
                        $pk2 = $item[$def['col2']];
                        if ($pdoOne->hasRecursive($key, $recursiveBack) && $class2::exist($item) === false) {
                            // we only update it if it has a recursive
                            $pk2 = $class2::insert($item, false);
                        }
                        $classRel = $ns . PdoOne::camelize($def['reftable']) . $postfix;
                        $refCol = ltrim($def['refcol'], '/');
                        $refCol2 = ltrim($def['refcol2'], '/');
                        $relationalObj = [$refCol => $entityCopy[$def['col']], $refCol2 => $pk2];
                        $classRel::insert($relationalObj, false);
                    }
                }
            }
            if ($transaction) {
                self::getPdoOne()->commit();
            }
            return $insert;
        } catch (Exception $exception) {
            if ($transaction) {
                self::getPdoOne()->rollback();
            }
            if (self::$falseOnError) {
                self::reset();
                self::$lastException = $exception->getMessage();
                return false;
            }
            self::reset();
            throw $exception;
        }
    }

    /**
     * Update an registry
     *
     * @param array $entity =static::factory()
     *
     * @param bool  $transaction
     *
     * @return mixed
     * @throws Exception
     */
    protected static function _update($entity, $transaction = true)
    {
        try {
            $pdo = self::getPdoOne();
            $defTable = static::getDef('conversion');
            self::convertPHPValue($entity, $defTable);

            self::invalidateCache();
            // only the fields that are defined are inserted
            $entityCopy = self::intersectArrays($entity, static::getDefName());
            $entityCopy = self::diffArrays($entityCopy, static::getDefNoUpdate()); // columns discarded

            if ($transaction) {
                $pdo->startTransaction();
            }
            $recursiveBack = $pdo->getRecursive();
            $r = $pdo->from(static::TABLE)->set($entityCopy)->where(static::intersectArrays($entity, static::PK))
                ->update();
            $pdo->recursive($recursiveBack); // update() delete recursive
            $defs = static::getDefFK();
            $ns = self::getNamespace();
            $postfix = self::getPostfix();
            foreach ($defs as $key => $def) { // ['/tablaparentxcategory']=['key'=>...]

                if ($def['key'] === 'MANYTOMANY') { //hasRecursive($recursiveInit . $key)
                    if (!isset($entity[$key]) || !is_array($entity[$key])) {
                        $newRows = [];
                    } else {
                        $newRows = $entity[$key];
                    }
                    $classRef = $ns . PdoOne::camelize($def['reftable']) . $postfix;
                    $class2 = $ns . PdoOne::camelize($def['table2']) . $postfix;
                    $col1 = ltrim($def['col'], '/');
                    $refcol = ltrim($def['refcol'], '/');
                    $refcol2 = ltrim($def['refcol2'], '/');
                    $col2 = ltrim($def['col2'], '/');
                    $newRowsKeys = [];
                    foreach ($newRows as $v) {
                        $newRowsKeys[] = $v[$col2];
                    }
                    //self::setRecursive([$def['refcol2']]);
                    self::setRecursive([]);
                    $oldRows = ($classRef::where($refcol, $entity[$col1]))::toList();
                    $oldRowsKeys = [];
                    foreach ($oldRows as $v) {
                        $oldRowsKeys[] = $v[$refcol2];
                    }
                    $insertKeys = array_diff($newRowsKeys, $oldRowsKeys);
                    $deleteKeys = array_diff($oldRowsKeys, $newRowsKeys);
                    // inserting a new value
                    foreach ($newRows as $item) {
                        if (in_array($item[$col2], $insertKeys)) {
                            $pk2 = $item[$def['col2']];
                            if ($class2::exist($item) === false && self::getPdoOne()->hasRecursive($key)) {
                                $pk2 = $class2::insert($item, false);
                            } else {
                                $class2::update($item, false);
                            }
                            $relationalObjInsert = [$refcol => $entity[$def['col']], $refcol2 => $pk2];
                            $classRef::insert($relationalObjInsert, false);
                        }
                    }
                    // delete
                    foreach ($newRows as $item) {
                        if (in_array($item[$col2], $deleteKeys)) {
                            $pk2 = $item[$def['col2']];
                            if (self::getPdoOne()->hasRecursive($key)) {
                                $class2::deleteById($item, $pk2);
                            }
                            $relationalObjDelete = [$refcol => $entity[$def['col']], $refcol2 => $pk2];
                            $classRef::deleteById($relationalObjDelete, false);
                        }
                    }
                }
            }
            if ($transaction) {
                self::getPdoOne()->commit();
            }
            return $r;
        } catch (Exception $exception) {
            if ($transaction) {
                self::getPdoOne()->rollback();
            }
            if (self::$falseOnError) {
                self::reset();
                self::$lastException = $exception->getMessage();
                return false;
            }
            self::reset();
            throw $exception;
        }
    }

    /**
     * It deletes a registry
     *
     * @param array $entity
     * @param bool  $transaction
     *
     * @return mixed
     * @throws Exception
     */
    protected static function _delete($entity, $transaction = true)
    {
        try {
            $entityCopy = self::intersectArraysNotNull($entity, static::getDefName());
            self::invalidateCache();
            $pdo = self::getPdoOne();
            if ($transaction) {
                $pdo->startTransaction();
            }

            $defs = static::getDefFK();
            $ns = self::getNamespace();
            $postfix = self::getPostfix();

            $recursiveBackup = self::getRecursive();

            foreach ($defs as $key => $def) { // ['/tablaparentxcategory']=['key'=>...]

                if ($def['key'] === 'MANYTOMANY' && isset($entity[$key])
                    && is_array($entity[$key])
                ) { //hasRecursive($recursiveInit . $key)
                    $classRef = $ns . PdoOne::camelize($def['reftable']) . $postfix;
                    $class2 = $ns . PdoOne::camelize($def['table2']) . $postfix;

                    $col1 = ltrim($def['col'], '/');
                    $refcol = ltrim($def['refcol'], '/');
                    //$refcol2 = ltrim($def['refcol2'], '/');
                    $col2 = $def['col2'];

                    //self::setRecursive([$def['refcol2']]);
                    self::setRecursive([]);


                    $cols2 = [];
                    foreach ($entity[$key] as $item) {
                        $cols2[] = $item[$col2];
                    }
                    $relationalObjDelete = [$refcol => $entity[$col1]];
                    $classRef::delete($relationalObjDelete, false);
                    if (self::getPdoOne()->hasRecursive($key, $recursiveBackup)) {
                        foreach ($cols2 as $c2) {
                            // $k = $v[$refcol2];
                            $object2Delete = [$col2 => $c2];
                            $class2::delete($object2Delete, false);
                        }
                    }
                    self::setRecursive($recursiveBackup);
                }
            }
            $r = self::getPdoOne()->delete(static::TABLE, $entityCopy);
            if ($transaction) {
                //self::getPdoOne()->rollback();
                self::getPdoOne()->commit();
            }
            self::reset();
            return $r;
        } catch (Exception $exception) {
            if ($transaction) {
                self::getPdoOne()->rollback();
            }
            if (self::$falseOnError) {
                self::reset();
                self::$lastException = $exception->getMessage();
                return false;
            }
            self::reset();
            throw $exception;
        }
    }


    /**
     * Merge two arrays only if the value of the second array is contained in the first array<br>
     * It works as masking. Example:<br>
     * <pre>
     * $this->intersectArrays(['a'=>'aaa','b'=>'bbb'],['a'],false); // ['a'=>'aaa']
     * $this->intersectArrays(['a'=>'aaa','b'=>'bbb'],[0=>'a'],true); // ['a'=>'aaa']
     * </pre>
     *
     * @param array $arrayValues An associative array with the keys and values.
     * @param array $arrayIndex  A string array with the indexes (if indexisKey=false then index is the value)
     * @param bool  $indexIsKey  (default false) if true then the index of $arrayIndex is considered as key
     *                           , otherwise the value of $arrayIndex is considered as key.
     *
     * @return array
     */
    public static function intersectArrays($arrayValues, $arrayIndex, $indexIsKey = false)
    {
        $result = [];

        foreach ($arrayIndex as $k => $v) {
            if ($indexIsKey) {
                $result[$k] = isset($arrayValues[$k]) ? $arrayValues[$k] : null;
            } else {
                $result[$v] = isset($arrayValues[$v]) ? $arrayValues[$v] : null;
            }
        }
        return $result;
    }

    /**
     * It filter an associative array<br>
     * <b>Example:</b><br>
     * <pre>
     * self::intersectArraysNotNull(['a1'=>1,'a2'=>2],['a1','a3']); // ['a1'=>1]
     * </pre>
     *
     * @param array $arrayValues An associative array with key as the column
     * @param array $arrayIndex  An indexed array with the name of the columns
     *
     * @return array
     */
    public static function intersectArraysNotNull($arrayValues, $arrayIndex)
    {
        $result = [];
        foreach ($arrayIndex as $k) {
            if (isset($arrayValues[$k])) {
                $result[$k] = $arrayValues[$k];
            }
        }
        return $result;
    }

    /**
     * Remove elements of an array unsing an array (indexed or not)<br>
     * <pre>
     * $this->diffArrays(['a'=>'aaa','b'=>'bbb'],['a'],false); // [b'=>'bbb']
     * $this->diffArrays(['a'=>'aaa','b'=>'bbb'],[0=>'a'],true); // [b'=>'bbb']
     * </pre>
     *
     * @param      $arrayValues
     * @param      $arrayIndex
     * @param bool $indexIsKey
     *
     * @return array
     */
    public static function diffArrays($arrayValues, $arrayIndex, $indexIsKey = false)
    {
        $result = [];

        foreach ($arrayValues as $k => $v) {
            if (!$indexIsKey && !in_array($k, $arrayIndex)) {
                $result[$k] = $v;
            }
            if ($indexIsKey && !array_key_exists($k, $arrayIndex)) {
                $result[$k] = $v;
            }
        }
        return $result;
    }

    /**
     * It deletes a registry
     *
     * @param mixed|array $pks
     *
     * @param bool        $transaction
     *
     * @return mixed
     * @throws Exception
     */
    protected static function _deleteById($pks, $transaction = true)
    {
        if (!is_array($pks)) {
            $pksI = [];
            $pksI[static::PK[0]] = $pks; // we convert into an associative array
        } else {
            $pksI = $pks;
        }
        return self::_delete($pksI, $transaction);
    }

    /**
     * It gets the first value of a query<br>
     * <b>Example:</b><br>
     * <pre>
     * self::_first('2'); // select * from table where pk='2' (only returns the first value)
     * self::_first();  // select * from table (returns the first row if any)
     * self::where(['pk'=>'2'])::_first();  // select * from table where pk='2'
     * self::_first(['pk'=>'2']);  // select * from table where pk='2'
     * </pre>
     *
     * @param mixed $pk If mixed. If null then it doesn't use the primary key to obtain data.
     *
     * @return array|bool static::factory()
     * @throws Exception
     */
    protected static function _first($pk = null)
    {
        if ($pk !== null) {
            $pk = is_array($pk) ? $pk : [static::PK[0] => $pk];
        }
        $r = self::generationStart('first', $pk);
        if (is_array($r)) {
            return $r[0];
        }
        return $r;
    }

    /**
     * It gets the postfix of the class base considering the the class is based in the table<br>
     * Example: Class "SomeTableRepo" and table "sometable", the prefix is "Repo"
     *
     * @return false|string False on error or not found.
     */
    public static function getPostfix()
    {
        $class = static::class;
        $table = static::TABLE;
        $p0 = strripos($class, $table) + strlen($table);
        if ($p0 === false) {
            return false;
        }
        return substr($class, $p0);
    }

    /**
     * It gets the current namespace.
     *
     * @return string
     */
    public static function getNamespace()
    {
        if (strpos(static::class, '\\')) { // we assume that every repo class lives in the same namespace.
            $ns = explode('\\', static::class);
            array_pop($ns);
            $ns = implode('\\', $ns) . '\\';
        } else {
            $ns = '';
        }
        return $ns;
    }

    public static function getRecursive()
    {
        return self::getPdoOne()->getRecursive();
    }

    /**
     * It sets the recursivity to read/insert/update the information.<br>
     * The fields recursives are marked with the prefix '/'.  For example 'customer' is a single field (column), while
     * '/customer' is a relation. Usually, a relation has both fields and relation.
     * - If the relation is manytoone, then the query is joined with the table indicated in the relation. Example:<br>
     * <pre>
     * ProductRepo::setRecursive(['/Category'])::toList(); // select .. from Producto inner join Category on ..
     * </pre>
     * - If the relation is onetomany, then it creates an extra query (or queries) with the corresponding values.
     * Example:<br>
     * <pre>
     * CategoryRepo::setRecursive(['/Product'])::toList(); // select .. from Category and select from Product where..
     * </pre>
     * - If the reation is onetoone, then it is considered as a manytoone, but it returns a single value. Example:<br>
     * <pre>
     * ProductRepo::setRecursive(['/ProductExtension'])::toList(); // select .. from Product inner join ProductExtension
     * </pre>
     * - If the relation is manytomany, then the system load the relational table (always, not matter the recursivity),
     * and it reads/insert/update the next values only if the value is marked as recursive. Example:<br>
     * <pre>
     * ProductRepo::setRecursive(['/product_x_category'])::toList(); // it returns porduct, productxcategory and category
     * ProductRepo::setRecursive([])->toList(); // it returns porduct and productxcategory (if /productcategory is marked as
     * manytomany)
     * </pre>
     *
     *
     * @param array $recursive An indexed array with the recursivity.
     *
     * @return self
     * @see static::getDefFK for where to define the relation.
     */
    public static function setRecursive($recursive)
    {
        self::getPdoOne()->recursive($recursive);
        return static::ME;
    }

    /**
     * The next operation (in the chain of function) must be cached<br>
     * <b>Example</b>
     * <pre>
     * self::useCache(5000,'city')->toList();
     * </pre>
     *
     * @param null   $ttl
     * @param string $family
     *
     * @return self
     */
    public static function useCache($ttl = null, $family = '')
    {
        self::getPdoOne()->useCache($ttl, $family);
        self::$useCache = $ttl;
        return static::ME;
    }

    /**
     * It invalidates a family/group of cache<br>
     * <b>Example</b>
     * <pre>
     * $list=CityRepo::useCache(50000,'city')->toList(); // using the cache
     * CityRepo::invalidateCache('city')->insert($city); // inserting a new value & flushing cache
     * $list=CityRepo::useCache(50000,'city')->toList(); // not using the cache
     * </pre>
     *
     * @param string $family The family/grupo of cache(s) to invalidate. If empty or null, then it invalidates the
     *                       current table and all recursivity (if any)
     *
     * @return self
     */
    public static function invalidateCache($family = '')
    {
        if (self::getPdoOne()->getCacheService() !== null) {
            if (!$family) {
                self::getPdoOne()->getCacheService()->invalidateCache('', self::getRecursiveClass());
            } else {
                self::getPdoOne()->invalidateCache('', $family);
            }
        }

        return static::ME;
    }

    /**
     * @param array $rows The associative arrays with values to convert.
     * @param bool  $list false for a single row, or true for a list of rows.
     */
    protected static function convertSQLValueInit(&$rows, $list = false)
    {
        if (!$list) {
            $rows = [$rows];
        }
        $defs = static::getDef('conversion');
        $ns = self::getNamespace();
        $postfix = self::getPostfix();
        $rels = static::getDefFK();

        foreach ($rows as &$row) {
            self::convertSQLValue($row, $defs);
            foreach ($rels as $k => $v) {
                if (isset($row[$k])) {
                    switch ($v['key']) {
                        // PARENT not because parent is a fk but is used for a one way relation.
                        case 'MANYTOONE':
                            $class = $ns . PdoOne::camelize($v['reftable']) . $postfix;
                            $class::convertSQLValueInit($row[$k], false);
                            break;
                        case 'ONETOMANY':
                            $class = $ns . PdoOne::camelize($v['reftable']) . $postfix;
                            $class::convertSQLValueInit($row[$k]);
                            break;
                        case 'MANYTOMANY':
                            $class = $ns . PdoOne::camelize($v['table2']) . $postfix;
                            $class::convertSQLValueInit($row[$k]);
                            break;
                    }
                }
            }
        }


        if (!$list) {
            $rows = $rows[0];
        }
    }

    /**
     * It converts a row (obtained from the database), using an definition of conversions.<br>
     * <pre>
     * DATABASE ---> convertSQLValue() ---> PHP
     * </pre>
     * <b>Conversions:</b> datetime3 (human string), datetime2 (iso),datetime(class) timestamp (int), bool, int, float
     *
     * @param array $row  [ref] An associative array with the values to convert. This array is changed.
     * @param array $defs An associative array with the definition to convert.<br>
     *                    Example: $defs=static::getDef('conversion');
     */
    public static function convertSQLValue(&$row, $defs)
    {
        //$defs=static::getDef('conversion');

        foreach ($defs as $k => $v) { // 

            if ($v !== null && isset($row[$k])) {
                switch ($v) {
                    case 'datetime3':
                        $row[$k] = PdoOne::dateConvert($row[$k], 'sql', 'human');
                        break;
                    case 'datetime2':
                        $row[$k] = PdoOne::dateConvert($row[$k], 'sql', 'iso');
                        break;
                    case 'datetime':
                        $row[$k] = PdoOne::dateConvert($row[$k], 'sql', 'class');
                        break;
                    case 'timestamp':
                        $row[$k] = PdoOne::dateConvert($row[$k], 'sql', 'timestamp');
                        break;
                    case 'bool':
                        $row[$k] = ($row[$k]) ? true : false;
                        break;
                    case 'int':
                        $row[$k] = (int)$row[$k];
                        break;
                    case 'float':
                        $row[$k] = (float)$row[$k];
                        break;
                    default:
                        trigger_error(self::TABLE . " Conversion not defined [$v]");
                }
            }
        }
    }

    /**
     * It converts a row (to sends to the database), using an definition of conversions.<br>
     * <pre>
     * PHP ---> convertPHPValue() ---> DATABASE
     * </pre>
     * <b>Conversions:</b> datetime3 (human string), datetime2 (iso),datetime(class) timestamp (int), bool, int, float
     *
     * @param array $row  [ref] An associative array with the values to convert. This array is changed.
     * @param array $defs An associative array with the definition to convert.<br>
     *                    Example: $defs=static::getDef('conversion');
     */
    public static function convertPHPValue(&$row, $defs)
    {
        foreach ($defs as $k => $v) {
            if ($v !== null && isset($row[$k])) {
                switch ($v) {
                    case 'datetime3':
                        $row[$k] = PdoOne::dateConvert($row[$k], 'human', 'sql');
                        break;
                    case 'datetime2':
                        $row[$k] = PdoOne::dateConvert($row[$k], 'iso', 'sql');
                        break;
                    case 'datetime':
                        $row[$k] = PdoOne::dateConvert($row[$k], 'class', 'sql');
                        break;
                    case 'timestamp':
                        $row[$k] = PdoOne::dateConvert($row[$k], 'timestamp', 'sql');
                        break;
                    case 'bool':
                        $row[$k] = ($row[$k] === false) ? 0 : 1;
                        break;
                    case 'int':
                        $row[$k] = (int)$row[$k];
                        break;
                    case 'float':
                        $row[$k] = (float)$row[$k];
                        break;
                }
            }
        }
    }

    /**
     * It adds an "limit" in a query. It depends on the type of database<br>
     *
     * @param $sql
     *
     * @return self
     * @throws Exception
     */
    public static function limit($sql)
    {
        self::getPdoOne()->limit($sql);
        return static::ME;
    }

    /**
     * @param $order
     *
     * @return self
     */
    public static function order($order)
    {
        self::getPdoOne()->order($order);
        return static::ME;
    }

    /**
     * @param        $sql
     * @param string $condition
     *
     * @return self
     */
    public static function innerjoin($sql, $condition = '')
    {
        self::getPdoOne()->innerjoin($sql, $condition);
        return static::ME;
    }

    /**
     * @param $sql
     *
     * @return self
     */
    public static function left($sql)
    {
        self::getPdoOne()->left($sql);
        return static::ME;
    }

    /**
     * @param string $sql
     *
     * @return self
     */
    public static function right($sql)
    {
        self::getPdoOne()->right($sql);
        return static::ME;
    }

    /**
     * @param string $sql
     *
     * @return self
     */
    public static function group($sql)
    {
        self::getPdoOne()->group($sql);
        return static::ME;
    }

    /**
     * @param array|string   $sql =static::factory()
     * @param null|array|int $param
     *
     * @return static
     */
    public static function where($sql, $param = PdoOne::NULL)
    {
        self::getPdoOne()->where($sql, $param);
        return static::ME;
    }

    /**
     * It returns the number of rows
     *
     * @param null|array $where =static::factory()
     *
     * @return int
     * @throws Exception
     */
    public static function count($where = null)
    {
        $pdoOne = self::getPdoOne();
        if (self::$useCache && $pdoOne->getCacheService() !== null) {
            self::$uid = $pdoOne->buildUniqueID([$where], static::TABLE . '::count');
            $getCache = $pdoOne->getCacheService()->getCache(self::$uid, static::TABLE);
            if ($getCache !== false) {
                self::reset();
                return $getCache;
            }
            $recursiveClass = static::getRecursiveClass();
            $usingCache = true;
        } else {
            $recursiveClass = null;
            $usingCache = false;
        }
        $rowc = self::getPdoOne()->count()->from(static::TABLE)->where($where)->firstScalar();

        if ($rowc !== false && $usingCache) {
            $pdoOne->getCacheService()->setCache(self::$uid, $recursiveClass, (int)$rowc, self::$useCache);
            self::reset();
        }
        return $rowc;
    }

    /**
     * @param $sql
     * @param $param
     *
     * @return self
     */
    public function having($sql, $param = self::NULL)
    {
        self::getPdoOne()->having($sql, $param);
        return static::ME;
    }

    /**
     * It resets the stack
     */
    protected static function reset()
    {
        self::$useCache = false;
        self::$uid = null;
        self::$cacheFamily = '';
        self::$gQueryCounter = 0;
        self::$gQuery = [];
        self::$falseOnError = false;
        self::$lastException = '';
    }

}