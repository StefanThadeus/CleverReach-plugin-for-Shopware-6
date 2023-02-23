#!/bin/sh

# Author : Stefan Radenkovic
# Copyright (c) Stefan Radenkovic

# define constant for color of the output
GREEN='\033[1;32m'

# move directory reference to parent directory
cd ..

# get version key and value from "composer.json", isolate the value, remove quotes, store in "version" variable
version=$(grep -o '"version": *"[^"]*"' composer.json | grep -o '"[^"]*"$' | tr -d '"')

# build result destination path string
destination_path="./PluginInstallation/$version"

# create destination directory and any intermediate directories that might not exist
mkdir -p $destination_path

# create temporary folder tp store necessary files
mkdir LogeecomCleverReachPlugin

# copy necessary files into temporary folder
cp composer.json LogeecomCleverReachPlugin
cp -r src LogeecomCleverReachPlugin

# make zip in destination directory
zip $destination_path/CleverReach.zip -r LogeecomCleverReachPlugin

# remove temporary folder
rm -r LogeecomCleverReachPlugin

echo "${GREEN}Plugin deployment zip package successfully created."
