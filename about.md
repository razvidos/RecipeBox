# About

RecipeBox is a single page web application (SPA) built using the
_Laravel_ PHP framework and the
_Vue.js_ JavaScript framework with
_Vuetify_ as the UI component library.

Laravel is used as the backend framework for RecipeBox, handling user authentication, database management, and API
endpoints for retrieving and updating recipe and shopping list data.

Vue.js and Vuetify are used for the front-end, providing a smooth and responsive user interface with a variety of
pre-built UI components that can be easily customized to fit the project's design.

### SPA

An SPA is a web application that loads a single HTML page and dynamically updates the page as the user interacts with
it, without the need for a full page reload.

Laravel routing system is being used to define a catch-all route that maps to the SinglePageController index method. The
where('any', '.*') method call allows the route to match any URI, including those with multiple segments, allowing the
Vue.js router to handle the routing. The Auth::routes() method is also called to set up authentication routes.

## Design Decisions:

- Frontend:
  I decided to use Vue.js and Vuetify for the frontend because Vue.js is lightweight and easy to learn while Vuetify
  provides a large set of pre-built UI components that allowed me to quickly put together a beautiful and responsive
  user interface.
- Backend: I chose Laravel because of its robust features, such as Eloquent ORM, built-in authentication system. These
  features helped me to quickly develop the backend logic and focus on the frontend implementation.

## Challenges Faced:

During the development of the RecipeBox project, I faced several challenges. One of the most notable was with sending
and processing FormData with images. In order to solve this issue, I added the following code to the constructor:

```php
class RecipeController 
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            /**
             * transform null to ''
             *
             * JS problem:
             * FormData.append(<key>, '') transform '' to null
             */
    
            $input = array_map(static function ($value) {
                return is_null($value) ? '' : $value;
            }, $request->all());
    
            $request->replace($input);
    
            return $next($request);
        })->only(['store', 'update']);
    }
    // ...
}
```

This middleware ensures that null values in the FormData are transformed to empty strings, which solved the issue of
null values being sent instead.

Overall, the development of RecipeBox was a rewarding experience, and these challenges helped me to grow as a developer.
