# Bookworm

Welcome to the Bookworm project! You will develop a web application for book lovers to share and discuss their favorite
books. While doing so, you will master your web development knowledge, improving many related coding skills along the
way.

## Introduction

Bookworm is a new platform for book enthusiasts. Users can create profiles, add books to their reading lists,
rate books, and engage in discussions with other users. Your task is to develop the core features of this web
application.

## Pre-requisites and Requirements

To create this web app, you will need a local environment equipped with:

1. Web server (Apache or Nginx)
2. PHP 8
3. MySQL
4. Composer
5. Cypress
6. Git

Please use the provided Docker `pwii-bookworm-environment` setup.

### Requirements

1. Use Slim as the underlying framework.
2. Create and configure services in the `dependencies.php` file. Examples of services are repositories, Twig, Flash, ...
3. Use Composer to manage all the dependencies of your application. You must add at least one new dependency.
4. Use Twig as the main template engine.
5. Implement CSS to add style to your application. Optionally, you may use a CSS framework. Keep the CSS organized and
   avoid using raw HTML without styling. Similarly, keep your JS organized and separated from the HTML templates.
6. Use MySQL as the main database management system.
7. Use the provided SQL schema as your model. If you feel the need to change something relevant, consult the PWII
   team before doing so.
8. Effectively use Object-Oriented Programming principles, including Namespaces, Classes, and Interfaces.
9. Use Git to collaborate with your teammates.
10. Upload all the code to the private Git repository assigned to your team.
11. Each team member must actively contribute to the project with at least 10 commits. Each member must commit code
    related to the View, Controller, and Model.

### Use of AI assistants and tools - Disclosure

You are allowed to use AI assistants to search for information and ask questions, but you shall not use AI to generate
code. During project interviews, you will need to disclose any AI usage, and if there are concerns about your code's
authenticity, you may be asked to provide the source or inspiration for specific code sections.

That being said, we strongly encourage you to try and understand the concepts you're applying and figure out the reason
behind any issues you encounter before blindly trying to get a LLM to solve a problem for you. In case of doubt, we
recommend consulting a human instead of a machine.

## Sections

The functionalities you must implement in this project can be divided into the following sections:

1. Landing Page
2. Navigation
3. User Registration and Login
4. User Profile
5. Book Catalogue
6. Book Details
7. Book Ratings and Reviews
8. Discussion Forums

### Landing Page

This platform's home page will be accessible by anyone, regardless of whether they are currently logged in with a
user account.

| Endpoint | Method |
|----------|--------|
| /        | GET    |

This time, the platform's home page will be purely informative in nature (in other words, a landing page). You must
implement a visually appealing landing page where you will show a brief description of the platform, as well as showcase
its main features and functionalities.

Since this will essentially be the only public page of the platform (besides those related to authentication), users
should be able to easily access the sign-in and sign-up pages from here.

### Navigation

Given that the application will implement a varied set of features, it's important to provide users with a comfortable
way of navigating between them, as well as to ensure that they have the proper permissions when accessing them.

At all times, the platform must show a menu containing:

- The name of the platform (bookworm) in the form of text or a logo. Clicking on it will allow the user to go to the
  landing page, regardless of their current location.
- Depending on the user's authentication status:

    - **If the user is currently NOT logged in:** Links to the sign-in and sign-up pages. Note that, given the
      requirement to be authenticated in order to access the rest of the platform, this will essentially only be
      visible in the landing page.

    - **If the user is currently logged in:** Links to the rest of the 'main' features (Book Catalogue, Discussion
      Forums), as well as a preview of the user's profile picture. Clicking the profile image will lead to the User
      Profile page.

Naturally, simple linking between pages can be accomplished entirely in the front-end.

> **IMPORTANT:** Unauthenticated navigation to any endpoint that isn't the landing, sign-in or sign-up page must result
> in the user being redirected to the sign-in page. A generic message must be displayed using flash, informing them that
> authentication is required. Note that just changing the template to render isn't enough, a proper HTTP redirect must
> be used.

### Sign-up

This section describes the process of signing up a new user into the system. Anyone can access this endpoint, even if
they are not logged in.

| Endpoint | Method |
|----------|--------|
| /sign-up | GET    |
| /sign-up | POST   |

A registration form must be displayed when a user accesses the `/sign-up` endpoint. The information from the form
must be sent to the same endpoint using a **POST** method. The registration form must include the following inputs:

- Email - required.
- Password - required.
- Repeat password - required.

When a **POST** request is sent to the `/sign-up` endpoint, you must validate the information received from the form
and sign the user up only if all the validations have passed. The requirements for each field are as follows:

