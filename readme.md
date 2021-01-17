# Spectyr Framework

### Overview

The Spectyr Framework is an ultra lightweight MVC framework for PHP and VueJS.

Some useful aspects to this idea are:

- Routing and Controller Support.
- Inclusive, yet easy-to-use ORM system.
- Simple View Templating.
- Middleware Support. 
- Basic Session Management.
- Additional Response Types for JSON APIs, File Responses, and more.

### Installation Steps

##### Use the following steps for new project installations.

1. Copy the ./framework/bin/example project adjacent to the framework folder in your local server directory (renaming the 
root directory accordingly).

    ```
    Example:
    
    -> ./wamp64/www/
        -> spectyr/
            -> framework/
                -> bin/
                -> src/
                -> ..
        -> {your-project}/
            -> app/
            -> index.php
            -> ..
    ```

2. Navigate to the project via command line.

3. Run the following installation commands:

    ```
    composer install
    composer require spectyr/framework
    npm install
    npm run production
    ```

7. Navigate to the project host via the browser.

