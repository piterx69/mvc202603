-- Active: 1793154262816@@127.0.0.1@3306@ecommerce
CREATE TABLE proyectos(
    id int NOT NULL PRIMARY KEY AUTO_INCREMENT COMMENT 'Primary Key',
    name VARCHAR(255),
    status CHAR(3),
    owner VARCHAR(90),
    client VARCHAR(90),
    create_time DATETIME COMMENT 'Create Time'
) COMMENT '';
