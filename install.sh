#!/bin/bash

TARGET=/usr/local/bin/publish
PHARDIR=/usr/share/antpublish
PHARCHIVE=ant-publish.phar
LINK=$TARGET

PHARPATH=$PHARDIR/$PHARCHIVE
check_root() {
if [ "$EUID" -ne 0 ]
  then echo "Please run as root"
  exit 1
fi    
}

install_files() {

    echo ""
    echo "Installing files..."

    if [ ! -f $PHARCHIVE ]; then
        echo "$PHARCHIVE is not here. Re-building phar..."
        php buildphar.php
    fi

    if [ ! -d  $PHARDIR ]; then
        mkdir $PHARDIR
    fi

    chmod +x $PHARCHIVE

    
    mv $PHARCHIVE $PHARPATH

    [ -f $PHARPATH ] && echo "$PHARCHIVE installed to: $PHARPATH" || echo "ERROR: phar archive not found in $PHARCHIVE"

    if [ ! -L $TARGET ]; then
        ln -s $PHARPATH $TARGET
    fi

    [ -L $TARGET ] && echo "Symlink installed $LINK -> $PHARPATH" || echo "ERROR: SYMLINK NOT INSTALLED!"
    echo "done"
    echo ""
}

remove_files() {
    
    [ ! -L $TARGET ] && unlink $TARGET
    echo "Unlnked: $TARGET"
    rm -v $PHARPATH
    echo "Removed $PHARPATH"
    echo "Uninstall complete."
}

show_help() {
    cat <<'EOF'

SUMMARY:

Installs the PHP-Ant app signer.

SYNTAX:

install.sh [ install | remove ]

WHERE:
  install  Installs the scripts into /usr/local/bin via a symlink to this
           directory. If you move these files, it will break the link. So,
           You should probably extract all these files to /usr/src/ to keep
           them safe long term.

  remove   Removes the symlinks from /usr/local/bin, but leaves the source
           files in tact.

For support, or to open issues, create a Github issue.

EOF
exit 0
}

if [ $# -ne 1 ];
    then show_help
fi

case "$1" in
    install)
        check_root
        install_files
        ;;
    remove)
        check_root
        remove_files
        ;;
    help)
        show_help
        ;;
    *)
        show_help
        ;;
esac