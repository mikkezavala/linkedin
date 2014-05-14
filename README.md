Twitter Connect
===============

A Basic & Light LinkedIn Connector for Laravel 4.1

Please contribute!



Installation
------------

Using Composer [composer](https://getcomposer.org/download/).

You need to add the repo for the project first in your composer.json:

```
	"repositories": [
        {
            "type":"vcs",
            "url": "https://github.com/mikkezavala/linked.git"
        }
    ]
```

In the same require the package:

```
	{
	    "require": {
			"mikke/linked-in": "0.0.1",
	    }
	}
```

Then just run a composer update

```
	$ composer update	

```

You can add the alias in your app/config/app.php

```php

		'LinkedIn'  		  => 'Mikke\LinkedIn\LinkedIn',
```


.... Work In process
Contribute
----------

This is just a simple connector, you can fork me, or contribute to this, the idea is this to make it easier, and collaborative, in the next week i'll be releasing more connectors
		
