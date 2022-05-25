<?php

namespace Drupal\ea_festivals\Commands;

use Drush\Commands\DrushCommands;
use Drupal\ea_festivals\Controller\FestivalsController;

/**
 * A drush command file.
 *
 * @package Drupal\ea_festivals\Commands
 */
class FestivalsCommands extends DrushCommands {
  /**
   * Drush command that displays the given text.
   *
   * Argument with message to be displayed.
   * @command ea_festivals:get_data
   * @aliases ea_festivals_data eafd
   * @usage ea_festivals
   */
  public function get_data() {
 
    //create a festivals instance
    $eafd = new FestivalsController;

    //calling festivals function to get data
    $eafd->get_festivals();
  }
}