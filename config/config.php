<?php

return [
    'error_message' => 'We are unable to process your request due to suspicious traffic from your network. If your request is urgent, please contact us by phone.',

    'repeat_offenders' => [
        'threshold' => 5,
        'http_code' => 403,
        'message' => 'We are unable to process your request due to suspicious traffic from your network. If your request is urgent, please contact us by phone.'
    ]
];
