<?php

namespace WPS;

class FilterIterator extends \RecursiveFilterIterator {
  protected $file_prefixes;
  public static $file_filters = [
    '.gitkeep'
  ];

  public function accept() {
    if($this->hasChildren()) return true;

    if(!empty($this->file_prefixes)) {
      $filename = $this->current()->getFilename();

      return $this->is_accepted_file($filename) &&
        $this->is_prefixed_file($filename);
    }
  }

  private function is_accepted_file($filename) {
    return !in_array($filename, self::$file_filters);
  }

  private function is_prefixed_file($filename) {
    foreach($this->file_prefixes as $prefix) {
      if(strpos($filename, $prefix) !== false) return true;
    }

    return false;
  }
}
