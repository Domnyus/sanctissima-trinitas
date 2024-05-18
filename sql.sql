create database db;
use db;

create table api_auth
(
id int primary key auto_increment,
user varchar(32),
password varchar(255),
token varchar(255)
);

create table api_routes
(
id int primary key auto_increment,
path varchar(255),
method varchar(255),
route varchar(255),
method varchar(255),
file varchar(255)
);

create table api_auth_routes
(
api_auth int not null,
api_route int not null,
foreign key (api_auth) references api_auth (id) on delete cascade,
foreign key (api_route) references api_routes (id) on delete cascade
);

create table views
(
id int primary key auto_increment,
uri varchar(255),
path varchar(255),
method varchar(255),
class varchar(255),
view varchar(255)
);