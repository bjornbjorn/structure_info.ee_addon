Structure Info
=======================

Structure info is an extension for Structure that will add some additional structure tags to the standard channel:entries tag

Currently available (inside any {exp:channel:entries} loop)

* {structure_info:page_uri} - page_uri variable (ie. "/about-us/employees/frank")
* {structure_info:page_last_segment} - last segment of page (e.g. would be "frank" in the above page_uri example)

The variables will be empty if the entry is not a page.

The variables above require no db queries so there is hardly any overhead.

In addtion to the ones above you can retrieve a string with the breadcrumb:

* {structure_info:path} - breadcrumb style path string (ie. Products > Flammables > Matches)

To enable this you need to add _include_structure_path="y"_ to your channel:entires tag. In addition you may specify the separator used with structure_path_separator="", ie:

{exp:channel:entries channel="products" include_structure_path="y" structure_path_separator=">"}

(> as separator is default so if that's what you want to use you do not need to specify it)

## Install

Install it like any other extension in the backend.