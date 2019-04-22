# HH-CMS

Hack is a practical web language and nobody will convince me otherwise.

The framework popped into existence alongside <https://lam.io>, and was the only way I could pull off making it in basically two weeks before application deadlines. Hack really saved my skin on this one. These good bits especially:

- [XHP](https://docs.hhvm.com/hack/XHP/introduction) is an inline XML syntax that produces first-class objects that are _actually typechecked_, which is just fantastic bar-none. React integration too if you want it. All the HTML on <https://lam.io> is rendered through XHP.
- [FBMarkdown](https://github.com/hhvm/fbmarkdown) is a complete and transparent Markdown library. It was a piece of cake to work with the AST for the basics, so I extended with an "aside" element &mdash; not much effort required either.
- [HSL](https://github.com/hhvm/hsl) is the new set of operators for HHVM 4.0 on collections and async that replace the older Hack collection methods and supply the functionality from PHP's array functions. I really think Hack has one of the more versatile inheritance, visibility and variance systems. And chaining is just plain fun. (Enjoy all the [`|>`](https://docs.hhvm.com/hack/expressions-and-operators/pipe))

Everything is here to make the library run generally, except for everything under [`hh-src/shadow-public`](https://github.com/acrylic-origami/hh-cms/tree/master/hh-src/shadow-public),  [`hh-src/pages`](https://github.com/acrylic-origami/hh-cms/tree/master/hh-src/shadow-public) and and [`public/`](https://github.com/acrylic-origami/hh-cms/tree/master/public) which are [lam.io](https://lam.io)-specific and are there as examples.

## Usage

A bit of a kludge is using PHP to run Composer now that we've got Hack &ne; PHP strongly. But what can you do.

```bash
$ composer install # i.e. php /path/to/composer.phar install
$ ./hhvm
```

HHVM is now running a FastCGI server on localhost:9000. Hit it however you wish.

### Apache

I personally have an Apache install on [lam.io](https://lam.io) which I've included in this repo if you're a fellow Apache user.

First, in [`apache/hh-cms.conf`](https://github.com/acrylic-origami/hh-cms/tree/master/hh-src/shadow-public) replace all instances of `/path/to/hh-cms/` with... well, the path to hh-cms. Then copy it into your Apache2 config directory:

```
$ mkdir -p /etc/apache2/vhosts
$ sudo cp apache/hh-cms.conf /etc/apache2/vhosts/hh-cms.conf
```

Then edit your `httpd.conf` or `apache2.conf` to `Include` that `hh-cms.conf`.