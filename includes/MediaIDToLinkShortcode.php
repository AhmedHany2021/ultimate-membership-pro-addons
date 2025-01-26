<?php

namespace MEMBERSHIPADDON\INCLUDES;

class MediaIDToLinkShortcode
{

    public function __construct()
    {
        add_shortcode('media_link_form', [$this, 'render_form']);
        add_action('init', [$this, 'handle_form_submission']);
    }

    public function render_form($atts)
    {
        $link = isset($_GET['media_link']) ? esc_url($_GET['media_link']) : '';
        ob_start();
        ?>
        <form method="post" action="">
            <label for="media_id">Enter Media ID:</label>
            <input type="number" id="media_id" name="media_id" required>
            <button type="submit" name="get_media_link">Get Media Link</button>
        </form>
        <?php if ($link): ?>
        <p>Media Link: <a href="<?php echo $link; ?>" target="_blank"><?php echo $link; ?></a></p>
    <?php endif; ?>
        <?php
        return ob_get_clean();
    }

    public function handle_form_submission()
    {
        if (isset($_POST['get_media_link']) && isset($_POST['media_id'])) {
            $media_id = intval($_POST['media_id']);
            $media_url = wp_get_attachment_url($media_id);

            if ($media_url) {
                $redirect_url = add_query_arg('media_link', urlencode($media_url), wp_get_referer());
                wp_redirect($redirect_url);
                exit;
            } else {
                $redirect_url = add_query_arg('media_error', 'invalid_id', wp_get_referer());
                wp_redirect($redirect_url);
                exit;
            }
        }
    }

}