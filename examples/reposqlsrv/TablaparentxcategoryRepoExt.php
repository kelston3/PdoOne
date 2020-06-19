<?php
/** @noinspection PhpIncompatibleReturnTypeInspection
 * @noinspection ReturnTypeCanBeDeclaredInspection
 * @noinspection DuplicatedCode
 * @noinspection PhpUnused
 * @noinspection PhpUndefinedMethodInspection
 * @noinspection PhpUnusedLocalVariableInspection
 * @noinspection PhpUnusedAliasInspection
 * @noinspection NullPointerExceptionInspection
 * @noinspection SenselessProxyMethodInspection
 * @noinspection PhpParameterByRefIsNotUsedAsReferenceInspection
 */
namespace repo;
use eftec\PdoOne;
use Exception;

/**
 * Generated by PdoOne Version 1.48 Date generated Fri, 19 Jun 2020 09:11:35 -0400. 
 * DO NOT EDIT THIS CODE. Use instead the Repo Class.
 * @copyright (c) Jorge Castro C. MIT License  https://github.com/EFTEC/PdoOne
 * Class TablaparentxcategoryRepo
 * <pre>
 * $code=$pdoOne->generateCodeClass('tablaparentxcategory','repo',array(),array('tablaparent'=>'TablaParentRepo','tablaparent_ext'=>'TablaParentExtRepo','tablachild'=>'TablachildRepo','tablaparentxcategory'=>'TablaparentxcategoryRepo','tablacategory'=>'TablacategoryRepo','tablagrandchildcat'=>'TablagrandchildcatRepo','tablagrandchild'=>'TablagrandchildRepo',),array(),'','','TestDb');
 * </pre>
 */
class TablaparentxcategoryRepoExt extends TestDb
{
    const TABLE = 'tablaparentxcategory';
    const PK = [
	    'idtablaparentPKFK',
	    'idcategoryPKFK'
	];
    const ME=__CLASS__;

