# Fogos.pt

```
///////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////,*/////////////////////////////////
////////////////////////////////////////////*  *///////////////////////////////
////////////////////////////////////////////*   ,//////////////////////////////
////////////////////////////////////////////*    ,/////////////////////////////
////////////////////////////////////////////.     *////////////////////////////
///////////////////////////////////////////.      .////////////////////////////
/////////////////////////////////////////*         ////////////////////////////
///////////////////////////////////////*           ////////////////////////////
////////////////////////////////////*.             ////////////////////////////
/////////////////////////////////,                .////////////////////////////
//////////////////////////////*                   *//////*/////////////////////
////////////////////////////*                    ./////. */////////////////////
//////////////////////////*.                     *///,   */////////////////////
/////////////////////////*                      .///     */////////////////////
////////////////////////*                       *//.     ./////////////////////
///////////////////////*                        */*       ,////////////////////
///////////////////////.                        */*        *///////////////////
//////////////////////*                         ,/*         ,//////////////////
///////////////////* **                          */.         ./////////////////
/////////////////*   /*                           ,*           ////////////////
////////////////.   ./*                                         *//////////////
//////////////*      //*                                         */////////////
/////////////,        ,,.                                         *////////////
////////////,                                                      *///////////
///////////,                                                       .///////////
//////////*                                                         *//////////
//////////,                           ,.                            ,//////////
//////////,                          */*                            .//////////
//////////*                         *////.                          .//////////
///////////                        ,//////*                         *//////////
///////////*                       //////////,                      ///////////
////////////*       ,             ,////////////*                   *///////////
//////////////,     ,*            *//////////////.                *////////////
////////////////.    */,          ////////////////,             .//////////////
//////////////////*   ,////*,     /////////////////.          .*///////////////
/////////////////////*  *//////.  *////////////////*        ,//////////////////
////////////////////////*,*//////,./////////////////.    ,*////////////////////
///////////////////////////////////*////////////////,,*////////////////////////
```

## Run & Go

### Requirements

1. [Docker Desktop](https://www.docker.com/products/docker-desktop/)
2. [Node.js](https://nodejs.org/) 14 (and npm 6)
3. [Python](https://www.python.org/) 2.7 (to install the necessary [node-gyp](https://github.com/nodejs/node-gyp/tree/v3.8.0) version according to the [`package-lock.json`](fogospt/package-lock.json) file)
4. [PHP](https://www.php.net/) 7 (at least 7.2.5) and [Composer](https://getcomposer.org/) 1

#### Node.js

If you use a Node.js version manager like [nvm](https://github.com/nvm-sh/nvm) or [fnm](https://github.com/Schniz/fnm), run the respective command to install and use the required version:

```bash
nvm use 14
```

or

```bash
fnm use 14
```

#### Python

If you use a Python version manager like [pyenv](https://github.com/pyenv/pyenv), run the respective commands to install and use the required version:

```bash
pyenv install 2.7
```

and

```bash
pyenv shell 2.7
```

### Commands

```
$ git clone https://github.com/FogosPT/fogospt.git
$ cd fogospt
$ docker-compose up
$ cd fogospt
$ composer install
$ npm install
$ npm run dev
```

#### PHP and Composer

You can use a containerized version of PHP and Composer to run the `composer install` command above. The latest [official Docker image](https://hub.docker.com/_/composer) tag providing PHP 7 and Composer 1 is `1.10.19`.

To install the PHP dependencies from a Docker container, run the following command instead:

```bash
docker run --rm -it -v "$(pwd):/app" composer:1.10.19 install
```

## Slack

https://communityinviter.com/apps/fogospt/fogos-pt

## License

Copyright 2018

Licensed under the Apache License, Version 2.0 (the "License");
you may not use this file except in compliance with the License.
You may obtain a copy of the License at

 http://www.apache.org/licenses/LICENSE-2.0

Unless required by applicable law or agreed to in writing, software
distributed under the License is distributed on an "AS IS" BASIS,
WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
See the License for the specific language governing permissions and
limitations under the License.
