<?php
/**
* Basic
* Micro framework em PHP
*/

namespace Basic;

use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Common\Type;
use Box\Spout\Writer\WriterFactory;

/**
* Classe Sheet
*/
class Sheet
{
  /**
  * Converte letras para números
  */
  public function alphaToNum($a)
  {
    $l = strlen($a);
    $n = 0;
    for ($i = 0; $i < $l; $i++) {
      $n = $n*26 + ord($a[$i]) - 0x60;
    }
    return $n-1;
  }
  /**
  * Converte número para letra
  */
  public function numToAlpha($n)
  {
    for ($r = ""; $n >= 0; $n = intval($n / 26) - 1) {
      $r = chr($n%26 + 0x61) . $r;
    }
    return $r;
  }
  /**
  * Converte as chaves numéricas da planilha para chaves alfabéticas
  */
  public function sheetToAlpha($sheet)
  {
    $fixed_sheet=false;
    foreach ($sheet as $key => $value) {
      foreach ($value as $value_key => $value_value) {
        unset($value[$value_key]);
        $value[$this->numToAlpha($value_key)]=$value_value;
      }
      unset($sheet[$key]);
      $fixed_sheet[$key+1]=$value;
    }
    return $fixed_sheet;
  }
  /**
  * Converte uma planilha para array
  */
  public function toArray($sheet_name,$ext=false)
  {
    if(!$ext){
      $ext=pathinfo($sheet_name, PATHINFO_EXTENSION);
    }
    switch ($ext) {
      case 'csv':
      $reader = ReaderFactory::create(Type::CSV);
      break;
      case 'ods':
      $reader = ReaderFactory::create(Type::ODS);
      break;
      case 'xlsx':
      $reader = ReaderFactory::create(Type::XLSX);
      break;
    }
    $reader->open($sheet_name);
    foreach ($reader->getSheetIterator() as $sheet) {
      $sheetName = $sheet->getName();
      foreach ($sheet->getRowIterator() as $row) {
        $list['lists'][$sheetName][]=$row;
      }
    }
    $reader->close();
    $lists=false;
    foreach ($list['lists'] as $key => $value) {
      $lists[$key]=$this->sheetToAlpha($value);
    }
    if($ext=="csv"){
      return $lists[""];
    }else{
      return $lists;
    }
  }
  /**
  * Converte um array de dados para uma planilha
  */
  public function toSheet($array, $sheet_name, $ext=false)
  {
    if(!$ext){
      $ext=pathinfo($sheet_name, PATHINFO_EXTENSION);  
    }
    switch ($ext) {
      case 'csv':
      $writer = WriterFactory::create(Type::CSV);
      break;
      case 'ods':
      $writer = WriterFactory::create(Type::ODS);
      break;
      case 'xlsx':
      $writer = WriterFactory::create(Type::XLSX);
      break;
    }
    @$writer->openToFile($sheet_name);
    foreach ($array as $row) {
      $writer->addRow($row);
    }
    if (is_null($writer->close())) {
      return true;
    } else {
      return false;
    }
  }
}