    /**
     * It returns the definitions of the columns<br>
     * <b>Example:</b><br>
     * <pre>
     * self::getDef(); // ['colName'=>[php type,php conversion type,type,size,nullable,extra,sql],'colName2'=>..]
     * self::getDef('sql'); // ['colName'=>'sql','colname2'=>'sql2']
     * self::getDef('identity',true); // it returns the columns that are identities ['col1','col2']
     * </pre>
     * <b>PHP Types</b>: binary, date, datetime, decimal,int, string,time, timestamp<br>
     * <b>PHP Conversions</b>: datetime3 (human string), datetime2 (iso), timestamp (int), bool, int, float<br>
     * <b>Param Types</b>: PDO::PARAM_LOB, PDO::PARAM_STR, PDO::PARAM_INT<br>
     *
     * @param string|null $column =['phptype','conversion','type','size','null','identity','sql'][$i]
     *                             if not null then it only returns the column specified.
     * @param string|null $filter If filter is not null, then it uses the column to filter the result.
     *
     * @return array|array[]
     */
    public static function getDef($column=null,$filter=null) {
       $r = [
		    'idtablaparentPKFK' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => FALSE,
		        'identity' => FALSE,
		        'sql' => 'int NOT NULL'
		    ],
		    'idcategoryPKFK' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => FALSE,
		        'identity' => FALSE,
		        'sql' => 'int NOT NULL'
		    ]
		];
       if($column!==null) {
            if($filter===null) {
                foreach($r as $k=>$v) {
                    $r[$k]=$v[$column];
                }
            } else {
                $new=[];
                foreach($r as $k=>$v) {
                    if($v[$column]===$filter) {
                        $new[]=$k;
                    }
                }
                return $new;
            }
        }
        return $r;
    }

    /**
     * It gets all the name of the columns.
     *
     * @return string[]
     */
    public static function getDefName() {
        return [
		    'idtablaparentPKFK',
		    'idcategoryPKFK'
		];
    }

    /**
     * It returns an associative array (colname=>key type) with all the keys/indexes (if any)
     *
     * @return string[]
     */
    public static function getDefKey() {
        return [
		    'idtablaparentPKFK' => 'PRIMARY KEY',
		    'idcategoryPKFK' => 'KEY'
		];
    }

    /**
     * It returns a string array with the name of the columns that are skipped when insert
     * @return string[]
     */
    public static function getDefNoInsert() {
        return [

		];
    }

    /**
     * It returns a string array with the name of the columns that are skipped when update
     * @return string[]
     */
    public static function getDefNoUpdate() {
        return [

		];
    }

    /**
     * It adds a where to the income query. It could be stacked with more where()<br>
     * <b>Example:</b><br>
     * <pre>
     * self::where(['col'=>'value'])::toList();
     * self::where(['col']=>['s','value'])::toList(); // s= string/double/date, i=integer, b=bool
     * self::where(['col=?']=>['s','value'])::toList(); // s= string/double/date, i=integer, b=bool
     * </pre>
     * 
     * @param array|string   $sql =self::factory()
     * @param null|array|int $param
     *
     * @return self
     */
    public static function where($sql, $param = PdoOne::NULL)
    {
        self::getPdoOne()->where($sql, $param);
        return self::ME;
    }

    public static function getDefFK($structure=false) {
        if ($structure) {
            return [
			    'idcategoryPKFK' => 'FOREIGN KEY REFERENCES [tablacategory]([IdTablaCategoryPK])',
			    'idtablaparentPKFK' => 'FOREIGN KEY REFERENCES [tablaparent]([idtablaparentPK])'
			];
        }
        /* key,refcol,reftable,extra */
        return [
		    'idcategoryPKFK' => [
		        'key' => 'FOREIGN KEY',
		        'refcol' => 'IdTablaCategoryPK',
		        'reftable' => 'tablacategory',
		        'extra' => '',
		        'name' => 'tablaparentxcategory_fk2'
		    ],
		    '/idcategoryPKFK' => [
		        'key' => 'MANYTOONE',
		        'refcol' => 'IdTablaCategoryPK',
		        'reftable' => 'tablacategory',
		        'extra' => '',
		        'name' => 'tablaparentxcategory_fk2'
		    ],
		    'idtablaparentPKFK' => [
		        'key' => 'FOREIGN KEY',
		        'refcol' => 'idtablaparentPK',
		        'reftable' => 'tablaparent',
		        'extra' => '',
		        'name' => 'FK_tablaparentxcategory_tablaparent'
		    ],
		    '/idtablaparentPKFK' => [
		        'key' => 'ONETOONE',
		        'refcol' => 'idtablaparentPK',
		        'reftable' => 'tablaparent',
		        'extra' => '',
		        'name' => 'FK_tablaparentxcategory_tablaparent'
		    ]
		];
    }
    public static function toList($filter=null,$filterValue=null) {
        return self::_toList($filter,$filterValue);
    }
    
    /**
     * @param array $recursive=self::factory();
     *
     * @return TablaparentxcategoryRepo
     * {@inheritDoc}
     */
    public static function setRecursive($recursive)
    {
        return parent::setRecursive($recursive); 
    }

    /**
     * It returns the first row of a query.
     * @param array|mixed|null $pk [optional] Specify the value of the primary key.
     *
     * @return array|bool
     * @throws Exception
     */
    public static function first($pk = null) {
        return self::_first($pk);
    }

    /**
     *  It returns true if the entity exists, otherwise false.<br>
     *  <b>Example:</b><br>
     *  <pre>
     *  $this->exist(['id'=>'a1','name'=>'name']); // using an array
     *  $this->exist('a1'); // using the primary key. The table needs a pks and it only works with the first pk.
     *  </pre>
     *
     * @param array|mixed $entity =self::factory()
     *
     * @return bool true if the pks exists
     * @throws Exception
     */
    public static function exist($entity) {
        return self::_exist($entity);
    }

    /**
     * It inserts a new entity(row) into the database<br>
     * @param array $entity        =self::factory()
     * @param bool  $transactional If true (default) then the operation is transactional
     *
     * @return array|false=self::factory()
     * @throws Exception
     */
    public static function insert(&$entity,$transactional=true) {
        return self::_insert($entity,$transactional);
    }
    
    /**
     * It merge a new entity(row) into the database. If the entity exists then it is updated, otherwise the entity is 
     * inserted<br>
     * @param array $entity        =self::factory()
     * @param bool  $transactional If true (default) then the operation is transactional   
     *
     * @return array|false=self::factory()
     * @throws Exception
     */
    public static function merge(&$entity,$transactional=true) {
        return self::_merge($entity,$transactional);
    }

    /**
     * @param array $entity        =self::factory()
     * @param bool  $transactional If true (default) then the operation is transactional
     *
     * @return array|false=self::factory()
     * @throws Exception
     */
    public static function update($entity,$transactional=true) {
        return self::_update($entity,$transactional);
    }

    /**
     * It deletes an entity by the primary key
     *
     * @param array $entity =self::factory()
     * @param bool  $transactional If true (default) then the operation is transactional   
     *
     * @return mixed
     * @throws Exception
     */
    public static function delete($entity,$transactional=true) {
        return self::_delete($entity,$transactional);
    }

    /**
     * It deletes an entity by the primary key.
     *
     * @param array $pk =self::factory()
     * @param bool  $transactional If true (default) then the operation is transactional   
     *
     * @return mixed
     * @throws Exception
     */
    public static function deleteById($pk,$transactional=true) {
        return self::_deleteById($pk,$transactional);
    }

    public static function factory() {
        $recursive=static::getRecursive();
        return [
		'idtablaparentPKFK'=>0,
		'/idtablaparentPKFK'=>(in_array('/idtablaparentPKFK',$recursive)) 
		                            ? tablaparentRepo::factory() 
		                            : null, /* manytoone */
		'idcategoryPKFK'=>0,
		'/idcategoryPKFK'=>(in_array('/idcategoryPKFK',$recursive)) 
		                            ? tablacategoryRepo::factory() 
		                            : null, /* manytoone */
		];
    }
    public static function factoryNull() {
        return [
		'idtablaparentPKFK'=>null,
		'/idtablaparentPKFK'=>null, /* manytoone */
		'idcategoryPKFK'=>null,
		'/idcategoryPKFK'=>null, /* manytoone */
		];
    }

}