# CLI to create Files for CallingAllPapers

This CLI retrieves external resources and creates the files needed by callingallpapers.com

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/joindin/callingallpapers-cli/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/joindin/callingallpapers-cli/?branch=master)
[![Build](https://github.com/joindin/callingallpapers-cli/actions/workflows/workflow.yml/badge.svg)](https://github.com/joindin/callingallpapers-cli/actions/workflows/workflow.yml)
[![Coverage Status](https://coveralls.io/repos/github/joindin/callingallpapers-cli/badge.svg?branch=master)](https://coveralls.io/github/joindin/callingallpapers-cli?branch=master)

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

