<?php
/**
 * @file
 * Media23Auth Browser plugin.
 */

/**
 * Class Media23VideoAuthBrowser.
 */
class Media23VideoAuthBrowser extends MediaBrowserPlugin {
  /**
   * Implements MediaBrowserPluginInterface::access().
   */
  public function access($account = NULL) {
    $op = 'create';
    $file = NULL;

    if (function_exists('file_entity_access')) {
      $access = file_entity_access($op, $file, $account);
    }
    elseif (function_exists('media_access')) {
      $access = media_access($op, $account);
    }
    else {
      $access = FALSE;
    }

    return $access;
  }

  /**
   * Implements MediaBrowserPlugin::view().
   */
  public function view() {
    // Check if the user is able to add remote media.
    if (user_access('add media from remote sources')) {
      // Upload form.
      $build['upload_form'] = drupal_get_form('media_23video_auth_upload', $this->params['types'], $this->params['multiselect']);
      // Form to attach uploaded file to content.
      $build['attach_form'] = drupal_get_form('media_23video_auth_attach', $this->params['types'], $this->params['multiselect']);
      return $build;
    }
  }
}
