<?php

return [
    
   // Method    URL Path                Controller@action
    [ 'GET'   , '/recipes'            , 'Recipes@get'         ],
    [ 'POST'  , '/recipes'            , 'Recipes@create'      ],
    [ 'GET'   , '/recipes/{id}'       , 'Recipes@read'        ],
    [ 'PUT'   , '/recipes/{id}'       , 'Recipes@update'      ],
    [ 'PATCH' , '/recipes/{id}'       , 'Recipes@updateField' ],
    [ 'DELETE', '/recipes/{id}'       , 'Recipes@remove'      ],
    [ 'POST'  , '/recipes/{id}/rating', 'Recipes@rate'        ]
    
];