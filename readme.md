# Spectyr Framework

### Overview

The Spectyr Framework is an ultra lightweight MVC framework for PHP.

**Some useful aspects to this idea are:**

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

4. Navigate to the project host via the browser.

### Views / Layouts

The framework supports a custom templating system. In order to access, you'll want
to first verify that a `resources/views` directory exists within your project. Afterwards,
you can start creating new views as needed.

```html
<!doctype html>
<html lang="en">
    <head>
        <?= $this->getSection('head'); ?>
    </head>

    <body>
        <?= $this->getSection('body'); ?>
    </body>
</html>
```

To extend existing layouts, you can reference the layout method, pointing to an
existing layout.

**Extension Example**

```html
$this->layout('layouts/master')
    ->setSection('body', function(){
        ?>
        <div>Hello World!</div>
        <?php
    })->render();
```

In both examples, `$this` will refer to your response object. Variables passed in from
your controllers are also accessible by name, as provided by your controller responses.

**Example Controller**

```php
/**
 * Class ExampleController
 *
 * @package App\Controllers
 */
class ExampleController extends Controller
{
    /**
     * Returns a generic welcome view.
     *
     * @return \App\Core\Response\View
     */
    public function welcome()
    {
        return view('controllers/example/welcome', [
            'greeting' => 'Hello World!'
        ]);
    }
}
```

**Example Layout**

```html
$this->layout('layouts/master')
    ->setSection('body', function() use ($greeting){
        <?= $greeting ?>
    })->render();
```