<?php

return [
    'empty_array' => "Route collection array cannot be empty",
    'error_controller_not_found' => "Error controller not found",
    'invalid_element_count' => "Route array must have 3 or 4 elements",
    
    'first_element_must_be_request_method' => "First element of routes array must be Request Method ('GET', 'POST', etc.)",
    'second_element_must_be_url_path' => "Second element of routes array must be URL Path",
    'third_element_must_be_controller_and_action' => "Third element of routes array must be in the format Controller@action",
    
    'url_path_pattern' => "|(\/)([\w\/\[\]\{\}]*)(\??[\w\/\[\]\{\}]+\=[\w\/\[\]\{\}]+)*(\&?[\w\/\[\]\{\}]+\=[\w\/\[\]\{\}]+)*|i",
    'at_pattern' => "|[^@]*@[^@]*|",
]
