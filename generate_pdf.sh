#!/bin/bash
tmp=$1
pdf=$2
/usr/bin/nohup /usr/bin/pandoc $tmp -f markdown -t latex --pdf-engine=xelatex -o $pdf 1>/dev/null 2>&1 &