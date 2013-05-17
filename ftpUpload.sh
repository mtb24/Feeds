#!/bin/bash
/usr/bin/ftp -d posftp.smartetailing.com << ftpEOF
   cd google
   put "/Applications/MAMP/htdocs/feeds/upload/*.txt"
   quit
ftpEOF