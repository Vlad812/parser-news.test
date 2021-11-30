Стек :
 - PHP 7.4
 - Symfony 5.1.3 
 - MySql 5.7
 
Для работы с DOM - использовалась библиотека DiDom. 
Для работы с HTTP запросами - Symfony Http Client

SQL - migration/init.sql

Расширть функционал для дополнительных новостных ресурсов можно путём добавления:

 1) Записи в справочник NewsResourceReference  
 2) Контроллера (как src/Controller/RbcController)
 3) Сервиса, который содержит конкретную логику парсера (как src\Services\Rbc\Parser)    





