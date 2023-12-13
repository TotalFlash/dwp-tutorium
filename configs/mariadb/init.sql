create schema if not exists tutorium;
use tutorium;

create table user
(
    id         int auto_increment,
    username   varchar(64)            not null,
    hash       varchar(512)           not null,
    salt       varchar(512)           not null,
    created_at datetime default now() not null,
    updated_at datetime default null  null on update now(),
    constraint user_pk
        primary key (id)
)
charset = utf8;


CREATE USER 'niclas'@'%' IDENTIFIED BY '#!asdf1234';
GRANT ALL PRIVILEGES ON tutorium.* TO 'niclas'@'%';
FLUSH PRIVILEGES;