- Email: It must be a valid email address. The email must be unique among all users of the application.
- Password: It must not be empty and must contain at least 6 characters and at least one number.
- Repeat password: It must be the same as the password field.

If there are any errors, you must display the sign-up form again. All the information entered by the user should be
kept and shown in the form (except for password fields) together with all the errors below the corresponding inputs.

Here are the error messages that you must show respectively, in order of priority when applicable:

- The email field is required.
- The email address is not valid.
- The email address is already registered.
- The password must contain at least 6 characters and at least one number.
- The passwords do not match.

Once the user's account is created, the system must automatically sign them in, showing the Book Catalogue page.

To facilitate testing, you MUST use the Cypress identifiers shown in the following snippet:

```
<form data-cy="sign-up">
    <input data-cy="sign-up__email">
    <input data-cy="sign-up__password">
    <input data-cy="sign-up__repeatPassword">
    <input data-cy="sign-up__btn">
</form>
```

Note that error messages don't have to include Cypress identifiers this time.

### Sign-in

This section describes the process of logging into the system. Anyone can access this endpoint, even if
they are not logged in.

| Endpoint | Method |
|----------|--------|
| /sign-in | GET    |
| /sign-in | POST   |

When a user accesses the `/sign-in` endpoint, you must display the sign-in form. The information from the form must
be sent to the same endpoint using a **POST** method. The sign-in form must include the following inputs:

- Email - required.
- Password - required.

When the application receives a POST request to the `/sign-in` endpoint, it must validate the information received
from the form and attempt to log in the user. The validations for the inputs should be the same as in the registration.

If there are any errors or if the user does not exist, you must display the form again with all the information
provided by the user and display the corresponding error.

Here are the error messages that you must show respectively:

- The email address is not valid.
- The email address or password is incorrect.

After logging in, the user will be redirected to the Book Catalogue page.

To facilitate testing, you MUST use the Cypress identifiers shown in the following snippet:

```
<form data-cy="sign-in">
    <input data-cy="sign-in__email">
    <input data-cy="sign-in__password">
    <input data-cy="sign-in__btn">
</form>
```

Note that error messages don't have to include Cypress identifiers this time.

### User Profile

This endpoint allows users to view and update their personal information. Only authenticated users can access this
endpoint, meaning that the application should redirect unauthenticated users to the sign-in page.

| Endpoint | Method |
|----------|--------|
| /profile | GET    |
| /profile | POST   |

When an authenticated user accesses the `/profile` endpoint, you must display a form containing the following inputs:

- Email: This field should be pre-filled with the user's current email address and disabled to prevent updates.
- Username: This field should be pre-filled with the user's current username (if any) and allow for updates. We must
  ensure usernames are unique (except when uninitialized, of course).
- Profile Picture: This page should allow users to upload a profile picture (via an input or through more complex means
  such as drag and drop), adhering to the following requirements:

    1. The image size must be less than 1MB.
    2. Only PNG, JPG, GIF and SVG images are allowed.
    3. The image dimensions must be 400x400 pixels or less.
    4. Generate a UUID for the image and replace the file name with it (plus the original extension).
    5. If a user doesn't have a profile picture, a placeholder must be shown when rendering it.

You must validate the profile picture upload, display any errors below the corresponding input, and store the images in
an "uploads" folder inside the public folder of the server for display (`/www/public/uploads`).

> **NOTE:** It's important that users set up their username after signing up. Therefore, whenever a user tries to access
> a page that requires authentication without having set up their username, they must be redirected to this page with a
> flash message prompting them to set up a username. Similarly, we must ensure that they can't remove their username by
> updating it to an empty field.

To facilitate testing, you MUST use the Cypress identifiers shown in the following snippet:

```
<form data-cy="profile">
    <input data-cy="profile__email">
    <input data-cy="profile__username">
    <input data-cy="profile__picture">
    <input data-cy="profile__btn">
</form>
```

Note that error messages don't have to include Cypress identifiers this time.

### Book Catalogue

This section describes the book catalogue and its functionalities. Only authenticated users can access this endpoint,
meaning that the application should redirect unauthenticated users to the sign-in page.

| Endpoint   | Method |
|------------|--------|
| /catalogue | GET    |
| /catalogue | POST   |

A list of all available books will be shown when an authenticated user accesses the `/catalogue` endpoint, allowing
them to click on a book to view its details. This page must also allow the user to create a new book via one of two
forms:

- **Full form:** This form will prompt the user to enter all fields that make up a book:
    - Title - required.
    - Author - required.
    - Description - required.
    - Number of pages - required.
    - Cover image URL - not required. Optionally, you can also allow users to upload their own images here.

