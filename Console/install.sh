#!/bin/bash

# Install CakePHP/Oven: curl http://dontkry.com/oven/install.sh | sh

# TODO
#   Ability to upgrade CakePHP or Oven

# A word about this shell script:
#
# I'm partially copied and totally inspired by npmjs.org
#
# It must work everywhere, including on systems that lack
# a /bin/bash, map 'sh' to ksh, ksh97, bash, ash, or zsh,
# and potentially have either a posix shell or bourne
# shell living at /bin/sh.
#
# See this helpful document on writing portable shell scripts:
# http://www.gnu.org/s/hello/manual/autoconf/Portable-Shell.html
#
# The only shell it won't ever work on is cmd.exe.

if [ "x$0" = "xsh" ]; then
  # run as curl | sh
  # on some systems, you can just do cat>oven-install.sh
  # which is a bit cuter. But on others, &1 is already closed,
  # so catting to another script file won't do anything.
  curl -s http://dontkry.com/oven/install.sh > oven-install-$$.sh
  sh oven-install-$$.sh
  ret=$?
  rm oven-install-$$.sh
  exit $ret
fi

# Check for git
git=`which git 2>&1`
ret=$?
if [ $ret -eq 0 ] && [ -x "$git" ]; then
  (exit 0)
else
  echo "Oven cannot be installed without git." >&2
  echo "Install git first, and then try again." >&2
  echo "" >&2
  exit $ret
fi

# Get install folder
if [ -z $1 ]; then
  pwd=`pwd`
else
  pwd=./$1
  # Folder exists?
  if [ ! -d "$pwd" ]; then
    mkdir $pwd 2>&1
  fi
fi

# install folder empty?
if [ "$(ls -A $pwd 2>&1 | grep -v oven-install-*)" ]; then
  echo "There appears to be files in [$pwd]." >&2
  echo "Please install Oven into an empty folder." >&2
  echo "" >&2
  exit 1
fi

# Check for CakePHP 2
cake=`cat $pwd/lib/Cake/VERSION.txt 2>&1`
ret=$?
if [ $ret != 0 ]; then
  echo "Installing CakePHP..."
  git clone git://github.com/cakephp/cakephp.git $pwd/oventmp
  mv $pwd/oventmp/* $pwd 2>&1 && rm -rf oventmp 2>&1
fi

# Install Oven
cake=`cat $pwd/lib/Cake/VERSION.txt 2>&1`
ret=$?
if [ $ret -eq 0 ]; then
  echo "Installing Oven..."
  git clone git://github.com/shama/oven.git $pwd/app/Plugin/Oven

  # TODO: Check for recipe

  # Init Oven
  echo -e "\nCakePlugin::load('Oven');" >> $pwd/app/Config/bootstrap.php
  cd $pwd/app
  ./Console/cake oven.init

  echo ""
  echo "Ding! Oven is ready."
  echo "cd into $pwd/app and run ./Console/cake oven.bake --help"
else
 echo "Something went wrong. Could not detect nor install CakePHP." >&2
fi

echo "" >&2