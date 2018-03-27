# RECIPELAND

Recipeland implements a REST API for managing Recipes.\
It can list, create, read, update, delete, search and rate Recipes.

It ships with the helper bash script `./recipe` for managing its deployment.

To run the application and expose it to port 80, just call `./recipe cook`.\
To see the full list of available commands, use `./recipe help`.


## Project features 
- The code has [100% test coverage](https://recipeland-rafaelbeckel.c9users.io/docs/test_coverage/index.html);
- It ships with a simple to use, fun deploy script;
- It generates whatever number of delicious random recipes;
- It implements an internal DSL for input validation (we can build some complex rules with it);
- List results are paginated and cached, with a DB fallback if cache layer fails;
- It supports complex search queries like /recipes/search?prep_time={"gt":15,"lte":20};
- Access management with a flexible ACL and smart JWT token issuance & validation;
- PSR-1 & 2 coding style; PSR-3 logging; PSR-4 loading, PSR-7 messaging; PSR-11 container; PSR-15 Middlewares;


## Endpoints

#### public
These endpoints are public and can be accessed by anyone:

- `GET /recipes`\
Will paginate and list all registered recipes. 

- `GET /recipes/{id}`\
Will read only the given recipe.

- `GET /recipes/search?query=foo`\
Searches recipes by name, subtitle and description. It supports filtering by: `author`, `rating`, `prep_time`, `total_time`, `vegetarian` and `difficulty`.\
Example: `/recipes/search?vegetarian=1&total_time={"lt":30,"gte":25}&rating={"gt":4}`

- `POST /auth/login`\
Provides a new JWT token for users who provide the correct username and password pair. See [Getting a token](#getting-a-token) session below for specific instructions.

#### not protected 
The endpoint below can be accessed by any unprivileged user, but it requires a valid token for taking requests. See [User Authentication](#user-authentication) session below for specific instructions:

- `POST /recipes/{id}/rating`\
Creates a new rating for the given recipe. A user can only rate each recipe once. Ratings cannot be edited. Authors cannot rate their own recipes. See [Rating recipes](#rating-recipes) session below for specific instructions. 

#### protected 
These endpoints can only be accessed by users with specific read / write permissions:

- `POST /recipes`\
Creates a new recipe. See [Creating and editing recipes](#creating-and-editing-recipes) session below for specific instructions. 

- `PUT /recipes`\
Updates (replaces) an existing recipe by providing a full body. The provided document MUST contain all mandatory keys, and it will entirely replace the old recipe. All ingredients and steps relationships will be destroyed and replaced by the provided ones. See [Creating and editing recipes](#creating-and-editing-recipes) session below for specific instructions. 

- `PATCH /recipes`\
Updates part of an existing recipe by providing a partial body. The provided document MUST contain at least one key, and it will replace only the selected keys in the old recipe. Existing ingredients and steps relationships will be kept. See [Creating and editing recipes](#creating-and-editing-recipes) session below for specific instructions. 

- `DELETE /recipes`\
Deletes the selected recipe. See [Deleting recipes](#deleting-recipes) session below for specific instructions. 

## User Authentication
To access the protected routes, use one of these credentials:

##### Homer Simpson
![Homer](public/static/Homer_Simpson.png)
- username: homer
- password: Marge1234!\
**Role:** client
  - Can only rate recipes.

##### Luigi Risotto
![Luigi](public/static/Luigi_Risotto.png)
- username: luigi
- password: Pasta1234!\
**Role:** chef
  - Can create recipes
  - Can edit his own recipes
  - Can delete his own recipes

##### Montgomery Burns
![Burns](public/static/Mr_Burns.png)
- username: burns
- password: Money1234!\
**Role:** restaurant owner
  - Can create recipes
  - Can edit all recipes
  - Can delete all recipes


### Getting a token:
Do a POST request to `/auth/login` with a raw JSON body of:
```json
{
    "username": "foo",
    "password": "bar"
}
```

The server shall respond with:
##### Header:
```bash
  Authorization: Bearer your.token.here # Valid for 30 minutes
```
##### Body:
```json
{
    "status":"Authorized"
}
```

Send this header to any protected route and you can use them. You MUST include the "Bearer" type before the token, and it MUST be in the `Authorization` header.

### Creating and editing recipes:
To create, replace (put) or update (patch) Recipes, send this raw JSON body to the appropriate endpoint:
```json
    {
        "recipe" : {
            "name" : "My Most delicious Recipe!",
            "subtitle" : "My subtitle",
            "description" : "My description",
            "prep_time" : 10,
            "total_time" : 20,
            "vegetarian" : 1,
            "difficulty" : 3,
            "picture" : "https://example.com/picture.jpg",
            "ingredients" : [
                {
                    "slug" : "my_ingredient",
                    "quantity" : "12",
                    "unit" : "mg",
                    "name" : "My Ingredient",
                    "picture" : "https://example.com/picture.jpg",
                    "allergens" : "milk"
                },
                {
                    "slug" : "my_second_ingredient",
                    "quantity" : "10",
                    "unit" : "ml",
                    "name" : "My Second Ingredient",
                    "picture" : "https://example.com/picture.jpg",
                }
            ],
            "steps" : [
                {
                    "description" : "Put ingredient somewhere",
                    "picture" : "https://example.com/picture.jpg"
                },
                {
                    "description" : "Eat Ingredient",
                    "picture" : "https://example.com/picture.jpg"
                }
            ]
        }
    }
```

For creating and replacing (put) you MUST provide all the keys above. For updating (patch), you can choose some keys to be updated, but you MUST include at least one of them.

When creating a recipe, if an ingredient's `slug` or a step's `description` exists, the provided body will be ignored (the program will use the existing record in the database). When updating or patching, existing body keys will be replaced by the provided ones.

##### Input validation:
All keys are validated before insertion in the database. So, if you provide the incorrect type for some key, the server will respond with a `401 Unauthorized` header with the validation message in the body. 

Notice that it will always respond `401` for incorrect input, even with valid tokens, because input validation occurs before user authentication (checking for token presence in the headers is part of the validation itself), so the server does not know yet who is performing the request.

### Rating recipes:
To create a rating for a given recipe, sinply provide a `rating` key in the body:
```json
{
    "rating" : 5
}
```

The rating MUST be an integer from 1 to 5. A user can only rate each recipe once. Ratings cannot be edited. Authors cannot rate their own recipes.

### Deleting recipes:
You don't need to provide a body to delete recipes. Users can only delete their own recipes, unless they have `delete_all_recipes` permission.
