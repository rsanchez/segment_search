# Segment Search #
Search URL segments for use in conditionals.

## Installation

* Copy the /system/expressionengine/third_party/segment_search/ folder to your /system/expressionengine/third_party/ folder

## Usage

Search ALL segments for the presence of the word "stuff".
	{if {exp:segment_search keyword="stuff"}}{/if}

Search ALL segments for the exact word "stuff".
	{if {exp:segment_search keyword="=stuff"}}{/if}

Search ALL segments for the presence of the word "stuff" or "puff".
	{if {exp:segment_search keyword="stuff|puff"}}{/if}

Search ALL segments for the absence of the words "stuff" and "puff".
	{if {exp:segment_search keyword="not stuff|puff"}}{/if}

Search segments 1-3 for the presence of the word "stuff".
	{if {exp:segment_search keyword="stuff" segments="1|2|3"}}{/if}

Search ALL segments except 1-3 for the presence of the word "stuff".
	{if {exp:segment_search keyword="stuff" segments="not 1|2|3"}}{/if}

Search segments 1-3 for a number using regex.
	{if {exp:segment_search keyword="/\d+/" segments="1|2|3" regex="yes"}}{/if}

Search the last segment for P and a number (a pagination page) using regex.
	{if {exp:segment_search keyword="/P\d+/" segments="last" regex="yes"}}{/if}