<?php

/**
 * @file
 * Theme and preprocess functions for Media: YouTube.
 */

/**
 * Implements THEME_preprocess().
 *
 * For error messages.
 */
function media_23video_auth_preprocess_media_23video_error(&$variables) {
  foreach ($variables['options'] as $key => $val) {
    $variables[$key] = $val;
  }
}

/**
 * Implements THEME_preprocess().
 *
 * For video.
 */
function media_23video_auth_preprocess_media_23video_video(&$variables) {
  foreach ($variables['options'] as $key => $val) {
    $variables[$key] = $val;
  }
  $variables['title'] = 'aoeuaoeu';
  $variables['width'] = 640;
  $variables['height'] = 480;
}
