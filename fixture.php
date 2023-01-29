<?php

$connect = include 'sqlite.php';

$connect->exec('CREATE TABLE users (
    uuid VARCHAR(40) NOT NULL PRIMARY KEY UNIQUE,
    username VARCHAR(100) NOT NULL UNIQUE,
    firstName VARCHAR(100),
    lastName VARCHAR(100),
    password VARCHAR(100) NOT NULL
)');

//$pdo->exec('CREATE TABLE tasks (
//  id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
//  description VARCHAR(300),
//  priority INTEGER NOT NULL,
//  isDone INTEGER NOT NULL,
//  dateCreated VARCHAR(100) NOT NULL,
//  dateUpdated VARCHAR(100) NOT NULL,
//  dateDone VARCHAR(100),
//  userId INTEGER NOT NULL
//)');

//Пример запроса на добавление задачи
/*
INSERT INTO tasks (description, priority, isDone, dateCreated, dateUpdated, dateDone, userId)
VALUES ('Поиграть с Василисой', 3, 0, '17-01-2023', '17-01-2023', null, 1);
//------------------
INSERT INTO tasks (description, priority, isDone, dateCreated, dateUpdated, dateDone, userId)
VALUES ('Составить дефектную', 2, 0, '17-01-2023', '17-01-2023', null, 1);
*/

//Пример на изменение отметки о выполнении
/*
UPDATE tasks
SET isDone   = 1,
    dateDone = '17-01-2023'
WHERE id = 4;
 */

//Пример запроса на выбор данных по двум критериям
/*
SELECT *
FROM tasks
WHERE id=8 AND userId=1;

SELECT *
FROM tasks
WHERE dateDone IS NOT NULL AND userId=1;    //поле dateDone неравно null

SELECT *
FROM tasks
WHERE dateDone IS NULL AND userId=1;    //поле dateDone равно null
 */

//Пример запроса на удаление
/*
DELETE
FROM tasks
WHERE id = 4;
 */