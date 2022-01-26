# Slim Bridge for ExpressionEngine

This is probably only of interest to me, but you're welcome to use this project if you come across it and like it.

I really like working in [Slim](https://www.slimframework.com), with [FastRoute](https://github.com/nikic/FastRoute), and PSR standard interfaces. To that end, I've created this extremely simple ExpressionEngine Extension that let's me do the front-end of EE sites in Slim and mostly ignore that EE is there in the background.

## Here's how to use it:

Since you're wanting to use Slim and PHP and PSR interfaces, it is assumed that you already have composer setup and are loading the vendor autoload.

1. In your craft project, run `composer require buzzingpixel/ee-slim-bridge`
2. Symlink or Docker mount or what ever you want to use `vendor/buzzingpixel/ee-slim-bridge/src/ExpressionEngine` to `system/user/addons/slim_bridge`
3. Install the extension in the ExpressionEngine control panel
4. Set up the configuration in your ExpressionEngine config file based on the examples in the [next section]((#config).

## Config

In your config file, all configuration goes in an array key named `'slimBridge'`.

Three keys are available in the config file ([See example](examples/config.php)):

### `enabled`

If this is not set to `(bool) true`, `(string) 'yes'`, `(string) 'y'`, `(string) '1'`, or `(int) 1`, then Slim Bridge will not serve the front-end via Slim. This _could_ be theoretically useful if you want to do some logic or other to determine whether a request should be sent to Slim or let ExpressionEngine serve the request as normal.

### `containerInterface`

Slim requires an implementation of `\Psr\Container\ContainerInterface` and I've left this to project owners to provide. [I've written what I think is a pretty good container implementation](https://github.com/buzzingpixel/container), and it is required as part of the project for internal use, so you could certainly configure and return an instance of that container.

### `appCreatedCallback`

This is optional, but if you don't use it, then you won't have set any routes or middlewares and it will be useless to you. Once the app has been created, this callback will be run and the argument received will be the Slim App instance which you can use to set routes and middleware.
