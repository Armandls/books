# CryptoNews

Welcome to CryptoNews, a web application that provides the latest news and updates in the world of cryptocurrencies.
In this project, you'll create a platform where users can stay informed about the latest trends, news articles, and market updates related to cryptocurrencies.
The application will have user authentication and authorization features, allowing users to sign up, sign in, and personalize their experience.

## Prerequisites

To set up and run this web app, you'll need the following:

1. Web server (Nginx)
2. PHP 8
3. MySQL
4. Composer

Please use the provided Docker `ca2-cryptonews-environment` setup.

## Requirements

1. Use Slim as the underlying framework.
2. Create and configure services in the `dependencies.php` file. Examples of services are repositories, Twig, Flash, ...
3. Use Composer to manage all the dependencies of your application. There must be at least two dependencies.
4. Use Twig as the main template engine.
5. Use MySQL as the main database management system.
6. Use Object-Oriented Programming principles, including Namespaces, Classes, and Objects.

## Resources

### MySQL

Use the provided [schema.sql](./docker-compose/mysql/schema.sql "Schema SQL") file in the `docker-compose/mysql` folder to create the tables in the MySQL database.

## Exercise

To complete the exercise, you need to create the following pages:

1. Sign-up
2. Sign-in
3. Homepage
4. News Articles
5. Market Updates

### Sign-up

This section describes the process of signing up a new user into the system.

| Endpoints  | Method |
| ---------- | ------ |
| /sign-up   | GET    |
| /sign-up   | POST   |

When a user accesses the **/sign-up** endpoint, you need to display the registration form. 
The information from the form must be sent to the same endpoint using a **POST** method. 
The registration form should include the following inputs:

- Email - required. This must be a `text` field in HTML.
- Password - required.
- Repeat password - required.
- numBitcoins - optional . This must be a `text` field in HTML. The number must not be negative and also must not be greater than 40000.

When a **POST** request is sent to the **/sign-up** endpoint, you must validate the information received from the form 
and sign up the user only if all the validations have passed. The requirements for each field are as follows:

- Email: It must be a valid email address (@salle.url.edu). The email must be unique among all users of the application.
- Password: It must not be empty and must contain at least 7 characters, at least one number and both upper and lower case letters.
- Repeat password: It must be the same as the password field.

If there are any errors, you need to display the sign-up form again. All the information entered by the user should be kept
and shown in the form (except for password fields) together with all the errors below the corresponding inputs.

Here are the error messages that you need to show respectively:

- Only emails from the domain @salle.url.edu are accepted.
- The email address is not valid.
- The email address is already registered.
- The password must contain at least 7 characters.
- The password must contain both upper and lower case letters and numbers.
- Passwords do not match.
- Sorry, the number of Bitcoins is either below or above the limits.
- The number of Bitcoins is not a valid number. 

Once the user's account is created, the system will allow the user to sign in with the newly created credentials.

### Sign-in

This section describes the process of logging into the system.

| Endpoints  | Method |
| ---------- | ------ |
| /sign-in   | GET    |
| /sign-in   | POST   |

When a user accesses the **/sign-in** endpoint, you need to display the sign-in form. 
The information from the form must be sent to the same endpoint using a **POST** method. 
The sign-in form should include the following inputs:

- Email - required. This must be a `text` field in HTML.
- Password - required.

When the application receives a POST request to the **/sign-in** endpoint, it must validate the information received
from the form and attempt to log in the user. The validations for the inputs should be the same as in the registration.

If there are any errors or if the user does not exist, you need to display the form again with all the information 
provided by the user and display the corresponding error.

Here are the error messages that you need to show respectively:

- The email address is not valid.
- Only emails from the domain @salle.url.edu are accepted.
- Your email and/or password are incorrect.
- The password must contain at least 7 characters.
- The password must contain both upper and lower case letters and numbers.
- User with this email address does not exist.

After logging in, the user will be redirected to the homepage.

### Homepage

| Endpoints | Method |
|-----------| ------ |
| /         | GET    |

The homepage will display a welcome message. The contents of this page will change depending on whether the user is authenticated or not.

