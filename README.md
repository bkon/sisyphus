Sisyphus::C14N
==============

This  library  provides a  pure-PHP  implementation  of the  following
standards:
* Canonical XML Version   1.0   (http://www.w3.org/TR/xml-c14n)
* Exclusive XML Canonicalization Version 1.0
(http://www.w3.org/TR/2002/REC-xml-exc-c14n-20020718/)

Use cases
---------

Consider using  this library if  you need XML canonicalization  on the
legacy  PHP versions  (PHP  5.1.x). If  you work  with  PHP 5.2+,  you
already have libxml-based  canonicalization functionality available as
DOMNode::C14n().

PHP  5.1 as  a target  platform  means we  don't have  access to  nice
features, like namespaces, lambdas or composer. Don't blame be if this
code looks outdated to you. :)

Note  that you  can  easily migrate  from  pure-PHP implementation  to
Lixml-based  one  when  you  upgrade  your  infrastructure  by  simply
replacing Sisyphus_C14n_Legacy with Sisyphus_C14n_Libxml.

Dependencies
------------

Runtime: PHP 5.1

Development:  ant,  phpunit,  phpmd,  phpcs,  hhvm  and  hhvm-wrapper,
phpcpd, phpdoc.

Basic usage
-----------

```
$service = new Sisyphus_C14n_Legacy();

$service
    ->withComments(false)
    ->exclusive(true)
    ->query('//n1:element')
    ->namespaces(array('n1' => 'http://n1.example.com'))
    ->inclusiveNamespaces(array('n1'));

$outputString = $service->canonicalize($xmlDocument)
```


[![Bitdeli Badge](https://d2weczhvl823v0.cloudfront.net/bkon/sisyphus/trend.png)](https://bitdeli.com/free "Bitdeli Badge")

