# Jokes One API
A client for popular jokes.one API. Jokes one offers plenty on the jokes area [Joke of the day](https://jokes.one/joke-of-the-day/), [Knock Knock Jokes](https://jokes.one/tag/knock-knock/), [Blonde Jokes](https://jokes.one/tag/blonde/), [Chuck Norris](https://jokes.one/tag/chuck-norris/) and more.

This repository aims to collect different clients (php, python, swift, java, javascript etc) for jokes one API.

## PHP

### Installation

Use [Composer](https://getcomposer.org/) to install the library.

``` bash
$ composer require orthosie/jokes-api
```

### Basic usage

```php
use Jokes\One\RestClient;

$jorc = new RestClient();

$jo = $jorc->joke_of_the_day();
$joc = $jorc->joke_of_the_day_categories();

print_r($jo);
print_r($joc);

print "Limit : " . $jorc->rate_limit_limit();
print "Remaining : " . $jorc->rate_limit_remaining(); 
```
