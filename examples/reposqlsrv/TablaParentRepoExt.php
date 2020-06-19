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
 * Class TablaParentRepo
 * <pre>
 * $code=$pdoOne->generateCodeClass('tablaparent','repo',array('/idchild2FK'=>'PARENT','/tablaparentxcategory'=>'MANYTOMANY',),array('tablaparent'=>'TablaParentRepo','tablaparent_ext'=>'TablaParentExtRepo','tablachild'=>'TablachildRepo','tablaparentxcategory'=>'TablaparentxcategoryRepo','tablacategory'=>'TablacategoryRepo','tablagrandchildcat'=>'TablagrandchildcatRepo','tablagrandchild'=>'TablagrandchildRepo',),array(),'','','TestDb');
 * </pre>
 */
class TablaParentRepoExt extends TestDb
{
    const TABLE = 'tablaparent';
    const PK = [
	    'idtablaparentPK'
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
		    'idtablaparentPK' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => FALSE,
		        'identity' => TRUE,
		        'sql' => 'int NOT NULL IDENTITY(1,1)'
		    ],
		    'field1' => [
		        'phptype' => 'string',
		        'conversion' => NULL,
		        'type' => 'varchar',
		        'size' => '50',
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'varchar(50)'
		    ],
		    'idchildFK' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int DEFAULT (NULL)'
		    ],
		    'idchild2FK' => [
		        'phptype' => 'int',
		        'conversion' => NULL,
		        'type' => 'int',
		        'size' => NULL,
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'int DEFAULT (NULL)'
		    ],
		    'field2' => [
		        'phptype' => 'string',
		        'conversion' => NULL,
		        'type' => 'varchar',
		        'size' => '50',
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'varchar(50)'
		    ],
		    'fieldd' => [
		        'phptype' => 'decimal',
		        'conversion' => NULL,
		        'type' => 'decimal',
		        'size' => '9,2',
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'decimal(9,2)'
		    ],
		    'fielddef' => [
		        'phptype' => 'string',
		        'conversion' => NULL,
		        'type' => 'varchar',
		        'size' => '50',
		        'null' => TRUE,
		        'identity' => FALSE,
		        'sql' => 'varchar(50) DEFAULT (\'hi there\')'
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
		    'idtablaparentPK',
		    'field1',
		    'idchildFK',
		    'idchild2FK',
		    'field2',
		    'fieldd',
		    'fielddef'
		];
    }

    /**
     * It returns an associative array (colname=>key type) with all the keys/indexes (if any)
     *
     * @return string[]
     */
    public static function getDefKey() {
        return [
		    'idtablaparentPK' => 'PRIMARY KEY',
		    'idchildFK' => 'KEY',
		    'idchild2FK' => 'KEY',
		    'field2' => 'UNIQUE KEY'
		];
    }

    /**
     * It returns a string array with the name of the columns that are skipped when insert
     * @return string[]
     */
    public static function getDefNoInsert() {
        return [
		    'idtablaparentPK'
		];
    }

    /**
     * It returns a string array with the name of the columns that are skipped when update
     * @return string[]
     */
    public static function getDefNoUpdate() {
        return [
		    'idtablaparentPK'
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
			    'idchild2FK' => 'FOREIGN KEY REFERENCES [tablachild]([idtablachildPK])',
			    'idchildFK' => 'FOREIGN KEY REFERENCES [tablachild]([idtablachildPK])'
			];
        }
        /* key,refcol,reftable,extra */
        return [
		    'idchild2FK' => [
		        'key' => 'FOREIGN KEY',
		        'refcol' => 'idtablachildPK',
		        'reftable' => 'tablachild',
		        'extra' => '',
		        'name' => 'tablaParent_fk2'
		    ],
		    '/idchild2FK' => [
		        'key' => 'PARENT',
		        'refcol' => 'idtablachildPK',
		        'reftable' => 'tablachild',
		        'extra' => '',
		        'name' => 'tablaParent_fk2'
		    ],
		    'idchildFK' => [
		        'key' => 'FOREIGN KEY',
		        'refcol' => 'idtablachildPK',
		        'reftable' => 'tablachild',
		        'extra' => '',
		        'name' => 'tablaParent_fk1'
		    ],
		    '/idchildFK' => [
		        'key' => 'MANYTOONE',
		        'refcol' => 'idtablachildPK',
		        'reftable' => 'tablachild',
		        'extra' => '',
		        'name' => 'tablaParent_fk1'
		    ],
		    '/tablaparentxcategory' => [
		        'key' => 'MANYTOMANY',
		        'col' => 'idtablaparentPK',
		        'reftable' => 'tablaparentxcategory',
		        'refcol' => '/idtablaparentPKFK',
		        'refcol2' => '/idcategoryPKFK',
		        'col2' => 'IdTablaCategoryPK',
		        'table2' => 'tablacategory'
		    ],
		    '/tablaparent_ext' => [
		        'key' => 'ONETOONE',
		        'col' => 'idtablaparentPK',
		        'reftable' => 'tablaparent_ext',
		        'refcol' => '/idtablaparentExtPK'
		    ]
		];
    }
    public static function toList($filter=null,$filterValue=null) {
        return self::_toList($filter,$filterValue);
    }
    
    /**
     * @param array $recursive=self::factory();
     *
     * @return TablaParentRepo
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
		'idtablaparentPK'=>0,
		'/tablaparentxcategory'=>(in_array('/tablaparentxcategory',$recursive))
		                            ? [] 
		                            : null, /* onetomany */
		'/tablaparent_ext'=>(in_array('/tablaparent_ext',$recursive))
		                            ? [] 
		                            : null, /* onetomany */
		'field1'=>'',
		'idchildFK'=>0,
		'/idchildFK'=>(in_array('/idchildFK',$recursive)) 
		                            ? tablachildRepo::factory() 
		                            : null, /* manytoone */
		'idchild2FK'=>0,
		'/idchild2FK'=>(in_array('/idchild2FK',$recursive)) 
		                            ? tablachildRepo::factory() 
		                            : null, /* manytoone */
		'field2'=>'',
		'fieldd'=>0.0,
		'fielddef'=>''
		];
    }
    public static function factoryNull() {
        return [
		'idtablaparentPK'=>null,
		'/tablaparentxcategory'=>null, /* onetomany */
		'/tablaparent_ext'=>null, /* onetomany */
		'field1'=>null,
		'idchildFK'=>null,
		'/idchildFK'=>null, /* manytoone */
		'idchild2FK'=>null,
		'/idchild2FK'=>null, /* manytoone */
		'field2'=>null,
		'fieldd'=>null,
		'fielddef'=>null
		];
    }

}