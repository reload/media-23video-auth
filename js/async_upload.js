(function ($) {
/*  $(document).ready(function() {
    $(window).keydown(function(event) {
      if (event.keyCode == 13) {
        event.preventDefault();
        $('#media-23videa-auth-upload').find('#edit-submit').trigger();
        return false;
      }
    });
  });*/

  /**
   * Get upload token synchronously from Drupal site.
   */
  function getUploadToken() {
    $('#upload-status').text('Fetching upload token...');
    var title = $('#media-23video-auth-upload input[name="title"]').val();
    var description = $('#media-23video-auth-upload input[name="description"]').val();
    var album_id = $('#media-23video-auth-upload input[name="album_id"]').val();
    $('#media-23video-auth-upload').find('input[name="title"], input[name="description"]').attr('disabled', 'disabled');
    $.ajax({
      url: '/media-23video-uploadtoken/',
      data: {'title': encodeURI(title), 'description': encodeURI(description), 'album_id': encodeURI(album_id)},
      dataType: 'json',
      async: true,
      success: function (responseText, statusText, xhr) {
        if (responseText.upload_token) {
          $('#upload-status').text('Uploading file.');
          // Save upload token in input tag.
          $('input[name="upload_token"]').val(responseText.upload_token);
          $('#media-23video-auth-upload').submit();
          return true;
        }
        else {
          $('#upload-status').text('An error ocurred while fetching upload token. Try again later.');
          $('#media-23video-auth-upload .ajax-progress-throbber div').removeAttr('class');
          $('#media-23video-auth-upload').find('input[name="title"], input[name="description"]').removeAttr('disabled');
          alert("Error. Couldn't receive upload token. ");
          return false;
        }
      },
      error: function (xhr, statusText, errorThrown) {
        $('#upload-status').text('An error ocurred. Try again later.');
        $('#media-23video-auth-upload .ajax-progress-throbber div').removeAttr('class');
        $('#media-23video-auth-upload').find('input[name="title"], input[name="description"]').removeAttr('disabled');
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
    })
  }

  /**
   * Upload files via ajax.
   *
   * @type {{attach: Function}}
   */
  Drupal.behaviors.upload = {
    attach: function (context, settings) {
      $('#media-23video-auth-upload').find('#edit-submit').click(function() {
        $('.ajax-progress-throbber div').attr('class', 'throbber');
        getUploadToken();
      });
      $("#edit-description, #edit-title").keypress(function(event) {
        if (event.keyCode == 13) {
          $('.ajax-progress-throbber div').attr('class', 'throbber');
          getUploadToken();
          return false;
        }
      });
      var options = {
        dataType: 'json',
        async: true,
        beforeSubmit: function(arr, $form, options) {
          if ($('#edit-upload-file').val() == '') {
            $('#upload-status').text('File is empty.');
            $('#media-23video-auth-upload .ajax-progress-throbber div').removeAttr('class');
            $('#media-23video-auth-upload').find('input[name="title"], input[name="description"]').removeAttr('disabled');
            return false;
          }
        },
        success: function(responseText, statusText, xhr, $form) {
          $("input[name='photo_id']").val(responseText.photo_id);
          $("input[name='title']").val(responseText.title);
          $("input[name='token']").val(responseText.token);
          $("#media-23video-auth-attach").submit();
          return true;
        },
        error: function (xhr, statusText, errorThrown) {
          $('#upload-status').text('An error ocurred. Try again later.');
          $('#media-23video-auth-upload .ajax-progress-throbber div').removeAttr('class');
          $('#media-23video-auth-upload').find('input[name="title"], input[name="description"]').removeAttr('disabled');
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
