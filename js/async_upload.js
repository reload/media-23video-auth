(function ($) {
  /**
   * Get upload token synchronously from Drupal site.
   */
  function getUploadToken() {
    var title = $('#media-23video-auth-upload input[name="title"]').val();
    var description = $('#media-23video-auth-upload input[name="description"]').val();
    $.ajax({
      url: '/media-23video-uploadtoken/',
      data: {'title': encodeURI(title), 'description': encodeURI(description)},
      dataType: 'json',
      async: false,
      success: function (responseText, statusText, xhr) {
        if (responseText.upload_token) {
          // Save upload token in input tag.
          $('input[name="upload_token"]').val(responseText.upload_token);
          return true;
        }
        else {
          alert("Error. Couldn't receive upload token. " + upload_token);
          return false;
        }
      },
      error: function (responseText) {
        return false;
      }
    })
  }

  /**
   * Upload files via ajax.
   *
   * @type {{attach: Function}}
   */
  Drupal.behaviors.upload = {
    attach: function (context, settings) {
      var options = {
        dataType: 'json',
        async: true,
        beforeSubmit: function() {
          // Get upload token before we try to submit the file.
          getUploadToken();
        },
        success: function(responseText, statusText, xhr, $form) {
          $("input[name='photo_id']").val(responseText.photo_id);
          $('#media-23video-auth-attach').submit();
          return true;
        },
        error: function(responseText) {
          switch (statusText) {
            case 'timeout':
              alert('The server timed out. Try again.');
              break;
            case 'error':
              alert('An error occurred.');
              break;
            case 'abort':
              alert('Upload was aborted.');
              break;
            default:
              alert('Unknown error occurred.');
              break;
          }

          return false;
        }
      };

      // Use upload form as an ajax form.
      $('#media-23video-auth-upload').ajaxForm(options);
    }
  };
})(jQuery);
