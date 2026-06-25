<?php

namespace octarinepress\Custom;

class Comments
{
    public function register()
    {
        add_filter('comment_form_fields', [$this, 'move_comment_field']);
        add_filter('comment_form_defaults', [$this, 'comments_text_change']);

    }

    public function move_comment_field($fields)
    {
        $comment_field = $fields['comment'];
        $author_field = $fields['author'];
        $email_field = $fields['email'];
        $url_field = $fields['url'];
        $cookies_field = $fields['cookies'];
        unset($fields['comment']);
        unset($fields['author']);
        unset($fields['email']);
        unset($fields['url']);
        unset($fields['cookies']);

        $fields['author'] = $author_field;
        $fields['email'] = $email_field;
        $fields['url'] = $url_field;
        $fields['comment'] = $comment_field;
        $fields['cookies'] = $cookies_field;

        return $fields;
    }

    public function comments_text_change()
    {
        $defaults['comment_notes_before'] = '<p class="comment-notes">' . __('Your email address will not be published. Required fields are marked with *.', 'octarinepress') . '</p>';

        return $defaults;

    }
}
