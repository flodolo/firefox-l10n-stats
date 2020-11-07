#! /usr/bin/env bash

script_folder=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )

# Update repository
cd $script_folder/../..
git pull

DAY=$(date +"%Y%m%d")
# Remove previous archive.zip, create new one and commit it
rm -f app/data/archive.zip
zip -j app/data/archive.zip app/data/data.json
git add app/data/archive.zip
git commit -m "Update data archive ($DAY)"
git push
