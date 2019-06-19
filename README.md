[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/joindin/callingallpapers-cli/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/joindin/callingallpapers-cli/?branch=master)
[![Build Status](https://travis-ci.org/joindin/callingallpapers-cli.svg?branch=master)](https://travis-ci.org/joindin/callingallpapers-cli)
# CLI to create Files for CallingAllPapers

This CLI retrieves external resources and creates the files needed by callingallpapers.com

## Installation:

    composer create-project callingallpapers/cli [path]

alternate version:

    git clone https://github.com/joindin/callingallpapers_cli.git


## Usage

    # Install Composer dependencies (if not using `composer create-project` command
    composer install
    
    # Copy and customize the .ini file:
    cp config/callingallpapers.ini.dist config/callingallpapers.ini
    
    # List valid commands
    ./bin/callinallpapers list

