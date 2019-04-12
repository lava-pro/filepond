<?php

return [
    'prefix' => 'filepond',       // Route group prefix
    'image'  => [                 // Images pool config
        'field'  => ['filepond'],   // Field name
        'disk'   => 'local',        // Disk name
        'tudn'   => 'tmp',          // Temporary upload directory name
        'pudn'   => 'uploads',      // Persistent upload directory name
        'img_1'  => 'original',     // Name of fullsize image
        'img_2'  => 'thumb',        // Name of thumb image
    ],
    'file' => [                  // Files pool config
    ],
];