Описание:
Для использования приложения необходимо зарегестрироваться. 
Реализован базовый функционал описанный в ТЗ.

Инструменты:
1. php
2. mysql
3. js, jquery

Структура:
1. /app - бизнес логика проекта. Хранятся контроллеры, Модели.
2. /config - хранятся файлы конфигов, файлы маршрутизации
3. /logs - папка с логами
4. /public - содержит файлы css, файлы js и точку входа в приложение - index.php
5. /resources - содержит файлы шаблонов страниц
6. /src - библиотека классов

База данных:
1. tasks - таблица задач

| Field      | Type          | Null | Key | Default | Extra          |
| ---------- | ------------- | ---- | --- | ------- | ---------------|
| id         | int(11)       | NO   | PRI | NULL    | auto_increment |
| name       | varchar(255)  | NO   |     | NULL    |                |
| user_id    | int(11)       | NO   | MUL | NULL    |                |
| status     | enum('1','0') | YES  |     | 1       |                |
| created_at | int(11)       | YES  |     | 0       |                |
| updated_at | int(11)       | YES  |     | 0       |                |


2. users - таблицп пользователей

| Field          | Type         | Null | Key | Default | Extra          |
|----------------|--------------|------|-----|---------|----------------|
| id             | int(11)      | NO   | PRI | NULL    | auto_increment |
| name           | varchar(255) | NO   | MUL | NULL    |                |
| email          | varchar(255) | YES  |     | NULL    |                |
| password       | varchar(255) | YES  |     | NULL    |                |
| remember_token | varchar(255) | YES  |     | NULL    |                |
| created_at     | int(11)      | YES  |     | 0       |                |
| updated_at     | int(11)      | YES  |     | 0       |                |


