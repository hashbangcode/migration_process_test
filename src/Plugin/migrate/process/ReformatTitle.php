<?php

namespace Drupal\migration_process_test\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Reformat the title of the page.
 *
 * @code
 * title:
 *   plugin: reformat_title
 *   source: title
 * @endcode
 *
 * @MigrateProcessPlugin(id = "reformat_title")
 */
class ReformatTitle extends ProcessPluginBase {

  /**
   * {@inheritDoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if ($value === NULL) {
      return $value;
    }

    // Strip any markup that the title might have.
    $value = strip_tags($value);

    // Strip any ending "page" words.
    $value = preg_replace('/\spage\s?$/', '', $value);

    // Make the string sentence case.
    $value = ucwords($value);

    return $value;
  }

}
