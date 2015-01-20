<?php

class Connector23Video {
  protected static $instance;
  protected $consumer;

  /**
   * Singleton
   */
  public static function getInstance() {


    $token_public = variable_get('media23video_service_api_key_public', '');
    $token_secret = variable_get('media23video_service_api_key_secret', '');
    $token_access_public = variable_get('media23video_service_api_access_token', '');
    $token_access_secret = variable_get('media23video_service_api_access_token_secret', '');

    // Set up HTTP request
    $httpRequest = new HTTP_Request2;
    $httpRequest->setHeader('Accept-Encoding', '.*');
    $request = new HTTP_OAuth_Consumer_Request;
    $request->accept($httpRequest);

    // Set up OAuth consumer
    $consumer = new HTTP_OAuth_Consumer($token_public, $token_secret);
    $consumer->accept($request);

    if ($token_access_public != NULL && $token_access_secret != NULL) {

      $consumer->setToken($token_access_public);
      $consumer->setTokenSecret($token_access_secret);

    }

    if (!isset(self::$instance)) {
      $class = __CLASS__;
      self::$instance = new $class();
      self::$instance->setConsumer($consumer);
    }
    return self::$instance;

  }

  public function setConsumer($consumer) {
    $this->consumer = $consumer;
  }

  public function getConsumer() {
    return $this->consumer;
  }

  public function doPhotoListAll($flush_cache = FALSE) {
    return $this->doPhotoList(0, $flush_cache);
  }

  public function doPhotoList($photo_id = 0, $flush_cache = FALSE) {
    $cache = cache_get(__CLASS__ . ":" . $photo_id);

    if ($flush_cache == TRUE || empty($cache)) {

      $consumer = $this->getConsumer();
      $requst_url = variable_get('media23video_service_site_url', '') . '/api/photo/list';

      $params = array(
        'format' => 'json',
      );

      if ($photo_id != 0) {
        $params['photo_id'] = $photo_id;
      }


      $response = $consumer->sendRequest($requst_url,
        $params,
        'GET'
      );

      $info = $this->getResponseJSON($response->getBody());

      cache_set(__CLASS__ . ":" . $photo_id, $response->getBody());
    }
    else {

      $info = $this->getResponseJSON($cache->data);

    }
    return $info;
  }

  public function doAlbumList() {
    $consumer = $this->getConsumer();
    $requst_url = variable_get('media23video_service_site_url', '') . '/api/album/list';

    $response = $consumer->sendRequest($requst_url,
      array(
        'format' => 'json',
      ),
      'GET'
    );
    return $this->getResponseJSON($response->getBody());
  }

  public function doGetUploadToken($title, $description, $return_url) {
    $consumer = $this->getConsumer();
    $requst_url = variable_get('media23video_service_site_url', '') . '/api/photo/get-upload-token';
    $response = $consumer->sendRequest($requst_url,
      array(
        'title' => $title,
        'description' => $description,
        //'album_id' => variable_get('media23video_service_album_default', ''),
        'format' => 'json',
        'return_url' => $return_url,
      ),
      'GET'
    );

    $info = $this->getResponseJSON($response->getBody());
    return $info['uploadtoken']['upload_token'];
  }

  public function getResponseJSON($body) {
    $body = str_replace('var visual = ', '', $body);
    $info = drupal_json_decode($body);
    if ($info['status'] != 'ok') {
      drupal_set_message($body, 'error');
      //throw new Exception("Somewrong", 1);
    }
    return $info;
  }
}