If the user is logged in the homepage will display a personalized welcome message with the user's username,
which must be taken from their email address. For example, for a user with the email "pwIsNice@salle.url.edu" 
the homepage will show "Hello pwIsNice!". 

If the user isn't authenticated the homepage will show "Hello stranger!" as a generic welcome message.

### News Articles

| Endpoints | Method |
|-----------| ------ |
| /news     | GET    |

The news articles page should display a list of articles related to cryptocurrencies 
(user must be logged in, if not, redirect to sign-in page). Each article should include the following information:

- Title
- Publication date
- Author
- Summary


Feel free to style the articles as you want. You can use both placeholder data or real data from an API (choose whichever you want).

If user is not logged in you must redirect the user to the sign-in page, where the following message must appear:

- You must be logged in to access the news page.

### Market Updates

| Endpoints | Method |
|-----------| ------ |
| /mkt      | GET    |

The market updates page should display the current market price of at least 1 cryptocurrency. 
You can use placeholder text or real data from external APIs to populate these section (cryptocurrency name and price).

If the user is not logged in, this page should display a generic welcome message, 
specifically "Welcome to CryptoNews! Login if you want to see your updated data.". Otherwise, 
the page title should be "Market Updates" and the page content should be the user's crypto balance 
(introduced in the user registration form) and, as said before, the current market price of at least 1 cryptocurrency.
Feel free to customize the page as you want.

## Tests

To check the validity of this exercise, we will be using [Cypress](https://www.cypress.io/), 
a JavaScript End-to-End Testing Framework. You can read more on how to use it in the [help file](HELP.md). 

For the tests to work, you need to add custom attributes to HTML elements in the following format:

```
data-cy=""
```

In the Sign-up page, you MUST add the following attributes:
```
<form data-cy="sign-up">
    <input data-cy="sign-up__email">
    <input data-cy="sign-up__password">
    <input data-cy="sign-up__repeatPassword">
    <input data-cy="sign-up__coins">
    <input data-cy="sign-up__btn">
    <span data-cy="sign-up__wrongEmail"></span>
    <span data-cy="sign-up__wrongPassword"></span>
    <span data-cy="sign-up__wrongCoins"></span>
</form>
```

As you can see, the `data-cy` attribute is different for each input, including the form itself. Your HTML can (and should probably) have more attributes, as well as other elements,
but these `data-cy` attributes must exist for the tests to work.

In the Sign-in page, you MUST add the attributes for the following pages:


```
<form data-cy="sign-in">
    <input data-cy="sign-in__email">
    <input data-cy="sign-in__password">
    <input data-cy="sign-in__btn">
    <span data-cy="sign-in__wrongEmail"></span>
    <span data-cy="sign-in__wrongPassword"></span>
</form>
```

In the Homepage, add the following attribute:

```
<h1 data-cy="home__welcomeMsg"></h1>
```

In the News Articles page, add the following attributes:

```
<h1 data-cy="news-articles__title">News Articles</h1>
<ul data-cy="news-articles__list">
    <li data-cy="news-articles__item">
        <h2 data-cy="news-articles__item-title"></h2>
        <p data-cy="news-articles__item-date"></p>
        <p data-cy="news-articles__item-author"></p>
        <p data-cy="news-articles__item-summary"></p>
    </li>
    <!-- Repeat the above structure for each news article -->
</ul>
```

In the Market Updates page, add the following attribute:

```
<h1 data-cy="market-updates__title">Market Updates</h1>
<p data-cy="market-updates__user-cryptobalance"></p>
<ul data-cy="market-updates__list">
    <li data-cy="market-updates__item">
        <h2 data-cy="market-updates__item-title"></h2>
        <p data-cy="market-updates__item-price"></p>
    </li>
    <!-- Repeat the above structure for each cryptocurrency if needed -->
</ul>
```

### Considerations

1. The endpoints described in the previous sections MUST be used exactly as is. You cannot use **/register** or
   **/login**. These endpoints will make the tests fail.
2. If any of the given tests fails, then you will know which feature or validation does not work in your code.
3. Do not modify the tests. Any modification in the tests will surely make the tests fail during the grading of your
   deliverable.

## Delivery

### Format

You must upload a .zip file with the filename format `AC2_<your_login>.zip` containing all the code to the eStudy.
