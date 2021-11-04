<?php

namespace Vojir\LeanMapper\Mappers;

use LeanMapper\Caller;
use LeanMapper\Helpers;
use LeanMapper\IMapper;

/**
 * Class CamelcaseUnderdashPluralMapper - mapper for conventions:
 *  - underdash separated names of tables and cols
 *  - PK and FK is in [table]_id format
 *  - entity repository is named [Entitie]Repository
 *  - M:N relations are stored in [table1]_[table2] tables
 * @package Vojir\LeanMapper\Mappers
 */
class CamelcaseUnderdashPluralMapper implements IMapper{

  /** @var string $defaultEntityNamespace */
  private $defaultEntityNamespace;

  /**
   * CamelcaseUnderdashPluralMapper constructor.
   * @param string $defaultEntityNamespace
   */
  public function __construct(string $defaultEntityNamespace = 'App\Model\Entity'){
    $this->defaultEntityNamespace = $defaultEntityNamespace;
  }

  /**
   * PK format [table]_id
   * @param string $table
   * @return string
   */
  public function getPrimaryKey(string $table):string {
    if (substr($table,-3)=='ies'){
      $table=substr($table,0,-3).'y';
    }elseif(substr($table,-1)=='s'){
      $table=substr($table,0,-1);
    }
    return $table.'_id';
  }

  /**
   * @param string $sourceTable
   * @param string $targetTable
   * @param string|null $relationshipName
   * @return string
   */
  public function getRelationshipColumn(string $sourceTable, string $targetTable, ?string $relationshipName = null):string {
    return ($relationshipName !== null ? self::camelToUnderdash($relationshipName).'_id' : $this->getPrimaryKey($targetTable));
  }

  /**
   * some_entities -> Model\Entity\SomeEntity
   * @param string $table
   * @param \LeanMapper\Row $row
   * @return string
   */
  public function getEntityClass(string $table, ?\LeanMapper\Row $row = null):string {
    if (substr($table,-3)=='ies'){
      $table=substr($table,0,-3).'y';
    }elseif(substr($table,-1)=='s'){
      $table=substr($table,0,-1);
    }
    return $this->defaultEntityNamespace.'\\'.ucfirst(self::underdashToCamel($table));
  }

  /**
   * Model\Entity\SomeEntity -> some_entities
   * @param string $entityClass
   * @return string
   */
  public function getTable(string $entityClass):string {
    $tableName=self::camelToUnderdash(self::trimNamespace($entityClass));
    switch (substr($tableName,-1)){
      case 'y': return substr($tableName, 0, -1).'ies';
      case 'a': return $tableName;
      default:  return $tableName.'s';
    }
  }

  /**
   * someField -> some_field
   * @param string $entityClass
   * @param string $field
   * @return string
   */
  public function getColumn(string $entityClass, string $field):string {
    return self::camelToUnderdash($field);
  }

  /**
   * some_field -> someField
   * @param string $table
   * @param string $column
   * @return string
   */
  public function getEntityField(string $table,string $column):string {
    return self::underdashToCamel($column);
  }

  /**
   * Model\Repository\SomeEntitiesRepository -> some_entity
   * @param string $repositoryClass
   * @return string
   * @throws \Exception
   */
  public function getTableByRepositoryClass(string $repositoryClass):string {
    $repositoryClass = self::trimNamespace($repositoryClass);
    if (preg_match('#([a-z0-9]+)Repository$#i', $repositoryClass, $matches)){
      return self::camelToUnderdash($matches[1]);
    }
    throw new \Exception('Invalid repository class name.');
  }

  /**
   * @inheritDoc
   */
  function getRelationshipTable(string $sourceTable, string $targetTable):string{
    return $sourceTable.'_'.$targetTable;
  }

  /**
   * @inheritDoc
   */
  function getImplicitFilters(string $entityClass, ?Caller $caller = null){
    return [];
  }

  /**
   * @inheritDoc
   */
  function convertToRowData(string $table, array $values):array {
    return $values;
  }

  /**
   * @inheritDoc
   */
  function convertFromRowData(string $table, array $data):array {
    return $data;
  }

  /**
   * Method for translation of names from camelCase to underdash notation
   * @param  string $s
   * @return string
   */
  public static function camelToUnderdash(string $s):string {
    $s = preg_replace('#(.)(?=[A-Z])#', '$1_', $s);
    $s = strtolower($s);
    $s = rawurlencode($s);
    return $s;
  }

  /**
   * Method for translation of names from underdash notation to camelCase
   * @param  string $s
   * @return string
   */
  public static function underdashToCamel(string $s):string {
    $s = strtolower($s);
    $s = preg_replace('#_(?=[a-z])#', ' ', $s);
    $s = substr(ucwords('x' . $s), 1);
    $s = str_replace(' ', '', $s);
    return $s;
  }

  private static function trimNamespace(string $className):string {
    return Helpers::trimNamespace($className);
  }
}