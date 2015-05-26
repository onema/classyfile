<?php
/**
 * Comments, copyright, license, etc....
 */
require_once "I/should/not/be/requiring/files/in/my/classes.php";

if (!class_exists("Date", false)) {
  /**
   * Represents a date.
   */
  class Date {

    const SOME_NAMESPACE = "https://some.endpoint.com/api/v1";

    /**
     * @var int
     */
    public $year;

    /**
     * @var int
     */
    public $month;

    /**
     * @var int
     */
    public $day;

    /**
     * Gets the namespace of this class
     * @return string namespace of this class
     */
    public function getNamespace() {
      return self::SOME_NAMESPACE;
    }

    public function __construct($year = null, $month = null, $day = null) {
      $this->year = $year;
      $this->month = $month;
      $this->day = $day;
    }

  }
}

if (!class_exists("DateRange", false)) {
  /**
   * Class description
   */
  class DateRange {
    const SOME_NAMESPACE = "https://some.endpoint.com/api/v1";

    /**
     * @var Date
     */
    public $min;

    /**
     * @var Date
     */
    public $max;

    /**
     * Gets the namespace of this class
     * @return string namespace of this class
     */
    public function getNamespace() {
      return self::WSDL_NAMESPACE;
    }

    public function __construct($min = null, $max = null) {
      $this->min = $min;
      $this->max = $max;
    }

  }
}

if (!class_exists("OrderBy", false)) {
  /**
   * Class description.
   */
  class OrderBy {

    const SOME_NAMESPACE = "https://some.endpoint.com/api/v1";

    /**
     * @var string
     */
    public $field;

    /**
     * @var string
     */
    public $sortOrder;

    /**
     * Gets the namesapce of this class
     * @return string namespace of this class
     */
    public function getNamespace() {
      return self::SOME_NAMESPACE;
    }

    public function __construct($field = null, $sortOrder = null) {
      $this->field = $field;
      $this->sortOrder = $sortOrder;
    }

  }
}