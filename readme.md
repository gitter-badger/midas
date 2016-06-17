# Midas

[![Join the chat at https://gitter.im/poiuty/midas](https://badges.gitter.im/poiuty/midas.svg)](https://gitter.im/poiuty/midas?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

Существует потребность в обмене. Совершать p2p обмен - может быть небезопасно.
Биржа выступает в роли гаранта, который позволяет совершить безопасный обмен между участниками.
С каждой операции на бирже мы будем планируем получать 0.25% ~ 0.1%

Примеры бирж
https://btc-e.com/
https://www.cryptsy.com/
https://poloniex.com/exchange#btc_eth
https://gatecoin.com/marketDepth

Обьем торгов на паре BTC
http://coinmarketcap.com/currencies/bitcoin/#markets
В среднем обьем за день 20kk$
Размер рынка небольшой. Имеет большой потенциал роста.

По форкам нам интересны макисимально ликвидные пары: Litecoin, DASH, Ethereum
(список форков можно посмотреть на этой странице http://coinmarketcap.com/currencies/views/all/)

Ключевые стадии проекта.
=> написание биржи, тестирование, запуска без фиата (торги по парам DASH/ BTC, LTC/ BTC, ETH/ BTC)
=> реклама, захват доли рынка, увеличение обьема торгов
=> поиск решения для ввода/ вывода фиата, открытие пар BTC/ USD, LTC/ USD, ETH/ USD

TODO [основная часть]
=> регистрация
=> авторизация
=> восстановление/ смена пароля
=> возможность совершения сделать (купить/ продать)
=> вывод графика
=> прием/ вывод btc, ltc, dash, eth (например https://github.com/poiuty/dashpay.org.ru/blob/master/private/cron/pay.php)
=> график торгов (можно использовать http://www.highcharts.com/)
=> дизайн сайта (можно применить http://getbootstrap.com/)
=> api (получение информации*, совершение действий**)
* => например, получение инфы о сделках на паре, обьеме торгов
** => например, возможность купить/ продать/ выставить сделку.
https://www.cryptsy.com/pages/api (тут есть пример api биржи)

Предлагаю делать сайт на php. Под базу взять MySQL.
В данном случае мы можем легко масштабировать наш сайт.
Например nginx [proxy, web server] => php1, php2, php3 => mysql(slave1) + mysql(slave2) + ... + mysql(master)

Структура сайта.
/private => подключаемые файлы, cron, настройки
/public => скрипты обработчики, на которые мы отправляем запросы (через js например)
/pages => страницы сайта (например http://test.dev/pages/settings.php)
index.php => главня страница

Обработку страницы предлагаю сделать через функции. Например
<php> include(...) => function(balance){} </php>
<html> balance: =function(balance)</html>

То есть вся логика происходит до html кода.
Далее когда php отработал логику - он формирует html и отдает страницу пользователю.
Делаем максимально простой код. Без использования php фреймворков типо yui, kohana.

Клиент делает запрос на наш веб сервер, nginx в зависимости от приоритета => отправляет запрос на обработку php.
http://nginx.org/en/docs/http/load_balancing.html
На уровне php мы можем использовать несколько mysql серверов. Часть из них (slave) только под select запросы, и один (master) под insert/ update.
Таким образом когда нам не хватает ресурсов мы добавляем новый сервер => N1 + N2 + ... +NX

Для защиты от ddos - мы используем сервисы фильтрации http трафика, например https://www.cloudflare.com/plans/
А так же берем серверы в тех ДЦ, которые фильтруют ddos. Например в OVH => https://www.ovh.ie/dedicated_servers/enterprise/
