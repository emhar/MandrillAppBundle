# Emhar MandrillApp Bundle

Send template email from Mandrill App in Symfony3 apps.

## Installation with composer

### Step one: Add a repository to composer

To add a new repository, add this lines in your composer.json
```json
{
    ...
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/emhar/MandrillAppBundle.git"
        }
    ]
    ...
}
```

### Step two: Download bundle
Open a command console, enter your project directory
and execute the following command to download the latest stable version of this bundle:
```bash
$ composer require emhar/mandrill-app-bundle
```
This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 3: Enable the Bundle

Then, enable the bundle by adding the following line in the app/AppKernel.php file of your project:

```php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Emhar\MandrillAppBundle\EmharMandrillAppBundle(),
        );

        // ...
    }

    // ...
}
```

### Step 4: Update your config.yml
```
emhar_mandrill_app:
    api_key: YOUR_MANDRILL_APP_KEY
```

Special case in during developments
```
emhar_mandrill_app:
    api_key: YOUR_MANDRILL_APP_KEY
    test_email: YOUR_EMAIL
```
If test_email is set, all email messages will be sent to this address instead of being sent to their actual recipient.

## Usage

### Dependency injection
This bundle provide a symfony service : ```emhar_mandrill_app.mailer```

### Send an email from mailer service

```php
->sendTemplateMail(
    RECIPIENT_EMAIL,
    TEMPLATE_MAIL,
    TEMPLATE_PARAMETERS
);
```

* Example

```php
->sendTemplateMail(
    $user->getEmail(),
    'inscription-confirmation',
    array(
        'NAME' => $user->getName(),
        'DATE' => (new \DateTime)->format('Y-m-d')
    )
);
```

### Logging

All log messages are redirected to a channel named ```mandrillapp```

Log levels:
* On success: ```notice```
* On errors: ```error```