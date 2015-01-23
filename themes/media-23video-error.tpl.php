<?php

/**
 * @file
 * Template file for theme('media_23video_auth').
 */

?>
<div class="<?php print $classes; ?> media-23video-auth-<?php print $id; ?>">
  <p><?php print t("Error. Couldn't show video. Contact an administrator."); ?></p>
  <p><?php print t('Error: @error', array('@error', $message)); ?></p>
</div>
