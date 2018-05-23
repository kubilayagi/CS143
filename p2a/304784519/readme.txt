Kubilay Agi 304784519
Lawrence Chen 704767754

In this part of project 2, we used regex expressions to parse through the comments from the given json files
We implemented the bz2 decompression, so our program takes in both json files and bz2 files. However, the bz2
files do have an initial cost to decompress the file, so please be patient with us! In the future, we may try
to implement it so that we read line-by-line, which eliminates the upfront cost of decompression.

There are a few edge cases that our program does not handle currently. For example, it does not handle the
case where there are ellipsis in the text, nor does it handle quotes or parentheses. That being said, it matches
on 91% of the cases to the provided sample-output.txt file, so we felt that this was good enough.

We only handle three types of URLs for now, the three being of the form:
http[s]://google.com
www.google.com
/r/politics/etc

Later on for part 2B, we will need to find a way to remove URLs of the form "r/politics/etc", but for now, we
did not because there are conflicts in the case where the comment contains a slash between two words to represent
an "either-or" expression (e.g. tariffs/embargo)
