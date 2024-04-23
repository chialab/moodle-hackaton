<?php

// Every file should have GPL and copyright in the header - we skip it in tutorials but you should not skip it for real.

// This line protects the file from being accessed by a URL directly.
defined('MOODLE_INTERNAL') || die();

function theme_albe_get_pre_scss($theme) {
  global $CFG;
  return file_get_contents($CFG->dirroot . '/theme/albe/scss/preset/default.scss');
}

// Function to return the SCSS to append to our main SCSS for this theme.
// Note the function name starts with the component name because this is a global function and we don't want namespace clashes.
function theme_albe_get_extra_scss($theme) {
  // Load the settings from the parent.
  $theme = theme_config::load('boost');
  // Call the parent themes get_extra_scss function.
  return theme_boost_get_extra_scss($theme);
}

function theme_albe_get_main_scss_content($theme) {
  global $CFG;

  $filename = !empty($theme->settings->preset) ? $theme->settings->preset : null;

  return file_get_contents($CFG->dirroot . '/theme/boost/scss/preset/default.scss');
}