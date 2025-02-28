# PHP 8 Migration Tools

PHP 8 introduced several changes to how type-loose comparison operators behave. These changes can lead to inconsistencies and unpredictable behavior when running PHP 7 code on PHP 8. 
This toolbox helps maintainers of PHP codebases migrate their applications smoothly by identifying usages of type-unsafe operators that would otherwise have changed the behavior of their application.

## Key Features

- **Automatic Replacement of Comparison Operators:** Replace type-loose comparison operators (e.g., `==`, `!=`) with new custom replacement functions (like `c_eq` for `==`, `c_ne` for `!=`).
- **Behavior Notification:** Replacement functions notify developers if the behavior would change due to the differences introduced in PHP 8.
- **Safe Migration:** Minimize errors caused by PHP version mismatches by emulating PHP 7 behavior, while improving awareness of potential edge cases in code.

## Getting Started

Make sure to read our article first! It explains the overall idea behind the migration tool. In a few words it can be explained like this:

- add this tool to your PHP 7 project
- configure how you want to receive notifications
- replace type-loose operators with compatibility/notification functions using [Rector](https://getrector.com/)
- run as many tests as you can, then deploy your app to production
- replace incompatible changes permanently (with a notification-free function, such as `StringToNumberComparison::eq()`, or have a closer look)
- use Rector to roll compatibility/notification functions back to the original type-unsafe operators
- uninstall this library 🎉

### Prerequisites

- PHP **>=7.2** must be installed to run these tools.
- Dependency management via **Composer** is recommended.

### Installation

Add this library to your project using Composer. Add this snippet to your project's `composer.json`:

```json5
{
  // ...
  "repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/onOfficeGmbH/php8migrationtools.git"
    }
  ],
  "require": {
    "onoffice/php8migrationtools": "dev-master"
  },
  "require-dev": {
    "rector/rector": "^0.14.2"
  }
}
```

Now, run `composer install`.

### Setting up the Project

In order to get this running, you're going to have to implement two classes:

The first class implements `\onOffice\Migration\Php8\DropIns\Interfaces\DebugMode`. 
`DebugMode` tells the tooling whether to throw a `\onOffice\Migration\Php8\DropIns\Exception\Php8MigrationException`
or to get notified while script execution goes on uninterrupted.

A simple way to implement the interface is to return true and therefore always throw an exception:

```php
<?php

use onOffice\Migration\Php8\DropIns\Interfaces\DebugMode;

class MyDebugMode implements DebugMode
{
    public function isEnabled(): bool
    {
        return true;
    }
}
```

The next interface to implement is `onOffice\Migration\Php8\DropIns\Interfaces\IncidentManager`. 
`IncidentManager` will be triggered if `DebugMode->isEnabled()` returns `false`.

A simple `IncidentManager` that writes to the server's error log may look like this:

```php
<?php

use onOffice\Migration\Php8\DropIns\Interfaces\Incident;
use onOffice\Migration\Php8\DropIns\Interfaces\IncidentManager;

class ErrorLogIncidentManager implements IncidentManager
{
    public function handle(Incident $pIncident): void
    {
        error_log(
            $pIncident->getSubject()
            ."\n\n"
            .$pIncident->getMessage()
        );
    }
}
```

Now it is time to configure the specific implementations for their respective interfaces. Add this block
somewhere into the bootstrap section of your app (but after `require 'vendor/autoload.php';`):

```php
use onOffice\Migration\Php8\DiSystem\StaticDI;
use onOffice\Migration\Php8\DropIns\Interfaces\DebugMode;
use onOffice\Migration\Php8\DropIns\Interfaces\IncidentManager;

StaticDI::configure([
    DebugMode::class => MyDebugMode::class,
    IncidentManager::class => ErrorLogIncidentManager::class,
]);
```

Now you're all set for the first big refactoring!

### Refactoring

You're going to want to copy our [example rector.php](tests/Examples/rector.php) and use it for your own refactoring.

It is important to exclude the `vendor` directory from refactorings. Depending on the architecture of your application, you'll
want to exclude bootstrapping code as well, e.g. code that runs before autoloading.

A simple example for the `paths` and `skip` method calls can be seen below.

```php
    $rectorConfig->paths([
        // Where to refactor
        __DIR__,
    ]);

    $rectorConfig->skip([
        __DIR__.'/vendor/',
    ]);
```

**Make sure you add our Rector rules to your config.** Set the variable `$phase3Start` to `false` to roll out the compatibility functions.

Once done, run Rector like so: `vendor/bin/rector --config rector.php`. This will take a while.

Congrats, you'll get notified about possible behavioral changes introduced by PHP 8. 😎

To fix them, you can choose the hard way and actually _fix_ them and replace them with a type-safe operator, or you insert the
non-notifying polyfill. The more permanent stand-in for `==` (which Rector replaced with the notifying `c_eq()`), would be
`onOffice\Migration\Php8\DropIns\StringToNumberComparison::eq()`.

#### Example Replacement

The following example shows a typical replacement done by Rector using the rules contained in this repository:

**Original (PHP 7):**

```php
if ($value == '') {
    echo 'Equal!';
}
```

**After replacement (PHP 7/8 compatible):**

```php
if (c_eq($value, '')) {
    echo 'Equal!';
}
```

The `c_eq` function emulates the `==` behavior but includes additional safety checks and notifications about behavior changes in PHP 8.

### Testing Your Code

After making changes, run your test suite to ensure everything works as expected. Check logs for output generated by this tool.

### Rolling back
Once you have addressed all messages from notification functions, and you feel ready to get back to the bare PHP 8 comparison functions, 
you may want to use the Rector rules included in this project to roll back.

To do so, open your copy of the example Rector config and set `$phase3Start` to `true`. This will roll back the `c_*` comparison and replacement functions, 
but will leave `StringToNumberComparison::` in the code.

Run Rector. Your code base is switched back to using the real operators, such as `==` and `!=` again now.

## Contributing

Contributions are welcome! If you have ideas or improvements, feel free to submit pull requests or open issues.

### Steps to Contribute

1. Fork the repository.
2. Create a new branch for your feature/fix: `git checkout -b feature-name`.
3. Commit your changes: `git commit -m 'Add your feature'`.
4. Push to the branch: `git push origin feature-name`.
5. Submit a pull request.

Please make sure to add/update tests for any changes.

If you make changes to the test matrix in `StringToNumberComparisonTest`, please make sure
to rebuild the [test expectations file](tests/DropIns/StringToNumberComparisonExpectations.php) by deleting it and re-running the test on **PHP 7**.
Commit the newly generated file back into Git.

## License

This project is licensed under the [MIT License](LICENSE).

## Support & Feedback

If you encounter any issues or have questions, feel free to open an issue on the repository or contact us directly.

Happy migrating! 🚀