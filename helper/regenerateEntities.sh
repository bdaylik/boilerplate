#!/bin/bash

rm ../model/entities/* ../model/proxies/*
php doctrine-cli.php orm:generate-entities ../model/
php doctrine-cli.php orm:generate-proxies
php doctrine-cli.php  orm:schema-tool:drop --force
php doctrine-cli.php  orm:schema-tool:create
#mysql -uroot -proot -e 'drop database hed; create database hed;'
php ../datafiller.php
