<?php

class Connector23Video {
  protected static $instance;
  protected $consumer;

  /**
   * Singleton.
   */
  public static function getInstance() {
    $consumer_key_public = variable_get('media23video_service_api_key_public', '');
    $consumer_key_secret = variable_get('media23video_service_api_key_secret', '');
    $access_token_public = variable_get('media23video_service_api_access_token', '');
    $access_token_secret = variable_get('media23video_service_api_access_token_secret', '');
    $site_url = variable_get('media23video_service_site_url', '');

    // Set up OAuth request.
    $consumer = new VisualVideo($site_url, $consumer_key_public, $consumer_key_secret, $access_token_public, $access_token_secret);

    if (!isset(self::$instance)) {
      $class = __CLASS__;
      self::$instance = new $class();
      self::$instance->setConsumer($consumer);
    }
    return self::$instance;
  }

  /**
   * Perform GET request with json format with raw = 1.
   *
   * @param string $api_function
   *   Api function, e.g. "/api/photo/list".
   * @param array $params
   *   Parameters to web service method.
   *
   * @return array
   *   Return json-decoded array
   */
  protected function doRequest($api_function, $params = array()) {
    $consumer = $this->getConsumer();
    $params['format'] = 'json';
    $params['raw'] = 1;

    $response = $consumer->get($api_function, $params);
    return $this->getResponseJson($response);
  }

  /**
   * Set consumer (VisualVideo instance).
   *
   * @param VisualVideo $consumer
   *   VisualVideo instance.
   */
  public function setConsumer($consumer) {
    $this->consumer = $consumer;
  }

  /**
   * Get consumer.
   *
   * @return VisualVideo
   *   VisualVideo instance
   */
  public function getConsumer() {
    return $this->consumer;
  }

  /**
   * Get all photos.
   *
   * @param bool $flush_cache
   *   Do we want to flush the cache.
   *
   * @return array
   *   json-decoded array
   */
  public function doPhotoListAll($flush_cache = FALSE) {
    return $this->doPhotoList(0, $flush_cache);
  }

  /**
   * Get list of photos.
   *
   * @param int $photo_id
   *   Photo id.
   * @param bool $flush_cache
   *   If we want to flush the cache.
   *
   * @return array
   *   Json-decoded array
   */
  public function doPhotoList($photo_id = NULL, $flush_cache = FALSE) {
    $cache = cache_get(__CLASS__ . ":" . $photo_id);

    if ($flush_cache == TRUE || empty($cache)) {
      $api_function = '/api/photo/list';

      $params = array();
      if ($photo_id) {
        $params['photo_id'] = $photo_id;
      }

      $response = $this->doRequest($api_function, $params);

      cache_set(__CLASS__ . ":" . $photo_id, $response);
    }
    else {

      $info = $cache->data;

    }
    return $info;
  }

  /**
   * Get list of albums.
   *
   * @return array
   *   JSon-decoded array
   */
  public function doAlbumList() {
    $api_function = '/api/album/list';
    return $this->doRequest($api_function, array());
  }

  /**
   * Get section list.
   *
   * @param int $photo_id
   *   Photo (video) id.
   * @param string $token
   *   Token for photo (video).
   *
   * @return array
   *   JSON-decoded array
   */
  public function doSectionList($photo_id, $token) {
    $params = array(
      'photo_id' => $photo_id,
      'token' => $token,
    );
    $api_function = '/api/photo/section/list';
    return $this->doRequest($api_function, $params);
  }

  /**
   * Create section of a video.
   *
   * @param int $photo_id
   *   Photo (video) id.
   * @param int $start_time
   *   Seconds from start.
   * @param string $title
   *   Title of section.
   * @param string $description
   *   Description of section.
   *
   * @return array
   *   JSON-decoded array
   */
  public function doSectionCreate($photo_id, $start_time, $title = 'Start of video', $description = 'Start of video') {
    $params = array(
      'photo_id' => $photo_id,
      'start_time' => $start_time,
      'title' => $title,
      'description' => $description,
    );
    $api_function = '/api/photo/section/create';
    return $this->doRequest($api_function, $params);
  }

  /**
   * Get upload token.
   *
   * @param string $title
   *   Title of video.
   * @param string $description
   *   Description of video.
   * @param int $album_id
   *   Album id to attach video to.
   * @param string $return_url
   *   Callback url after upload of file.
   *
   * @return array
   *   JSON-decoded array
   */
  public function doGetUploadToken($title, $description, $album_id, $return_url) {
    $api_function = '/api/photo/get-upload-token';
    $params = array(
      'title' => $title,
      'description' => $description,
      'album_id' => $album_id,
      'return_url' => $return_url,
    );
    $response = $this->doRequest($api_function, $params);
    return $response['uploadtoken']['upload_token'];
  }

  /**
   * Get user login token.
   *
   * @param int $user_id
   *   User id.
   * @param string $return_url
   *   URL to be redirected to after login.
   *
   * @return string
   *   JSON formatted.
   */
  public function doGetLoginToken($user_id, $return_url) {
    $api_function = '/api/user/get-login-token';
    $params = array(
      'user_id' => $user_id,
      'return_url' => $return_url,
    );
    return $this->doRequest($api_function, $params);
  }

  /**
   * Get user list.
   *
   * @return string
   *   JSON formatted list of users.
   */
  public function doUserList() {
    $api_function = '/api/user/list';
    return $this->doRequest($api_function, array());
  }

  /**
   * Decode JSON response to array.
   *
   * @param string $body
   *   JSON from 23Video server.
   *
   * @return array
   *   JSON-decoded array
   */
  public function getResponseJson($body) {
    $info = drupal_json_decode($body);
    if ($info['status'] != 'ok') {
      // TODO: error handling
    }
    return $info;
  }

}
