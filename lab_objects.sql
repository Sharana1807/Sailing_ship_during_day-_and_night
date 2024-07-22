CREATE DATABASE lab_objects;
USE lab_objects;

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE labs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) on delete cascade
);

CREATE TABLE systems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lab_id INT,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (lab_id) REFERENCES labs(id) on delete cascade
);

CREATE TABLE components (
    id INT AUTO_INCREMENT PRIMARY KEY,
    system_id INT,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (system_id) REFERENCES systems(id) on delete cascade
);

INSERT INTO departments (name) VALUES ('Civil engg'), ('CSE');
INSERT INTO labs (department_id, name) VALUES (2, 'Lab1'), (2, 'Lab2'), (2, 'Lab3');
INSERT INTO systems (lab_id, name) VALUES (1, 'System1'), (1, 'System2'), (2, 'System3');
INSERT INTO components (system_id, name) VALUES (1, 'Mouse'), (1, 'Keyboard'), (1, 'Monitor'),(2, 'Mouse'), (2, 'Keyboard'), (2, 'Monitor');