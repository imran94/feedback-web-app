This is a full-stack web application for users to submit feedback, made with a Vuejs frontend and a Laravel backend.
It supports the following features:

- Registration and login for users
- Submit feedback
- Vote on feedback
- Edit or delete own feedback posts
- Comment on feedback posts.
- Edit or delete own comments.
- The ability for admins to moderate all feedback posts and comments.

### How to run

First run the backend:

1. Create .env file by copying from .env.example
2. set the necessary database credentials in the following fields (This application uses a MySQL database)

- DB_HOST
- DB_PORT
- DB_DATABASE
- DB_USERNAME
- DB_PASSWORD

Run the following commands:

1. `cd backend`
2. `composer install`
3. `php artisan migrate`
4. `php artisan db:seed`
5. `php artisan serve`

Then run the frontend with the following commands:

1. `cd frontend` from the project root
2. `npm install`
3. `npm run dev`

Note: The backend runs on port 8000 by default. If you wish to run it on some other port, do so with the command `php artisan serve --port={port}`. Then set the VITE_BACKEND_PORT property in the frontend's .env file to the same port number, otherwise the frontend won't be able to communicate with the backend.
