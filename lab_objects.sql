
create database lab_objects;
use lab_objects;

CREATE TABLE colleges (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL
);

CREATE TABLE departments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    college_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (college_id) REFERENCES colleges(id) on delete cascade
);

CREATE TABLE labs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (department_id) REFERENCES departments(id) on delete cascade
);

CREATE TABLE systems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    lab_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (lab_id) REFERENCES labs(id) on delete cascade
);

CREATE TABLE components (
    id INT AUTO_INCREMENT PRIMARY KEY,
    system_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    qr_code_path VARCHAR(255),
    FOREIGN KEY (system_id) REFERENCES systems(id) on delete cascade
);

CREATE TABLE campus_parts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    college_id int,
    name VARCHAR(255) NOT NULL,
    FOREIGN KEY (college_id) REFERENCES colleges(id) on delete cascade
);

CREATE TABLE trees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    campus_part_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    details TEXT,
    qr_code_path VARCHAR(255),
    FOREIGN KEY (campus_part_id) REFERENCES campus_parts(id) on delete cascade
);

insert into colleges values (1,"Government Engineering College Hassan");
insert into departments(id,college_id,name) values (1,1,"Civil Engineering"),(2,1,"Computer Science and Engineering"),(3,1,"Electronics and Communication Engineering"),(4,1,"Mechanical Engineering");
insert into labs(id,department_id,name) values (201,2,"Lab 1"),(202,2,"Lab 2");
insert into systems(id,lab_id,name) values (1,201,"System 1"),(2,201,"System 2");
insert into components(system_id,name) values (1,"Mouse"),(1,"Monitor"),(1,"Keyboard"),(1,"CPU");
insert into campus_parts(id,college_id,name) values (1001,1,"Front part");
insert into trees(campus_part_id,name,details) values (1001,"Jackfruit tree","Binomial name is Artocarpus heteropyllus, moraceae family");
