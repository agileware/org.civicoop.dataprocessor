<?php
/**
 * @author Jaap Jansma <jaap.jansma@civicoop.org>
 * @license AGPL-3.0
 */

namespace Civi\DataProcessor\DataFlow\MultipleDataFlows;

use Civi\DataProcessor\DataFlow\AbstractDataFlow;
use Civi\DataProcessor\DataFlow\CombinedDataFlow\CombinedSqlDataFlow;
use Civi\DataProcessor\DataFlow\CombinedDataFlow\SubqueryDataFlow;
use Civi\DataProcessor\DataFlow\SqlDataFlow;
use Civi\DataProcessor\DataFlow\SqlTableDataFlow;
use Civi\DataProcessor\ProcessorType\AbstractProcessorType;
use Civi\DataProcessor\DataFlow\SqlDataFlow\WhereClauseInterface;

class SimpleNonRequiredJoin  extends  SimpleJoin {

  public function __construct($left_prefix = null, $left_field = null, $right_prefix = null, $right_field = null, $type = "LEFT") {
    parent::__construct($left_prefix, $left_field, $right_prefix, $right_field, $type);
  }

  /**
   * @param array $configuration
   *
   * @return \Civi\DataProcessor\DataFlow\MultipleDataFlows\JoinInterface
   */
  public function setConfiguration($configuration) {
    return parent::setConfiguration($configuration);
  }


}
