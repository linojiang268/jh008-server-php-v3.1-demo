<?php

return [
    // the minimum time interval (in seconds) after which message is
    // allowed to be sent since last. 0 indicates no limit
    'sms_send_interval' => env('SMS_SEND_INTERVAL', 120),

    // after how many seconds, message should be expired
    'sms_survival_time' => env('SMS_SURVIVAL_TIME', 600),
    
    // the maximum number of messages that can be sent within a period
    // of time (in seconds). for instance, ['limit_period' => 86400, 'limit_count' => 10]
    // tells that user can be sent 10 messages per day. 
    // 0 of either field indicates no limit.
    'sms_count_limit_period' => env('SMS_COUNT_LIMIT_PERIOD', 86400),
    'sms_count_limit'  => env('SMS_COUNT_LIMIT',  10),
];
