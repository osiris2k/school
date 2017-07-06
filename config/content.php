<?php
return [
    /*
     * General Section
     */
    'MAIN_MENU_STATUS'   => 'CONTENT',

    /*
     * Config Section
     */
    'CONTENT_TYPE_ID'    => 1,
    'MEDIA_TYPE_ID'      => 2,
    'MULTIMEDIA_TYPE_ID' => 3,

    'CONTENT_SUB_LEVEL_LIMIT' => 3,

    /*
     * Export Section
     */
    'EXPORT_DATA_TYPE_ID'     => [1, 3, 4],
    'EXPORT_DATA_TYPE_NAME'   => ['text', 'textarea', 'richtext'],
    'EXPORT_FILE_NAME_FORMAT' => 'content_translation_' . date('Y_m_d_h_i_s')

];