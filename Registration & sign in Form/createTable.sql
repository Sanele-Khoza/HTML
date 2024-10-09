CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    surname VARCHAR(100) NOT NULL,
    student_no VARCHAR(50) NOT NULL,
    contact VARCHAR(15) NOT NULL,
    module_code VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    password VARCHAR(255) NOT NULL
);

ALTER TABLE students ADD profile_image VARCHAR(255) DEFAULT NULL;
