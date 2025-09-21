#!/bin/bash

NAME=user-repo
VERSION="$1"

if [ "$VERSION" == "" ]
then
	echo "Please specifiy version: $0 v0.0.0"
fi

echo ""
echo "*** Creating new release for version $VERSION ***"
echo ""

git tag -a -m "" "${VERSION}"
git archive --prefix="$NAME/" -o "${NAME}.zip" "${VERSION}:plugin/"
git push origin "${VERSION}"

gh release create "${VERSION}" "${NAME}.zip" --title "${VERSION}" -F notes.md
gh release list
