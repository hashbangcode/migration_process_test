<?php

namespace Drupal\Tests\migration_process_test\Unit\Plugin\migrate\process;

use Drupal\migration_process_test\Plugin\migrate\process\ReformatTitle;
use Drupal\Tests\migrate\Unit\process\MigrateProcessTestCase;

/**
 * Tests the reformat_title migration plugin.
 */
class ReformatTitleTest extends MigrateProcessTestCase {

  /**
   * Test that different title values reformat correctly.
   *
   * @dataProvider titleIsReformattedDataProvider
   */
  public function testTitleIsReformatted($sourceValue, $expectedResult) {
    $plugin = new ReformatTitle([], 'reformat_title', []);
    $value = $plugin->transform($sourceValue, $this->migrateExecutable, $this->row, 'title');
    $this->assertEquals($expectedResult, $value);
  }

  /**
   * Data provider for testTitleIsReformatted.
   *
   * @return array
   *   The data to be tested.
   */
  public function titleIsReformattedDataProvider() {
    return [
      [
        '<p>About us page</p>',
        'About Us',
      ],
      [
        '<p>contact Us page</p>',
        'Contact Us',
      ],
    ];
  }

}
