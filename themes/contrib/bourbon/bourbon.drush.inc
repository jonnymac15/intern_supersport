<?php
function bourbon_drush_command(){
  $items = array();

  $items['bourbon-sub-theme'] = array(
    'aliases' => array('bnew'),
    'description' => 'Creates a new sub theme based on bourbon theme.',
    'callback' => 'bourbon_create_subtheme',
  );

  $items['bourbon-watch'] = array(
    'aliases' => array('bwatch'),
    'description' => 'Watches theme files for changes.',
    'callback' => 'bourbon_watch',
  );

  return $items;
}


function bourbon_create_subtheme() {
  return;
}

function bourbon_watch() {
  $theme_name = bourbon_choose_theme();
  $path = bourbon_get_theme_path($theme_name);
  drush_shell_cd_and_exec(DRUPAL_ROOT . '/' . $path, 'gulp');
  reset(drush_shell_exec_output());
}

function bourbon_choose_theme() {
  //Drupal 8 specific way to get a list of all themes available.
  $themes = array_keys(\Drupal::service('theme_handler')->listInfo());
  foreach($themes as $key => $theme) {
    $options[$key] = $theme;
  }
  $message = 'Please choose a theme to watch:';
  $theme_name = drush_choice($options, $message);
  return $theme_name;
}

function bourbon_get_theme_path($name) {
  $theme_path = drupal_get_path('theme', $name);
  return $theme_path;
}
