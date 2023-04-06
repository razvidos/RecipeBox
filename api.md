# API Documentation

This document describes the endpoints and data models for the RecipeBox API.

## Base URL

`https://localhost:8000/api`

All API endpoints are relative to this base URL.

## Authentication

Store Update and Delete endpoints require authentication using an API key.

To authenticate, include your API key in the Authorization header of your HTTP request.

```makefile
Authorization: Bearer <api_key>
```

## Endpoints

### /recipes

#### POST /api/recipes

Creates a new recipe.

**Request Parameters**

| Name |    Type |    Description |
|---|---|---|
| title |    string |    Required. The title of the recipe. Maximum length: 255 characters. |
| description |    string |    The description of the recipe. |
| ingredients |    string |    The ingredients of the recipe. |
| instructions |    string |    The instructions of the recipe. |
| image |    file |    The image of the recipe. Maximum size: 2MB. |
| category_ids |    array |    An array of category IDs associated with the recipe. |

**Response**

| Name    | Type    | Description |
|--- |--- |--- |
| id |    integer |    The ID of the created recipe. |
| title |    string |    The title of the recipe. |
| description |    string |    The description of the recipe. |
| ingredients |    string |    The ingredients of the recipe. |
| instructions |    string |    The instructions of the recipe. |
| image |    string |    The URL of the image of the recipe. |

**Error Responses**

| Status Code |    Description |
|--- |--- |
| 404    | Recipe not found. |
| 403    | User is not the owner of the recipe. | 
| 422    | Validation error. |

**Example Request**

```json
{
    "title": "Spaghetti Bolognese",
    "description": "A classic pasta dish from Italy.",
    "ingredients": "spaghetti, minced beef, onion, garlic, tomato sauce",
    "instructions": "1. Cook spaghetti. 2. Fry minced beef, onion and garlic. 3. Add tomato sauce. 4. Mix with cooked spaghetti.",
    "category_ids": [
        1,
        2
    ]
}
```

**Example Response (201)**

```json
{
    "id": 1,
    "title": "Spaghetti Bolognese",
    "description": "A classic pasta dish from Italy.",
    "ingredients": "spaghetti, minced beef, onion, garlic, tomato sauce",
    "instructions": "1. Cook spaghetti. 2. Fry minced beef, onion and garlic. 3. Add tomato sauce. 4. Mix with cooked spaghetti.",
    "image": "https://example.com/images/recipe-1.jpg",
    "categories": [
        {
            "id": 1,
            "name": "Pasta"
        },
        {
            "id": 2,
            "name": "Meat"
        }
    ]
}
```

**Example Error Response (422)**

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "title": [
      "The title field is required."
    ]
  }
}

```

#### GET /recipes

Retrieve a list of all recipes.

Query Parameters

| Parameter   | Type    | Description |
|-------------|---------|----------------------------------------------------------------|
| page        | integer | Paginate the results.|
| per_page        | integer | Count of the results by page.|
| category_ids (optional)      | array of integers  | An array of integers representing the ids of the categories to filter by. |
| keyword (optional)      | string  | A string representing the search term. Searches the title field of recipes by default. |
| searchType (optional)  | string  | (Optional) (optional): An enum representing the search type. Possible values are simple, with_ingredients, and deep. Default value is simple.|

##### Response

Returns a JSON object that contains Laravel Paginator data. The JSON response includes the following fields

- current_page: the current page number
- data: an array of recipe objects on the current page
- first_page_url: the URL of the first page
- from: the index of the first recipe on the current page
- last_page: the total number of pages
- last_page_url: the URL of the last page
- links: an array of URL by pages
- next_page_url: the URL of the next page, if there is one
- path: the base path for all pages
- per_page: the number of recipes per page
- prev_page_url: the URL of the previous page, if there is one
- to: the index of the last recipe on the current page
- total: the total number of recipes in the database

**data** containing an array of recipe objects.

| Parameter    | Type    | Description |
|---|---|---|
| id |    integer |    The recipe's ID. |
| user_id |    integer |    The ID of the user who created the recipe. |
| title |    string |    The recipe's title. |
| description |    string |    The recipe's description. |
| ingredients | string |    The ingredients required to make the recipe. |
| instructions | string |    The instructions to make the recipe. |
| categories | array |    An array of categories that the recipe belongs to. |
| image |    string |    The URL of the recipe's image. |
| created_at |    string |    The date and time the recipe was created. |
| updated_at |    string |    The date and time the recipe was last updated. |

**Example Requests**

Retrieve all recipes:

```http
GET /api/recipes
```

Retrieve recipes containing the keyword "chicken":

```http
GET /api/recipes?keyword=chicken
```

Retrieve recipes in the "dinner" and "healthy" categories:

```http
GET /api/recipes?category_ids[]=1&category_ids[]=3
```

Retrieve recipes containing the keyword "salad" and ingredients and instructions using deep search:

```http
GET /api/recipes?keyword=salad&searchType=deep
```

**Example Response (200)**

```json
{
    "data": [
        {
            "id": 24,
            "user_id": 2,
            "title": "asfsaf",
            "description": "",
            "ingredients": "asf",
            "instructions": "",
            "image": "public/images/recipes/u6oyk0EgZITkgMEhHMlZeN1YMisGAWUqqdN1Hdpi.jpg",
            "created_at": "2023-04-06T18:26:29.000000Z",
            "updated_at": "2023-04-06T18:26:54.000000Z",
            "categories": [
                {
                    "id": 15,
                    "name": "ut",
                    "created_at": "2023-04-06T13:58:12.000000Z",
                    "updated_at": "2023-04-06T13:58:12.000000Z"
                },
                {
                    "id": 14,
                    "name": "rem",
                    "created_at": "2023-04-06T13:58:12.000000Z",
                    "updated_at": "2023-04-06T13:58:12.000000Z"
                }
            ]
        }
    ],
    "links": {
        "first": "http://localhost:8000/api/recipes?page=1",
        "last": "http://localhost:8000/api/recipes?page=2",
        "prev": null,
        "next": "http://localhost:8000/api/recipes?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "last_page": 2,
        "links": [
            {
                "url": "http://localhost:8000/api/recipes?page=1",
                "label": 1,
                "active": true
            },
            {
                "url": "http://localhost:8000/api/recipes?page=2",
                "label": 2,
                "active": false
            }
        ],
        "path": "http://localhost:8000/api/recipes",
        "per_page": 1,
        "to": 1,
        "total": 2
    }
}

