# Hiawatha Login Protection

Plugin to ban repeated failed login attempts on [Hiawatha](https://www.hiawatha-webserver.org/) servers via the [BanByCGI](https://www.hiawatha-webserver.org/manpages/hiawatha). 

You need to make sure you've got the BanByCGI option enabled and setup correctly in your Hiawatha configuration file.

    BanByCGI = yes|no[, <max value>]

## Installation

This plugin isn't listed on the WordPress plugins directory (I may add it at somepoint) so the best way is to download the zip file of this project and extract it to the `wp-content/plugins` directory and rename the folder it's extracted into to 'hiawatha-login' though, for the most part WordPress should pick up the plugin just fine.

## Default options:

The standard options are:

* 5 invalid login attempts
* within a 5 minute period
* will ban a user for 5 minutes

It stores the logins in a table, stores the IP and the time of the incorrect login.


## Notes

If you have any other plugins that alter logins, or interact with the WordPress authentication system, this plugin may not function as expected.

Also, if another function implements this function already, this plugin won't do anything.

### Roadmap / Items to be added

* Configurable options (a screen to set your own threshold and timeout values)
* A way to clear out old log entries from the entries table


I'm not a WordPress dev, this is just a bit of tinkering to get something basic working.