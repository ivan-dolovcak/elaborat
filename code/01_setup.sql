-- Datoteka: sql/01_setup.sql

-- Napomena: korisničko ime, ime baze i lozinka su drugačiji na produkcijskom serveru. Ovo je samo primjer.
create user 'exam_engine_admin'@'localhost' identified by 'admin';
create database `exam_engine`;
-- Davanje "exam_engine_admin" korisniku svih prava (potpuna kontrola nad bazom):
grant all privileges on `exam_engine`.* to 'exam_engine_admin'@'localhost';
