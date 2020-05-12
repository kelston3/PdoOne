<?php
/** @noinspection PhpUnused */
namespace repo;
use eftec\PdoOne;
use eftec\_BasePdoOneRepo;

/**
 * Generated by PdoOne Version 1.37
 * @copyright (c) Jorge Castro C. MIT License  https://github.com/EFTEC/PdoOne 
 * Class TablagrandchildcatRepo
 */
class TablagrandchildcatRepo extends _BasePdoOneRepo
{
    const TABLE = 'tablagrandchildcat';
    const PK = [
	    'IdTablaGrandChildCatPK'
	];
    const ME=__CLASS__;   
    
    public static function getDef($onlyKeys=false) {
        $r= [
		    'IdTablaGrandChildCatPK' => 'int NOT NULL IDENTITY(1,1)',
		    'Name' => 'varchar(50) DEFAULT (NULL)',
		    'IdgrandchildFK' => 'int DEFAULT (NULL)'
		];
        return ($onlyKeys)? array_keys($r): $r;
    }
    
    /**
     * It returns an associative array (colname=>key type) with all the keys/indexes (if any)
     * 
     * @return string[]
     */    
    public static function getDefKey() {
        return [
		    'IdTablaGrandChildCatPK' => 'PRIMARY KEY',
		    'IdgrandchildFK' => 'KEY'
		];
    }
    public static function getDefIdentity() {
        return [
		    'IdTablaGrandChildCatPK'
		];
    }
    public static function getDefFK($structure=false) {
        if ($structure) {
            return [
			    'IdgrandchildFK' => 'FOREIGN KEY REFERENCES tablagrandchild(idgrandchildPK)'
			];
        }
        /* key,refcol,reftable,extra */
        return [
		    'IdgrandchildFK' => [
		        'key' => 'FOREIGN KEY',
		        'refcol' => 'idgrandchildPK',
		        'reftable' => 'tablagrandchild',
		        'extra' => ''
		    ],
		    '/IdgrandchildFK' => [
		        'key' => 'MANYTOONE',
		        'refcol' => 'idgrandchildPK',
		        'reftable' => 'tablagrandchild',
		        'extra' => NULL
		    ]
		];
    }
    public static function toList($filter=null,$filterValue=null) {
        return self::_toList($filter,$filterValue);
    }
    public static function first($pk = null) {
        return self::_first($pk);
    }
    public static function insert($entity) {
        return self::_insert($entity);
    }
    public static function update($entity) {
        return self::_update($entity);
    }
    public static function delete($filter=null,$filterValue=null) {
        return self::_delete($filter,$filterValue);
    }
    public static function deleteById($pk) {
        return self::_deleteById($pk);
    }  
    

    public static function factory() {
        $recursive=static::getRecursive();
        return [
		'IdTablaGrandChildCatPK'=>0,
		'Name'=>'',
		'IdgrandchildFK'=>0,
		'/IdgrandchildFK'=>(in_array('/IdgrandchildFK',$recursive)) 
		                            ? tablagrandchildRepo::factory() 
		                            : null, /* manytoone */
		];
    }
    public static function factoryNull() {
        return [
		'IdTablaGrandChildCatPK'=>null,
		'Name'=>null,
		'IdgrandchildFK'=>null,
		'/IdgrandchildFK'=>null, /* manytoone */
		];
    }     
     
}