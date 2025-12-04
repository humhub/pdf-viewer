# Web Server Configuration Note

Some environments may require additional web server configuration to correctly serve modern JavaScript module files (.mjs) used by this module. If your PDF viewer does not load properly, ensure that your server is configured to deliver .mjs files with the correct MIME type.

## Apache (.htaccess or server config):

```
AddType text/javascript .mjs
```

## NGINX (server block):

``` 
types {
    application/javascript mjs;
}
```
Depending on your setup, you may need to reload or restart your web server after applying these changes.