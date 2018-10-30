[![](https://travis-ci.org/bulton-fr/bfw.svg?branch=3.0)](https://travis-ci.org/bulton-fr/bfw "Build Status")
[![Coverage Status](https://coveralls.io/repos/github/bulton-fr/bfw/badge.svg?branch=3.0)](https://coveralls.io/github/bulton-fr/bfw?branch=3.0)
[![](https://scrutinizer-ci.com/g/bulton-fr/bfw/badges/quality-score.png?b=3.0)](https://scrutinizer-ci.com/g/bulton-fr/bfw/?branch=3.0 "Scrutinizer Code Quality")
[![](https://poser.pugx.org/bulton-fr/bfw/v/stable)](https://packagist.org/packages/bulton-fr/bfw "Latest Stable Version")
[![](https://poser.pugx.org/bulton-fr/bfw/v/unstable)](https://packagist.org/packages/bulton-fr/bfw "Latest Unstable Version")
[![](https://poser.pugx.org/bulton-fr/bfw/license)](https://packagist.org/packages/bulton-fr/bfw "License")
[![](https://poser.pugx.org/bulton-fr/bfw/downloads)](https://packagist.org/packages/bulton-fr/bfw "Total Downloads")

BFW is a framework makes for web projects create with PHP.

It is compatible with PHP >= 7.0
The full list of supported version is on [travis-ci](https://travis-ci.org/bulton-fr/bfw "bfw builds on travis-ci").

## Why a new framework / The origin :

The origin of this framework is the need to have a code base that I completely mastered, which is not heavy, but is flexible.
You need to remember it's at the time where SF2 existing since a few months only, so the choice was ZF1 or home-made.
ZF1 was very heavy (the light version was a weight of 8 Mo !), and I never liked SF.
Furthermore, I like core development, so my choice to create my own framework was obviousness.

When I had created the framework, and during its evolution, I want to keep two main principles :
* Lightness : Do not become like ZF1 with much Mo of code !
* Flexibility : Choose to use lib we want and never force some libs.

With time, many systems need to have application cache to run (thanks to SF and WP), so a third principle has been added :
* Speed : Never need to have Mo of cache, a Varnish and load balancing to display a landing page in less than a minute.

## Implementation of these 3 principles

### Lightness

Really simple. Never add things which will never be used by the majority of web projects.

So we have a framework that integrates very few things (for lightness).
And all missing things can be added by modules.
Indeed, it's the reason why the framework not integrate systems for MVC.
There are to add by modules which are called "core module" because yes, the MVC principle is clearly recommended for web projects. ;)

### Flexibility

I think not to have wrong when I say that each developer has his uses.
From way to code to lib he wants to use.

For me the flexibility is mainly the choice of lib we want to use.
It's the reason why the framework not integrate systems for MVC.
It's to take the possibility to use different libs for each project.

To doing that, the framework integrates a module system.
There are two different types of modules.
First is "cores" modules, used for MVC system.
All others are "application" modules.

Another thing that permits to have some flexibility inside module is the usage of
the [design pattern Observer](https://en.wikipedia.org/wiki/Observer_pattern).
It has been integrated internally into the system and allow modules to be notified of all events during the initialisation step.

Some modules already exist : you have [a list](./how-it-works/existing-modules.md).

### Speed

The base idea is not to have the necessity to have cache so that the system continues to react correctly and quickly
However, if you have a big traffic like several thousand page views per hour, maybe it can be a good idea to have cache...

Yes is true, PHP have an OPcache into its core (since PHP 5.5) so that helps.
But I think there is no need more.

I did not bother to do a benchmark of speed with others famous framework.
I don't want to play who at the biggest.
Especially because test an empty framework with default page it's a thing. But tests in real condition, with the full project is another thing...

I think each people can create its own opinion about the speed of each system.
Some person will want numbers; but keep in mind that your users don't care about your number and a nanosecond difference.
The only thing that will matter for him, it's their own feel when they use your website.
And don't forget you developers ! Their feel when they create new functionality or maintain the project is important too.
