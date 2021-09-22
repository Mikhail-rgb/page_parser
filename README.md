# page_parser

Данный проект является решением тестовго задания:



Необходимо спарсить страницу: https://etp.eltox.ru/registry/procedure 

С установленным фильтром: Тип процедуры – Запрос цен (котировок)

И получить:
1.1.	Номер процедуры, вида: 2187

1.2.	ООС номер процедуры, вида: 32110439421

1.3.	Ссылку на страницу процедуры, пример: https://etp.eltox.ru/procedure/read/2187

1.4.	Со страницы процедуры получить:

1.4.1.	Email (поле Почта), например: goszakaz@tppkomi.ru

1.4.2.	Документацию к этому аукциону, имя файла и ссылки на нее (вкладка "Документы", в карточке процедуры), пример:

Документация_на энергосбережение и повышение энергетической эффективности.docx - https://storage.eltox.ru/bcacd638-36fd-4e03-a7fc-e92ff963387c/60ddb36c8178b_%D0%94%D0%BE%D0%BA%D1%83%D0%BC%D0%B5%D0%BD%D1%82%D0%B0%D1%86%D0%B8%D1%8F_%D0%BD%D0%B0%20%D1%8D%D0%BD%D0%B5%D1%80%D0%B3%D0%BE%D1%81%D0%B1%D0%B5%D1%80%D0%B5%D0%B6%D0%B5%D0%BD%D0%B8%D0%B5%20%D0%B8%20%D0%BF%D0%BE%D0%B2%D1%8B%D1%88%D0%B5%D0%BD%D0%B8%D0%B5%20%D1%8D%D0%BD%D0%B5%D1%80%D0%B3%D0%B5%D1%82%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%BE%D0%B9%20%D1%8D%D1%84%D1%84%D0%B5%D0%BA%D1%82%D0%B8%D0%B2%D0%BD%D0%BE%D1%81%D1%82%D0%B8.docx

Результат вывести на экран, и записать в базу.

___
## Инструментарий

Для создания программы использовался следующий инструментарий:
- фреймворк Symfony 5.3.7;
- язык программирования PHP 7.4.3;
- СУБД MySQL.

___
## Запуск программы

Для того, чтобы запустить проект, нужно в командной строке перейти в папку проекта и выполнить команду "php bin/console ParserCommand".

___
## Результаты

В ходе выполнения тестового задания со страницы удалось получить всё, что требовалось, кроме документации к аукциону (пункт 1.4.2. задания).

Результаты выводятся на экран, и сохраняются в базу.

Пример удачной работы программы:

```json
Procedure number: 2206
OOS procedure number: 32110647924
Procedure page link: https://etp.eltox.ru/procedure/read/2206
Email: madou5@mail.ru
================

Procedure number: 2205
OOS procedure number: 32110636579
Procedure page link: https://etp.eltox.ru/procedure/read/2205
Email: madou5@mail.ru
================

Procedure number: 2204
OOS procedure number: 32110635662
Procedure page link: https://etp.eltox.ru/procedure/read/2204
Email: ooo.chts@mail.ru
================

Procedure number: 2203
OOS procedure number: 32110635667
Procedure page link: https://etp.eltox.ru/procedure/read/2203
Email: ooo.chts@mail.ru
================

Procedure number: 2202
OOS procedure number: 32110635145
Procedure page link: https://etp.eltox.ru/procedure/read/2202
Email: goszakaz@tppkomi.ru
================

Procedure number: 2201
OOS procedure number: 32110632539
Procedure page link: https://etp.eltox.ru/procedure/read/2201
Email: madou5@mail.ru
================

Procedure number: 2199
OOS procedure number: 32110626686
Procedure page link: https://etp.eltox.ru/procedure/read/2199
Email: madou26@mail.ru
================

Procedure number: 2198
OOS procedure number: 32110626569
Procedure page link: https://etp.eltox.ru/procedure/read/2198
Email: madou26@mail.ru
================

Procedure number: 2197
OOS procedure number: 32110578945
Procedure page link: https://etp.eltox.ru/procedure/read/2197
Email: goszakaz@tppkomi.ru
================

Procedure number: 2195
OOS procedure number: 32110563544
Procedure page link: https://etp.eltox.ru/procedure/read/2195
Email: ds.alenka@yandex.ru
================
```
