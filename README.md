# cabinetry.jacobsanford.com
This is an easy-development instance for the [cabinetry module on d.o.](https://www.drupal.org/project/cabinetry).

![Cabinetry Module Screenshot](https://www.drupal.org/files/project-images/Screen%2BShot%2B2017-07-04%2Bat%2B3.25.02%2BPM.png "Cabinetry Module Screenshot")

## Summary
Cabinetry was created with the goal of fast prototype-to-build designs for various forms of cabinetry. After an input of project specs, the entities display a summary of necessary materials, and provide a cut list and sheet layouts.

The base module itself cabinetry_core provides a framework for extending into specific areas, and does little on its own.

Please note this project is still in development, and has been presented to garner interest in co-development or critique.

## Layouts
The sheet layouts are calculated with a modified 2D bin packing algorithm, taken from [https://github.com/juj/RectangleBinPack](https://github.com/juj/RectangleBinPack) as an example. The base algorithm used is the **SHELF-FF**, with a modification that considers cabinet doors look best with a vertical grain orientation.

The above algorithm was chosen with the intent of producing sheet layouts that eased the burden of cutting the sheets with a track (circular) saw. A 'shelf' layout provides straight lines that are easy to break down quickly, while minimizing human error.

If you are planning to adapt this to set up a cut list (and toolpath) for a CNC machine, this changes the layout consideration significantly. Guillotine based algorithms [1](https://github.com/juj/RectangleBinPack/blob/master/GuillotineBinPack.cpp), [2](http://www.win.tue.nl/~nikhil/pubs/Bansal-packing.pdf) are significantly more efficient and should be considered, since there is limited human involvement. If you do adapt this, please let me know / contribute!

## Getting Started
### Requirements
The following packages are required to be globally installed on your development instance:

* [PHP7](https://php.org/) - Install instructions [are here for OSX](https://gist.github.com/JacobSanford/52ad35b83bcde5c113072d5591eb89bd).
* [Composer](https://getcomposer.org/)
* [docker](https://www.docker.com)/[docker-compose](https://docs.docker.com/compose/) - An installation HowTo for OSX and Linux [is located here, in section 2.](https://github.com/unb-libraries/docker-drupal/wiki/2.-Setting-Up-Prerequisites).
* [dockworker](https://gist.github.com/JacobSanford/1448fece856be371060d0f16ccb1b194) - Install the dockworker alias.

### 1. Initial Setup
#### A. Configure Local Development
In the ```env/drupal.env``` file, change the environment settings to match your local development environment.

```
DOCKER_ENDPOINT_IP=localhost
LOCAL_USER_GROUP=20
```

* ```DOCKER_ENDPOINT_IP``` - This is the IP of your docker daemon. In OSX, this is likely the [IP of your docker-machine](https://docs.docker.com/machine/reference/ip/). On Linux, this is probably localhost.
* ```LOCAL_USER_GROUP``` - The [group id](https://kb.iu.edu/d/adwf) of your local user. This is used to change permissions when deploying locally.

### 2. Deploy Instance
```
composer install --prefer-dist
```

Start it!
```
dockworker container:start-over
```

### 3. Other useful commands
Run ```dockworker``` to get a list of available commands.

## Repository Branches
* `dev` - Core development branch. Deployed to dev when pushed.
* `live` - Deployed to live when pushed.
