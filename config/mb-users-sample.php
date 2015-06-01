<?php
if ( !defined('ABSPATH') ) exit();	// sanity check

//	Global list of virtual users.
//	The first blog on the list will be the default

//	You can put a domain, subdomain, or directory in the list, or 
//	a domain *with* directory.
//	Examples:
//		$vusers[] = 'dog';   // will key on both domain.com/dog and dog.domain.com
//		$vusers[] = 'cat';

//		$vusers[] = 'domain.com';        // NOTE: www.domain.com == domain.com
//		$vusers[] = 'sub.domain.com';    // NOTE: subdomains _must_ come after domain if both are present

//		$vusers[] = 'domain.com/dog';    // NOTE: No trailing slashes!
//		$vusers[] = 'dog.domain.com';

$vusers[] = '';


//	OPTIONAL: If using subdomain ("dog.example.com") addresses with 
//	one domain, set $mydomain to the domain name -- 
//	e.g. 'example.com' -- and then you only need set the subdomain 
//	('dog') in $vusers[];

//	You may only set one $mydomain.

$mydomain = '';

?>