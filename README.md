# RecipeBox

The RecipeBox web application is a platform for users to create, store, and share their favorite recipes. The
application is built using the Laravel framework with a MySQL database for data storage. The frontend is built using
Vue.js with Vuetify for UI components.

## Links

- [API documentation](api.md)
- [Demo video](https://www.youtube.com/watch?v=ANSukCZaxRw)

## Installation

To run RecipeBox on your local machine, follow these steps:

1. Clone the repository from GitHub:

```console
git clone https://github.com/razvidos/RecipeBox.git
```   

2. Navigate into the project directory:

```console
cd recipebox
```

3. Install the necessary dependencies using Composer:

```console
composer install
```

4. Create a copy of the .env.example file and name it .env:

```console
cp .env.example .env
```

5. Generate an application key:

```console
php artisan key:generate
```

6. Create an empty database for the application.

7. In the .env file, set the necessary database connection variables:

```makefile
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=recipebox
DB_USERNAME=root
DB_PASSWORD=
```

8. Run the database migrations:

```console
php artisan migrate
```

9. Seed the database with sample data:

```console
php artisan db:seed
```

10. Install the necessary NPM packages:

```console
npm install
```

11. Compile the assets:

```console
npm run dev
```

12. Start the application:

```console
php artisan serve
```

13. Visit http://localhost:8000 in your web browser to view the application.

## The main features:

- Recipe Creation and Management: Users can create, edit, and delete their own recipes.

- Recipe Search: Users can search for recipes based on keywords or categories.

- Authorization and Authentication: Only registered users can access certain features, such as creating and editing
  recipes.

- User Profile: Each user has their own profile page, where they can see their own recipes.

- UI Design: The application is designed using Vuetify components to provide a clean and intuitive user interface.
