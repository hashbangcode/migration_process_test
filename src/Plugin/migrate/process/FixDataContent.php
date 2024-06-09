<?php

namespace Drupal\migration_process_test\Plugin\migrate\process;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Fix any broken markup in the source field.
 *
 * @code
 * body/0/value:
 *   plugin: fix_data_content
 * @endcode
 *
 * @MigrateProcessPlugin(id = "fix_data_content")
 */
class FixDataContent extends ProcessPluginBase implements ContainerFactoryPluginInterface {

  /**
   * The config factory object.
   *
   * @var \Drupal\Core\Config\ImmutableConfig
   */
  protected ImmutableConfig $siteConfig;

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    $instance = new self($configuration, $plugin_id, $plugin_definition);

    $instance->siteConfig = $container->get('config.factory')->get('system.site');

    return $instance;
  }

  /**
   * {@inheritDoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if ($value === NULL) {
      return $value;
    }

    // Strip any empty elements.
    $value = preg_replace('/<span>\s*<\/span>/', '', $value);
    $value = preg_replace('/<p>\s*<\/p>/', '', $value);

    // Replace all instance of example@example.com with our site email.
    $value = preg_replace('/example@example.com/', $this->siteConfig->get('mail'), $value);

    return $value;
  }

}
