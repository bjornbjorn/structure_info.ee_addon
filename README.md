Structure Info
=======================

Structure info is an extension for Structure that will add some additional structure tags to the standard channel:entries tag

Currently available (inside any {exp:channel:entries} loop)
```
* {structure_info:page_uri} - page_uri variable (ie. "/about-us/employees/frank")
* {structure_info:page_last_segment} - last segment of page (e.g. would be "frank" in the above page_uri example)
