Feed RSS + Atom Syndication package for TYPO3 Neos
==================================================

A feed syndication package dedicated to TYPO3 Neos. 

With this package your can create a custom document
 ``Ttree.Aggregator:Feed`` type in your content repository. In this document, you can configure the feed URI, crawling
 frequency. A cron task, based on [Ttree.Scheduler](https://github.com/ttreeagency/Scheduler), is automatically created
 when you published the node. During syndication, the system will create automaticaly one sub document per item found
 in the RSS or ATOM feed.

**Warning**: This package is in is early age, so things can change ;)
	
TODO
----

Feel free to open issue if you need a specific feature and better send a pull request. Here are some idea for future 
improvements:

* More robust feed parsing, need more feedback to know if the current parser work correctly
* Add support for GUID in feed, currently we create a new node based on the document title, this title can change, so we
can have multiple document for the same item.
* Add proper TypoScript configuration to display to feed content
* Improve rendering of the TypoScript syndicated document rendering
	
Acknowledgments
---------------

Development sponsored by [ttree ltd - neos solution provider](http://ttree.ch).

License
-------

Licensed under GPLv2+, see [LICENSE](LICENSE)