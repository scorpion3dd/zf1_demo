![Logo](http://framework.zend.com/images/logos/ZendFramework-logo.png)

> ## Simple demo web-project with REST Full API 
>
> This project is no longer maintained.
>
> At this time, the repository has been archived, and is read-only.
### (c) Denis Puzik <scorpion3dd@gmail.com>

---

The project is written in Zend Framework 1.12 Release. 


RELEASE INFORMATION
===================

Zend Framework 1.12 Release.

SYSTEM REQUIREMENTS
===================

Zend Framework requires PHP 5.2.11 or later. Please see our reference
guide for more detailed system requirements:

http://framework.zend.com/manual/en/requirements.html

1. Web Servers (example Apache)
2. Apache Lucene
3. Memcache
4. MySql
5. PHP

INSTALLATION
============

1. Create DB zf1.demo
~~~~~~
CREATE DATABASE zf1.demo
CHARACTER SET utf8mb4
COLLATE utf8mb4_0900_ai_ci;
~~~~~~

2. Create tables in the zf1.demo database by executing the SQL script:

~~~~~~
CREATE TABLE country (
  CountryID int NOT NULL AUTO_INCREMENT,
  CountryName varchar(255) NOT NULL,
  PRIMARY KEY (CountryID)
)
ENGINE = INNODB,
AUTO_INCREMENT = 19,
AVG_ROW_LENGTH = 1365,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
~~~~~~
~~~~~~
CREATE TABLE grade (
  GradeID int NOT NULL AUTO_INCREMENT,
  GradeName varchar(255) NOT NULL,
  PRIMARY KEY (GradeID)
)
ENGINE = INNODB,
AUTO_INCREMENT = 6,
AVG_ROW_LENGTH = 3276,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
~~~~~~
~~~~~~
CREATE TABLE item (
  RecordID int NOT NULL AUTO_INCREMENT,
  RecordDate date NOT NULL,
  SellerName varchar(255) NOT NULL,
  SellerEmail varchar(255) NOT NULL,
  SellerTel varchar(50) DEFAULT NULL,
  SellerAddress text DEFAULT NULL,
  Title varchar(255) NOT NULL,
  Year int NOT NULL,
  CountryID int NOT NULL,
  Denomination float NOT NULL,
  TypeID int NOT NULL,
  GradeID int NOT NULL,
  SalePriceMin float NOT NULL,
  SalePriceMax float NOT NULL,
  Description text NOT NULL,
  DisplayStatus tinyint(1) NOT NULL,
  DisplayUntil date DEFAULT NULL,
  PRIMARY KEY (RecordID)
)
ENGINE = INNODB,
AUTO_INCREMENT = 11398,
AVG_ROW_LENGTH = 541,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
~~~~~~
~~~~~~
CREATE TABLE log (
  RecordID int NOT NULL AUTO_INCREMENT,
  LogMessage text NOT NULL,
  LogLevel varchar(30) NOT NULL,
  LogTime varchar(30) NOT NULL,
  Stack text DEFAULT NULL,
  Request text DEFAULT NULL,
  PRIMARY KEY (RecordID)
)
ENGINE = INNODB,
AUTO_INCREMENT = 111,
AVG_ROW_LENGTH = 2520,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
~~~~~~
~~~~~~
CREATE TABLE type (
  TypeID int NOT NULL AUTO_INCREMENT,
  TypeName varchar(255) NOT NULL,
  PRIMARY KEY (TypeID)
)
ENGINE = INNODB,
AUTO_INCREMENT = 6,
AVG_ROW_LENGTH = 3276,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
~~~~~~
~~~~~~
CREATE TABLE user (
  RecordID int NOT NULL AUTO_INCREMENT,
  Username varchar(10) NOT NULL,
  Password text NOT NULL,
  PRIMARY KEY (RecordID)
)
ENGINE = INNODB,
AUTO_INCREMENT = 4,
AVG_ROW_LENGTH = 16384,
CHARACTER SET utf8,
COLLATE utf8_general_ci;
~~~~~~

3. Clone a project from the repository
~~~~~~
git clone https://github.com/scorpion3dd/Simple_REST_Full_API_airlines.git ./api.simple
~~~~~~
4. In the file /application/configs/application.ini, if necessary, change the parameters

5. Create virtual host in you web server

6. Reload Web Server (example Apache)
~~~~~~
sudo systemctl restart apache2
~~~~~~
7. Give write permissions to directories:
- /data/logs
- /data/indexes
- /public/captcha
- /public/uploads
~~~~~~
sudo chmod -R 777 logs
sudo chmod -R 777 indexes
sudo chmod -R 777 captcha
sudo chmod -R 777 uploads
~~~~~~

DESCRIPTION OF WEB
============

The web part of this project consists of a public part and an admin panel, 
which can only be entered after passing authentication, having entered 
the username and password correctly.

In the public area, you can:
- receive news data from RCC networks;
- get data from third-party services;
- perform a simple search using filters;
- perform a full-text search on a pre-created index;
- send your contacts;
- select the language for viewing information;
- go to the authentication form for the subsequent entry of the username 
  and password, if entered correctly - then go to the admin panel.

In the admin area, you can:
- view the entire catalog with a page-by-page breakdown of information output, 
  re-sort by any available field in the table, view detailed data, update any record, 
  delete one or several records;
- generate new data;
- create a new index for full-text search;
- view and change the setting data.

DESCRIPTION OF API REQUESTS
============

1. get all items from the catalog
~~~~~~
GET /api/catalog
OUT OK (200):
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
  <id>/api/catalog</id>
  <title><![CDATA[Catalog records]]></title>
  <author>
    <name>Zf1 API/1.0</name>
  </author>
  <updated>2021-03-31T18:44:58+03:00</updated>
  <link rel="self" href="/api/catalog"/>
  <generator>Zend_Feed</generator>
  <entry>
    <id>/api/catalog/1</id>
    <title><![CDATA[Himalayas - Silver Jubilee - 1958]]></title>
    <updated>2009-12-06T00:00:00+03:00</updated>
    <link rel="alternate" href="/api/catalog/1"/>
    <summary><![CDATA[Silver jubilee issue. Aerial view of snow-capped.  
Himalayan mountains. Horizontal orange stripe across  
top margin. Excellent condition, no marks.]]></summary>
  </entry>
  <entry>
    <id>/api/catalog/3</id>
    <title><![CDATA[Book Pattern Designe - 2018]]></title>
    <updated>2018-06-30T00:00:00+03:00</updated>
    <link rel="alternate" href="/api/catalog/3"/>
    <summary><![CDATA[good]]></summary>
  </entry>
</feed>  
~~~~~~

2. get one item from the catalog
~~~~~~
GET /api/catalog/1
OUT OK (200):
<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom" xmlns:zf1="http://zf1.demo">
  <id>/api/catalog/1</id>
  <title><![CDATA[Catalog record for item ID: 1]]></title>
  <author>
    <name>Zf1 App/1.0</name>
  </author>
  <updated>2021-04-05T14:18:13+03:00</updated>
  <link rel="self" href="/api/catalog/1"/>
  <generator>Zend_Feed</generator>
  <entry>
    <id>/api/catalog/1</id>
    <title><![CDATA[Himalayas - Silver Jubilee - 1958]]></title>
    <updated>2009-12-06T00:00:00+03:00</updated>
    <link rel="alternate" href="/api/catalog/1"/>
    <summary><![CDATA[Silver jubilee issue. Aerial view of snow-capped.  
Himalayan mountains. Horizontal orange stripe across  
top margin. Excellent condition, no marks.]]></summary>
    <zf1:id xmlns:zf1="http://zf1.demo">1</zf1:id>
    <zf1:title xmlns:zf1="http://zf1.demo">Himalayas - Silver Jubilee</zf1:title>
    <zf1:year xmlns:zf1="http://zf1.demo">1958</zf1:year>
    <zf1:grade xmlns:zf1="http://zf1.demo">Fine</zf1:grade>
    <zf1:description xmlns:zf1="http://zf1.demo">Silver jubilee issue. Aerial view of snow-capped.  &#13;
Himalayan mountains. Horizontal orange stripe across  &#13;
top margin. Excellent condition, no marks.</zf1:description>
    <zf1:country xmlns:zf1="http://zf1.demo">India</zf1:country>
    <zf1:price xmlns:zf1="http://zf1.demo">
      <zf1:min>10</zf1:min>
      <zf1:max>15</zf1:max>
    </zf1:price>
  </entry>
</feed>
~~~~~~

3. save one new item into the catalog
~~~~~~
POST http://zf1.demo.os/api/catalog
Content-Type: multipart/form-data; boundary=WebAppBoundary

--WebAppBoundary
Content-Disposition: form-data; name="SellerName"
Content-Type: text/plain

Compeny Best
--WebAppBoundary
Content-Disposition: form-data; name="SellerEmail"
Content-Type: text/plain

best@gmail.com
--WebAppBoundary
Content-Disposition: form-data; name="SellerTel"
Content-Type: text/plain

+380501112233
--WebAppBoundary
Content-Disposition: form-data; name="SellerAddress"
Content-Type: text/plain

city Kiev
--WebAppBoundary
Content-Disposition: form-data; name="Title"
Content-Type: text/plain

Kiev
--WebAppBoundary
Content-Disposition: form-data; name="Year"
Content-Type: text/plain

2001
--WebAppBoundary
Content-Disposition: form-data; name="CountryID"
Content-Type: text/plain

12
--WebAppBoundary
Content-Disposition: form-data; name="Denomination"
Content-Type: text/plain

120
--WebAppBoundary
Content-Disposition: form-data; name="TypeID"
Content-Type: text/plain

1
--WebAppBoundary
Content-Disposition: form-data; name="GradeID"
Content-Type: text/plain

1
--WebAppBoundary
Content-Disposition: form-data; name="SalePriceMin"
Content-Type: text/plain

100
--WebAppBoundary
Content-Disposition: form-data; name="SalePriceMax"
Content-Type: text/plain

300
--WebAppBoundary
Content-Disposition: form-data; name="Description"
Content-Type: text/plain

sample text ...
--WebAppBoundary

OUT OK (201):
/api/catalog/11401

OUT ERROR (500)
~~~~~~

4. update one item into the catalog
~~~~~~
PUT http://zf1.demo.os/api/catalog/1?Year=1998

OUT OK (200):
/api/catalog/1

OUT ERROR (500)

OUT ERROR (404):
Invalid record identifier
~~~~~~

5. delete one item into the catalog
~~~~~~
DELETE http://zf1.demo.os/api/catalog/1

OUT OK (200):
deleted id = 1

OUT ERROR (404):
Invalid record identifier
~~~~~~

6. head one item into the catalog
~~~~~~
HEAD http://zf1.demo.os/api/catalog/1

OUT OK (200)

OUT ERROR (404)
~~~~~~