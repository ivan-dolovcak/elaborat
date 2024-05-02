-- Datoteka: sql/02_schema.sql

create table `User` (
    `ID`                   mediumint unsigned not null auto_increment,
    `username`             varchar(30) not null,
    `email`                varchar(50) not null,
    -- PHP password_hash() koristi algoritam PASSWORD_BCRYPT koji uvijek vraća hash duljine 60 znakova:
    `passwordHash`         char(60) not null,
    `firstName`            varchar(40) not null,
    `lastName`             varchar(40) not null,
    `creationDate`         date not null default utc_date(),
    `lastLoginTime`        datetime not null default utc_timestamp(),
    primary key (`ID`),
    constraint `UK_username`    unique key (`username`),
    constraint `UK_email`       unique key (`email`)
);

create table `Document` (
    `ID`                  mediumint unsigned not null auto_increment,
    `name`                varchar(50) not null,
    `type`                enum("exam", "form") not null,
    `visibility`          enum("public", "unlisted", "private")
                          not null default "private",
    `numMaxSubmissions`   tinyint unsigned,
    `authorID`            mediumint unsigned not null,
    `deadlineDatetime`    datetime,
    `creationDate`        date not null default utc_date(),
    `documentJSON`        json,
    `solutionJSON`        json,
    primary key (`ID`),
    constraint `FK_author`
        foreign key (`authorID`) references `User`(`ID`)
        on delete cascade
);
