#!/bin/bash

# Check if directory is provided
if [ -z "$1" ]; then
    echo "Usage: $0 <directory_with_zips>"
    exit 1
fi

# Check if directory exists
if [ ! -d "$1" ]; then
    echo "Error: '$1' is not a valid directory."
    exit 1
fi

# Find all .zip files (including subdirectories if needed)
find "$1" -type f -name "*.zip" | while read -r zipfile; do
    echo "Extracting: $zipfile"
    # Extract into the .zip's own directory (no subfolder)
    unzip -q -o -d "$(dirname "$zipfile")" "$zipfile"
done

echo "All ZIP files extracted in place."
