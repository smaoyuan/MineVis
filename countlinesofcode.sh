#!/bin/bash

# this could use some clean up...

#count lines of codes for the minevis project
#tries to exclude symfony only folders
perl /usr/local/bin/cloc.pl --exclude-dir='data/sql',plugins,dump,log,'lib/vendor','web/sf',test  /Users/thepunksnowman/Dropbox/Work/WebDev/minevis