- **Import form:** This form will only prompt the user for a single value: A book's ISBN identifier. The application
  will then consult the [OpenLibrary API](https://openlibrary.org/dev/docs/api/books) to import its information. Here is
  our recommended approach on how to use it, but feel free to read the documentation for alternatives (their
  [Search API](https://openlibrary.org/dev/docs/api/search) is also useful):
    - Start at the ISBN endpoint [/isbn/{queried-isbn}.json](https://openlibrary.org/isbn/9781435122963.json), which
      will give you information about a specific Edition of a Work. This will include some already useful information
      (the title, number of pages, and Work endpoint to consult).
    - Consult the Work endpoint provided in the first query to obtain more information. The endpoint
      should look like [/works/{work-identifier}.json](https://openlibrary.org/works/OL19549764W.json) and will include
      the description and Author endpoint to consult.
    - Consult the Author endpoint provided in the previous query to obtain their name. The endpoint should look like
      [/authors/{author-identifier}.json](https://openlibrary.org/authors/OL22161A.json).
    - Generate the Cover URL from the cover ID obtained in the first query. The format is described in the corresponding
      [documentation](https://openlibrary.org/dev/docs/api/covers) and matches the following
      pattern: [https://covers.openlibrary.org/b/id/{cover-id}-L.jpg](https://covers.openlibrary.org/b/id/8545670-L.jpg)

It's your choice whether to leave the forms always visible or present them in a modal, tabs, etc. That being said, both
forms must generate a POST request to the current endpoint, with the backend being in charge of distinguishing which
information it receives.

When the endpoint receives a POST request the application will validate the received information, making sure that the
required fields exist and/or fetching anything it needs, and lastly adding the book to the database.

### Book Details

This section allows users to obtain the detailed information of a book. Only authenticated users can access this
endpoint, meaning that the application should redirect unauthenticated users to the sign-in page.

| Endpoint        | Method |
|-----------------|--------|
| /catalogue/{id} | GET    |

When an authenticated user clicks on a book in the catalogue they must be directed to the book's details page, which
will display the corresponding information. This includes the selected book's title, author, description, and cover
(rendered as an image in the DOM, of course). Additionally, you must show its average rating and any reviews that may
be available.

### Book Ratings and Reviews

These endpoints enable authenticated users to rate and review books they have read. Only authenticated users can access
these endpoints, meaning that the application should redirect unauthenticated users to the sign-in page.

| Endpoint               | Method |
|------------------------|--------|
| /catalogue/{id}/rate   | PUT    |
| /catalogue/{id}/rate   | DELETE |
| /catalogue/{id}/review | PUT    |
| /catalogue/{id}/review | DELETE |

You must allow users to submit ratings (out of 5 stars) and write text reviews for books in the catalogue. Users can
only
provide one rating and review per book, but they must be able to delete and update them.

> **IMPORTANT:** Notice that, since users can update ratings and reviews, the corresponding endpoints don't use the POST
> method, but rather PUT.

You're free to choose how to allow the users to interact with these endpoints. We suggest one of the following:

- Add UI elements to the Book Details page that users can use to transparently submit requests to the corresponding
  endpoints. For instance, add a 5-star bar where the user can click on a star to submit, update or remove their rating
  (you can use hover effects to indicate what action will take place).
- Define forms that users can access by submitting GET requests to the endpoints (`/catalogue/{id}/rate` and
  `/catalogue/{id}/review`) which allow the user to either submit a new rating/review or update/delete their current
  one,
  depending on whether it already exists.

### Discussion Forums (API)

The last major section of the project presents an additional challenge. Given that we want to be able to develop an
independent mobile application in the future that allows users to access our Forums, we have decided that this part will
be implemented as a REST API.

The API specs are established in a [swagger file](resources/swagger.json) which you can find in the `resources`
directory. To help you understand the spec you can copy the JSON into the official visualizer, which is found at
[https://editor-next.swagger.io/](https://editor-next.swagger.io/). Some IDEs also allow you to visualize the file
natively (or via the use of a plugin).

To learn more about Swagger, check out their documentation [here](https://swagger.io/docs/specification/about/).

To interact with the API from the front-end, use [jQuery AJAX](https://api.jquery.com/jQuery.ajax/) or the modern and
[widely-supported](https://caniuse.com/fetch) native
[fetch API](https://developer.mozilla.org/en-US/docs/Web/API/Fetch_API).

These functionalities will be tested using cypress. Once they are public, you will be able to see the tests in the
`cypress/e2e` directory.

#### Discussion Forums

This section allows users to engage in discussions about books. Only authenticated users can access these endpoints,
meaning that the application should redirect unauthenticated users to the sign-in page.

In the case of API endpoints, a 401 status code must be returned when accessing them without being authenticated.
Similarly, if a user tries to access an API endpoint without having a defined username, a 403 status code must be
returned. In both cases, the JSON body will contain an error message and the front-end JS code will perform the
corresponding redirect.

| Endpoint         | Method |
|------------------|--------|
| /forums          | GET    |
| /api/forums      | GET    |
| /api/forums      | POST   |
| /api/forums/{id} | GET    |
| /api/forums/{id} | DELETE |

All endpoints starting with `/api` must follow the Swagger specification, meaning that they will only accept / return
content in JSON format. Make sure to validate the content properly and return adequate status codes and JSON messages
when an error is detected.

The `/forums` endpoint must render an HTML page containing the skeleton to be able to render a list of forums, as well
as a form to create a new forum with the corresponding information (title and description). It must also include
JavaScript code that allows the user to interact with the REST API (that is, the other endpoints).

In other words, the included JS code should obtain a JSON list of currently existing forums from the `/api/forums` GET
endpoint and render the corresponding information in the DOM, as well as handle the submission of form data to
the `/api/forums` POST endpoint.

When the user clicks on a forum from the list they must be able to see a list of its posts, described in the next
section.

#### Forum Posts

This section describes how users can see and interact with the posts in a forum. Only authenticated users can access
these endpoints, meaning that the application should redirect unauthenticated users to the sign-in page.

In the case of API endpoints, a 401 status code must be returned when accessing them without being authenticated.
Similarly, if a user tries to access an API endpoint without having a defined username, a 403 status code must be
returned. In both cases, the JSON body will contain an error message and the front-end JS code will perform the
corresponding redirect.

| Endpoint                    | Method |
|-----------------------------|--------|
| /forums/{id}/posts          | GET    |
| /api/forums/{id}/posts      | GET    |
| /api/forums/{id}/posts      | POST   |

All endpoints starting with `/api` must follow the Swagger specification, meaning that they will only accept / return
content in JSON format. Make sure to validate the content properly and return adequate status codes and JSON messages
when an error is detected.

The `/forums/{id}/posts` endpoint must render an HTML page containing the skeleton to be able to render the details of
that forum (name and description), a list of its posts as well as a form to submit a new post from the corresponding
information (title and contents). It must also include JavaScript code that allows the user to interact with the
REST API (that is, the other endpoints).

In other words, the included JS code should obtain the JSON information of the current forum from the `/api/forums/{id}`
GET endpoint, as well as a JSON list of posts from the `/api/forums/{id}/posts` GET endpoint, rendering the
corresponding information in the DOM. It should also handle the submission of form data to the `/api/forums/{id}/posts`
POST endpoint.

> **NOTE:** The non-API GET endpoint (`/forums/{id}/posts`) must not only render the contents of each post, but also
> the username and profile picture of the user that posted it (also known as the OP, short for "original poster"). 
> Note that the corresponding API endpoint already should return this information, simplifying the process.

## Submission

You must submit this exercise in two different ways:

- **Git:** You will use Git and annotated tags to release new versions of your application. Tags should be pushed to 
  the Bitbucket repository to make them accessible to your team and instructors. Use **tag** `v1.0.0` for the final
  version.

- **eStudy:** For academic reasons, we also require you to upload a zipped copy of the final repository version to the
  eStudy.

 Make sure that everything needed to properly run your code is present in the repository (including the `schema.sql` 
 file to automatically set up the database), while avoiding committing any unnecessary files (such as the contents of 
 the vendor folder or user uploads). In case you commit unnecessary files, know that they will stay in the 
 repository's history, making your `.git` directory larger.

You can only submit this project in the ordinary call. The deadline is May 19. 

> **IMPORTANT:** Make sure to include a `INSTRUCTIONS.md` file with any information you deem necessary for us to
> understand how to use your application. This is particularly important when it comes to functionalities where you
> have creative control or multiple implementation options are proposed, as we won't know what choices you made during
> the development process.

## Evaluation

1. To evaluate the project, we will use the release `v1.0.0` of your repository.
2. In May, all the teams that have delivered the final release on time will be interviewed by the teachers.
3. In this interview, we will validate that each team member has actively contributed to the project as expected.
4. Check the syllabus of the subject for further information.

### Evaluation Criteria - `v1.0.0`

To grade the release `v1.0.0`, the distribution of points is as follows:

- Landing Page (Including semantic HTML, CSS of the whole project): 0.5p
- Navigation: 1p
- User Registration and Login: 1p
- User Profile: 1.5p
- Book Catalogue: 1.5p
- Book Details: 0.5p
- Book Ratings and Reviews: 1.5p
- Discussion Forums: 2.5p
- Other criteria (clean code quality, clean design, etc.): -1p