```

In this example response, we have one recipe object with id 24, and it has an array of categories associated with it.
The response also includes pagination information with links to the first, last, previous, and next pages, as well as
metadata about the current page, the total number of items, and the number of items per page.

#### GET /recipes/{id}

**Request**

```http
GET /api/recipes/{id}
```

**Example Response (200)**

```json
{
    "id": 1,
    "title": "Spaghetti Carbonara",
    "description": "A classic Italian dish with spaghetti, bacon, eggs, and cheese.",
    "image": "/storage/recipes/spaghetti-carbonara.jpg",
    "ingredients": [
        {
            "id": 1,
            "name": "spaghetti",
            "quantity": "1 pound"
        },
        {
            "id": 2,
            "name": "bacon",
            "quantity": "4 slices"
        },
        {
            "id": 3,
            "name": "eggs",
            "quantity": "2"
        },
        {
            "id": 4,
            "name": "parmesan cheese",
            "quantity": "1/2 cup"
        }
    ],
    "steps": [
        {
            "id": 1,
            "description": "Cook spaghetti according to package instructions."
        },
        {
            "id": 2,
            "description": "Cook bacon in a large skillet until crispy."
        },
        {
            "id": 3,
            "description": "Beat eggs and parmesan cheese together in a bowl."
        },
        {
            "id": 4,
            "description": "Drain spaghetti and add to the skillet with the bacon. Remove from heat."
        },
        {
            "id": 5,
            "description": "Pour egg mixture over the spaghetti and stir quickly to coat. The residual heat from the spaghetti will cook the eggs."
        },
        {
            "id": 6,
            "description": "Serve hot, garnished with additional parmesan cheese if desired."
        }
    ],
    "categories": [
        {
            "id": 1,
            "name": "Pasta"
        },
        {
            "id": 2,
            "name": "Italian"
        }
    ],
    "created_at": "2022-04-03T18:25:43.511Z",
    "updated_at": "2022-04-03T18:25:43.511Z"
}
```

**Not Found (404)**

if the recipe with the given ID does not exist.

#### PUT/PATH /recipes/{id}

Updates an existing recipe.

**Request Parameters**

| Name |    Type |    Description |
|---|---|---|
| title |    string |    Required. The title of the recipe. Maximum length: 255 characters. |
| description |    string |    The description of the recipe. |
| ingredients |    string |    The ingredients of the recipe. |
| instructions |    string |    The instructions of the recipe. |
| image |    file |    The image of the recipe. Maximum size: 2MB. |
| category_ids |    array |    An array of category IDs associated with the recipe. |

**Response**

| Name    | Type    | Description |
|--- |--- |--- |
| id |    integer |    The ID of the created recipe. |
| title |    string |    The title of the recipe. |
| description |    string |    The description of the recipe. |
| ingredients |    string |    The ingredients of the recipe. |
| instructions |    string |    The instructions of the recipe. |
| image |    string |    The URL of the image of the recipe. |

**Error Responses**

| Status Code |    Description |
|--- |--- |
| 404    | Recipe not found. |
| 403    | User is not the owner of the recipe. | 
| 422    | Validation error. |

**Example Request**

```json
{
    "title": "Spaghetti Bolognese",
    "category_ids": [1, 2]
}
```

**Example Response (201)**

```json
{
    "id": 1,
    "title": "Spaghetti Bolognese",
    "description": "A classic pasta dish from Italy.",
    "ingredients": "spaghetti, minced beef, onion, garlic, tomato sauce",
    "instructions": "1. Cook spaghetti. 2. Fry minced beef, onion and garlic. 3. Add tomato sauce. 4. Mix with cooked spaghetti.",
    "image": "https://example.com/images/recipe-1.jpg"
}
```

#### DELETE /api/recipes/{id}

Deletes the recipe with the given ID.

**Request**

```http
DELETE /recipes/{id}
```

**Example Response**

```json
{
    "message": "Recipe deleted"
}
```
