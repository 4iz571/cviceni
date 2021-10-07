<?php

namespace App\Model\Mappers;
use Nette\Utils\Strings;


/**
 * Standard mapper for conventions:
 * - underdash separated names of tables and cols
 * - PK and FK is in [table]_id format
 * - entity repository is named [Entity]Repository
 * - M:N relations are stored in [table1]_[table2] tables
 *
 * @author Jan Nedbal, Stanislav Vojíř
 * @noinspection PhpFullyQualifiedNameUsageInspection
 */
class StandardMapper extends \LeanMapper\DefaultMapper {

  /**
   * PK format [table]_id
   * @param string $table
   * @return string
   */
  public function getPrimaryKey(string $table):string {
    if (Strings::endsWith($table,'ies')){
      return substr($table,0,strlen($table)-3)."y_id";
    }elseif(Strings::endsWith($table,'a')){
      return $table."_id";
    }else{
      return substr($table,0,strlen($table)-1)."_id";
    }
  }

  /**
   * @param string $sourceTable
   * @param string $targetTable
   * @param string|null $relationshipName
   * @return string
   */
  public function getRelationshipColumn(string $sourceTable, string $targetTable, ?string $relationshipName = null):string{
    return ($relationshipName !== null ? $relationshipName.'_id' : $this->getPrimaryKey($targetTable));
  }

  /**
   * some_entity -> Model\Entity\SomeEntity
   * @param string $table
   * @param \LeanMapper\Row $row
   * @return string
   */
  public function getEntityClass(string $table, ?\LeanMapper\Row $row = null):string {
    if (Strings::endsWith($table,'ies')){
      return $this->defaultEntityNamespace . '\\' . ucfirst($this->underdashToCamel(substr($table,0,strlen($table)-3).'y'));
    }elseif(Strings::endsWith($table,'a')){
      return $this->defaultEntityNamespace . '\\' . ucfirst($this->underdashToCamel($table));
    }else{
      return $this->defaultEntityNamespace . '\\' . ucfirst($this->underdashToCamel(substr($table,0,strlen($table)-1)));
    }
  }

  /**
   * Model\Entity\SomeEntity -> some_entity
   * @param string $entityClass
   * @return string
   */
  public function getTable(string $entityClass):string {
    if (Strings::endsWith($entityClass,'y')){
      return $this->camelToUnderdash(Strings::substring($this->trimNamespace($entityClass),0,Strings::length($entityClass))) .'ies';
    }elseif(Strings::endsWith($entityClass,'a')){
      return $this->camelToUnderdash($this->trimNamespace($entityClass));
    }else{
      return $this->camelToUnderdash($this->trimNamespace($entityClass)).'s';
    }
  }

  /**
   * someField -> some_field
   * @param string $entityClass
   * @param string $field
   * @return string
   */
  public function getColumn(string $entityClass, string $field):string {
    return $this->camelToUnderdash($field);
  }

  /**
   * some_field -> someField
   * @param string $table
   * @param string $column
   * @return string
   */
  public function getEntityField(string $table,string $column):string {
    return $this->underdashToCamel($column);
  }

  /**
   * Model\Repository\SomeEntityRepository -> some_entity
   * @param string $repositoryClass
   * @return string
   */
  public function getTableByRepositoryClass(string $repositoryClass):string {
    $class = preg_replace('#([a-z0-9]+)iesRepository$#', '$1ie', $repositoryClass);
    $class = preg_replace('#([a-z0-9]+)sRepository$#', '$1', $class);
    $class = preg_replace('#([a-z0-9]+)aRepository$#', '$1a', $class);
    $class=$this->trimNamespace($class);
    return $this->camelToUnderdash(Strings::endsWith($class,'a')?$class:$class.'s');
  }

  /**
   * camelCase -> underdash_separated.
   * @param  string
   * @return string
   */
  protected function camelToUnderdash($s) {
    $s = preg_replace('#(.)(?=[A-Z])#', '$1_', $s);
    $s = strtolower($s);
    $s = rawurlencode($s);
    return $s;
  }

  /**
   * underdash_separated -> camelCase
   * @param  string
   * @return string
   */
  protected function underdashToCamel($s) {
    $s = strtolower($s);
    $s = preg_replace('#_(?=[a-z])#', ' ', $s);
    $s = substr(ucwords('x' . $s), 1);
    $s = str_replace(' ', '', $s);
    return $s;
  }

}