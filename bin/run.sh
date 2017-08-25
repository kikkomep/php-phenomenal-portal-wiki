#!/usr/bin/env bash
path="/var/www/html/php-phenomenal-portal-wiki"
markdownFolder="$path/wiki-markdown"
htmlFolder="$path/wiki-html"
gitList="$path/conf/gitList.txt"
extension=".html"

mkdir -p $markdownFolder
mkdir -p $htmlFolder

cd $markdownFolder && rm -rf *

echo $gitList

while IFS= read line
do
    git clone "$line"
done <"$gitList"

# For running inside cron, for markdown2
PATH=/usr/local/bin/:$PATH

for dir in `ls ./`;
do
    mkdir -p "$htmlFolder/$dir"
    for file in `ls ./$dir`;
    do
	    if [[ -f "$dir/$file" ]]; then
			filename="${file%.*}"
			markdown2 --extras fenced-code-blocks "$dir/$file" > "$htmlFolder/$dir/$filename"
			cp "$htmlFolder/$dir/$filename" "$htmlFolder/$dir/$filename$extension"
	    fi
	    cp -r "$dir/$file" "$htmlFolder/$dir/$file"
    done
done
