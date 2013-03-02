<?php

// Admin Navigation: add new item
Navigation::add(__('Dev Js', 'dev'), 'content', 'dev', 10);


/**
 * Sandbox admin class
 */
class DevAdmin extends Backend {

    /**
     * Main Sandbox admin function
     */
    public static function main() {

        if (Request::post('dev_submit_settings')) {
            if (Security::check(Request::post('csrf'))) {
                Option::update(array(
                    'dev_valid_frontend'    => (int)Request::post('valid_frontend'),
                    'dev_valid_backend'     => (int)Request::post('valid_backend'),
                    'dev_fancy_frontend'    => (int)Request::post('fancy_frontend'),
                    'dev_fancy_backend'     => (int)Request::post('fancy_backend'),
                    'dev_file_upload'       => (int)Request::post('file_upload'),
                    'dev_date_frontend'     => (int)Request::post('date_frontend'),
                    'dev_date_backend'      => (int)Request::post('date_backend')
                ));

                Notification::set('success', __('Your changes have been saved', 'dev'));

                Request::redirect('index.php?id=dev');
            } else { die('csrf detected!'); }
        }

        View::factory('dev/views/backend/settings')->display();
    }

}