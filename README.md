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
2. [Node.js](https://nodejs.org/) 20+ (and npm 10+)
3. [PHP](https://www.php.net/) 8.2+ and [Composer](https://getcomposer.org/) 2

<details>

<summary>Node.js</summary>

If you use a Node.js version manager like [nvm](https://github.com/nvm-sh/nvm) or [fnm](https://github.com/Schniz/fnm), run the respective command to install and use the required version:

```bash
nvm install && nvm use
```

or

```bash
fnm install && fnm use
```

</details>

### Commands

```
$ git clone https://github.com/FogosPT/fogospt.git
$ cd fogospt
$ docker-compose up
$ cd fogospt
$ composer install
$ npm install
$ cp .env.example .env
$ php artisan key:generate
$ php artisan serve    # Terminal 1 - Laravel server
$ npm run dev          # Terminal 2 - Vite dev server
```

#### PHP and Composer

You can use a containerized version of PHP and Composer to run the `composer install` command above:

```bash
docker run --rm -it -v "$(pwd):/app" composer:latest install
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
