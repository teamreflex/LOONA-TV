# LOONA-TV
An index for LOONA TV episodes.

## Requirements
- PHP 7.4
- YouTube Data API key

## Setup
```shell script
$ git clone git@github.com:teamreflex/LOONA-TV.git
$ cd LOONA-TV
$ composer install
$ cp .env.example .env
$ php artisan key:generate
$ npm install
$ npm run dev
```

## Seeding Data
The application is built around importing [Blockberry Creative's own LOONA TV playlist](https://www.youtube.com/playlist?list=PLn2nfuATkZsQzaTPcar9B0vPVO2a59hf5).

You can import everything with an Artisan command:
```shell script
$ php artisan ltv:playlist PLn2nfuATkZsQzaTPcar9B0vPVO2a59hf5 -r
```
`-r|--reverse` is to reverse the playlist, so video #1 corresponds to LTV episode #1.

`-c|--create` is to create new Arc to add the given playlist into. It will prompt for the name, color and order. This is suitable for adding LOONA 1/3's New Zealand Story playlist.

`-a|--add` is to add each video in a playlist into separate arcs. This is suitable for the Prequel series.


Arcs are stored in `config/episodes.php`, with each arc denoting its episode number range. This should probably be periodically updated.
