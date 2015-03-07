# Hiawatha Login Protection

Plugin to ban repeated failed login attempts on [Hiawatha](https://www.hiawatha-webserver.org/) servers via the [BanByCGI](https://www.hiawatha-webserver.org/manpages/hiawatha). 

You need to make sure you've got the BanByCGI option enabled and setup correctly in your Hiawatha configuration file.

    BanByCGI = yes|no[, <max value>]
    
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