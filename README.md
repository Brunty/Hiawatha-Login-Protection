# Hiawatha Login Protection

Plugin to ban repeated failed login attempts on [Hiawatha](https://www.hiawatha-webserver.org/) servers via the [BanByCGI](https://www.hiawatha-webserver.org/manpages/hiawatha). 

You need to make sure you've got the BanByCGI option enabled and setup correctly in your Hiawatha configuration file.

    BanByCGI = yes|no[, <max value>]g
    
## Notes

If you have any other plugins that alter logins, or interact with the WordPress authentication system, this plugin may not function as expected.

Also, if another function implements this function already, this plugin won't do anything.