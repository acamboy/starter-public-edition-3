<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

            <p>
                Project repository: <a href="https://github.com/ivantcholakov/starter-public-edition-3/" target="_blank">https://github.com/ivantcholakov/starter-public-edition-3/</a>
            </p>

            <p>
                Note: This is an older version of the platform. Further efforts will be applied on Application Starter 4, which supports multiple applications.
                See <a href="https://github.com/ivantcholakov/starter-public-edition-4/" target="_blank">https://github.com/ivantcholakov/starter-public-edition-4/</a>
            </p>

            <p>A Semantic UI icon: <i class="star icon"></i></p>
            <p>A Material Design icon: <i class="mdi mdi-star"></i></p>
            <p>A Font Awesome icon: <i class="fa fa-star"></i></p>

            <h2>Internationalization Test</h2>

            <p>
                Switch language by using the menu, see top, right. The text below should be properly translated.
            </p>

            <p>A translated text: <strong><?php echo lang('welcome.hello'); ?></strong></p>

            <h2>Self-Diagnostics</h2>

            <p><?php echo $diagnostics; ?></p>
