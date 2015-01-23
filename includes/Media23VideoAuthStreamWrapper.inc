<?php

/**
 * @file
 * Create a 23video Stream Wrapper class for the Media/Resource module.
 */


/**
 * Stream wrapper for 23Video.
 */
class Media23VideoAuthStreamWrapper extends MediaReadOnlyStreamWrapper {

  /**
   * Handles parameters on the URL string.
   *
   * @see MediaReadOnlyStreamWrapper::interpolateUrl()
   */
  public function interpolateUrl() {
    if ($parameters = $this->get_parameters()) {
      if (!isset($parameters['s']) || !isset($parameters['v'])) {
        return '';
      }
      return "https://{$parameters['s']}/v.ihtml/player.html?source=share&photo_id={$parameters['v']}&autoPlay=0";
    }
  }

  /**
   * Returns the MIME type of the resource.
   *
   * @see MediaReadOnlyStreamWrapper::getMimeType()
   */
  public static function getMimeType($uri, $mapping = NULL) {
    return 'video/23video-auth';
  }

  /**
   * Returns original thumbnail.
   */
  public function getOriginalThumbnailPath() {
    if ($parameters = $this->get_parameters()) {
      $consumer = Connector23Video::getInstance();
      $photo_id = $parameters['v'];
      $token = $parameters['t'];
      $sections = $consumer->doSectionList($photo_id, $token);

      if (!isset($sections['sections']) || !count($sections['sections'])) {
        $consumer->doSectionCreate($photo_id, 0);
        $sections = $consumer->doSectionList($photo_id, $token);
      }
      $thumbnail_url = $sections['section']['quad100_download'];
      $base = variable_get('media23video_service_site_url');
      return $base . $thumbnail_url;
    }
  }

  /**
   * Returns local thumbnail.
   */
  public function getLocalThumbnailPath() {
    $parts = $this->get_parameters();
    // There's no need to hide thumbnails, always use the public system rather
    // than file_default_scheme().
    $local_path = 'public://media-23video-auth/' . check_plain($parts['v']) . '.jpg';
    if (!file_exists($local_path)) {
      $dirname = drupal_dirname($local_path);
      file_prepare_directory($dirname, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS);
      $response = drupal_http_request($this->getOriginalThumbnailPath());
      if (!isset($response->error)) {
        file_unmanaged_save_data($response->data, $local_path, TRUE);
      }
      else {
        @copy($this->getOriginalThumbnailPath(), $local_path);
      }
    }

    return $local_path;
  }
}
