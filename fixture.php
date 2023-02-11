<?php

$connect = include 'sqlite.php';

$connect->exec('CREATE TABLE users (
    uuid VARCHAR(40) NOT NULL PRIMARY KEY UNIQUE,
    username VARCHAR(30) NOT NULL UNIQUE,
    firstName VARCHAR(30) NOT NULL,
    lastName VARCHAR(30) NOT NULL,
    password VARCHAR(30) NOT NULL
)');

$connect->exec('CREATE TABLE posts (
    uuid VARCHAR(40) NOT NULL PRIMARY KEY UNIQUE,
    uuidAuthor VARCHAR(40) NOT NULL,
    header TEXT NOT NULL,
    text TEXT NOT NULL
)');

$connect->exec('CREATE TABLE comments (
    uuid VARCHAR(40) NOT NULL PRIMARY KEY UNIQUE,
    uuidAuthor VARCHAR(40) NOT NULL,
    uuidPost VARCHAR(40) NOT NULL,
    text TEXT NOT NULL
)');

$connect->exec('CREATE TABLE likes (
    uuid VARCHAR(40) NOT NULL PRIMARY KEY,
    uuidPost VARCHAR(40) NOT NULL,
    uuidUser VARCHAR(40) NOT NULL UNIQUE
)');
