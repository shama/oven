#!/bin/sh

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
  pwd=$1
fi

# install folder empty?
if [ "$(ls -A $pwd)" ]; then
  echo "There appears to be files in [$pwd]." >&2
  echo "Please install Oven into an empty folder." >&2
  echo "" >&2
  exit 1
fi

# Check for CakePHP 2
cake=`cat lib/Cake/VERSION.txt 2>&1`
ret=$?
if [ $ret -eq 0 ]; then
  (exit 0)
else
  echo "Installing CakePHP 2..." >&2
  git clone git://github.com/cakephp/cakephp.git $pwd
fi

# Install Oven
echo "Installing Oven..." >&2
git clone git://github.com/shama/oven.git $pwd/app/Plugin/Oven

# TODO: Check for recipe
# TODO: Enable plugin
# TODO: Run oven.init with recipe

echo "" >&2
echo "Ding! Oven is ready." >&2
echo "cd into $pwd/app and run ./Console/cake Oven.bake --help" >&2
echo "" >&2