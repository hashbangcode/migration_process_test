<?php

namespace Drupal\Tests\migration_process_test\Unit\Plugin\migrate\process;

use Drupal\KernelTests\KernelTestBase;

/**
 * Tests the fix_data_content migration plugin.
 *
 * @group migrate
 */
class FixDataContentTest extends KernelTestBase {

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'migrate',
    'migration_process_test',
    'system',
  ];

  /**
   * The migrate row object.
   *
   * @var \Drupal\migrate\Row|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $row;

  /**
   * The migrate executable.
   *
   * @var \Drupal\migrate\MigrateExecutable|\PHPUnit\Framework\MockObject\MockObject
   */
  protected $migrateExecutable;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $container = \Drupal::getContainer();

    // Update the site configuration with our test email address.
    \Drupal::service('config.factory')->getEditable('system.site');
    $system = $this->config('system.site');
    $system
      ->set('mail', 'test@example2.com')
      ->save();

    \Drupal::setContainer($container);

    // Create the test row and executable objects.
    $this->row = $this->getMockBuilder('Drupal\migrate\Row')
      ->disableOriginalConstructor()
      ->getMock();
    $this->migrateExecutable = $this->getMockBuilder('Drupal\migrate\MigrateExecutable')
      ->disableOriginalConstructor()
      ->getMock();
  }

  /**
   * Test that data content is fixed in the expected way.
   *
   * @dataProvider testFixDataContentFixesContentDataProvider
   */
  public function testFixDataContentFixesContent($sourceValue, $expectedResult) {
    // Create migration stub.
    $migration = \Drupal::service('plugin.manager.migration')
      ->createStubMigration([
        'id' => 'test',
        'source' => [],
        'process' => [],
        'destination' => [
          'plugin' => 'entity:node',
        ],
      ]);

    // Set plugin configuration.
    $configuration = [];

    // Generate the plugin via the plugin.manager.migrate.process service.
    $plugin = \Drupal::service('plugin.manager.migrate.process')
      ->createInstance('fix_data_content', $configuration, $migration);

    // Run the test.
    $value = $plugin->transform($sourceValue, $this->migrateExecutable, $this->row, 'field_body');
    $this->assertEquals($expectedResult, $value);
  }

  /**
   * Data provider for testFixDataContentFixesContent.
   *
   * @return array
   *   The data to be tested.
   */
  public function testFixDataContentFixesContentDataProvider() {
    return [
      [
        NULL,
        NULL,
      ],
      [
        '<p>Some text.</p>',
        '<p>Some text.</p>',
      ],
      [
        '<p>Some text.<span></span></p>',
        '<p>Some text.</p>',
      ],
      [
        '<p>Some text.</p><p></p>',
        '<p>Some text.</p>',
      ],
      [
        '<p>Some example@example.com.</p><p> </p>',
        '<p>Some test@example2.com.</p>',
      ],
    ];
  }

}
