<?php

return [
    'default_message' => json_encode([
        ['message' => "Okay, Houston, we've had a problem here.", 'from' => 'John Swigert (CMP)', 'date' => 'April 13, 1970'],
        ['message' => 'This is Houston. Say again, please.', 'from' => 'Jack R Lousma (CC)', 'date' => 'April 13, 1970'],
        ['message' => "Uh, Houston, we've had a problem.", 'from' => 'Jim Lovell (CDR)', 'date' => 'April 13, 1970'],
        ['message' => "We've had a MAIN B BUS UNDERVOLT.", 'from' => 'Jim Lovell (CDR)', 'date' => 'April 13, 1970'],
        ['message' => 'Roger, MAIN B UNDERVOLT.', 'from' => 'Jack R Lousma (CC)', 'date' => 'April 13, 1970'],
        ['message' => "Okay, stand by, Thirteen, we're looking at it.", 'from' => 'Jack R Lousma (CC)', 'date' => 'April 13, 1970'],
        ['message' => "Internal Server Error", 'from' => 'Mr. Server', 'error_code' => '500', 'full_message' => 'https://en.wikipedia.org/wiki/File:Apollo13-wehaveaproblem.ogg'],
    ], JSON_PRETTY_PRINT),
];
