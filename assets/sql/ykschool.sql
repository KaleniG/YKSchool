-- First, define the ENUM type for course status in PostgreSQL
DO $$
BEGIN
    IF NOT EXISTS (SELECT 1 FROM pg_type WHERE typname = 'course_status') THEN
        CREATE TYPE course_status AS ENUM ('Active', 'Suspended', 'UnderDevelopment');
    END IF;
END$$;

-- Administrators table
CREATE TABLE IF NOT EXISTS administrators (
    id SERIAL NOT NULL PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30) NOT NULL,
    email VARCHAR(320),
    phone_number VARCHAR(15),
    CONSTRAINT unique_administrator_identity UNIQUE (name, surname, email, phone_number)
);

-- Teachers table
CREATE TABLE IF NOT EXISTS teachers (
    id SERIAL NOT NULL PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30) NOT NULL,
    email VARCHAR(320),
    phone_number VARCHAR(15),
    CONSTRAINT unique_teacher_identity UNIQUE (name, surname, email, phone_number)
);

-- Students table
CREATE TABLE IF NOT EXISTS students (
    id SERIAL NOT NULL PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    surname VARCHAR(30) NOT NULL,
    email VARCHAR(320),
    phone_number VARCHAR(15),
    tuition_enabled BOOL DEFAULT NULL,
    CONSTRAINT unique_student_identity UNIQUE (name, surname, email, phone_number)
);

-- Subjects table
CREATE TABLE IF NOT EXISTS subjects (
    id SERIAL NOT NULL PRIMARY KEY,
    subject VARCHAR(20) NOT NULL
);

-- Courses table
CREATE TABLE IF NOT EXISTS courses (
    id SERIAL NOT NULL PRIMARY KEY,
    name VARCHAR(20) NOT NULL,
    description TEXT,
    status course_status DEFAULT 'UnderDevelopment',
    subject_id INT DEFAULT NULL,
    CONSTRAINT fk_course_subject FOREIGN KEY (subject_id)
        REFERENCES subjects (id) ON DELETE SET NULL
);

-- Subject-Teachers table
CREATE TABLE IF NOT EXISTS subject_teachers (
    id SERIAL NOT NULL PRIMARY KEY,
    subject_id INT NOT NULL,
    teacher_id INT NOT NULL,
    CONSTRAINT fk_subject FOREIGN KEY (subject_id)
        REFERENCES courses (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_teacher FOREIGN KEY (teacher_id)
        REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Course-Teachers table
CREATE TABLE IF NOT EXISTS course_teachers (
    id SERIAL NOT NULL PRIMARY KEY,
    course_id INT NOT NULL,
    teacher_id INT NOT NULL,
    CONSTRAINT fk_course_t FOREIGN KEY (course_id)
        REFERENCES courses (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_teacher FOREIGN KEY (teacher_id)
        REFERENCES teachers (id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- Course-Students table
CREATE TABLE IF NOT EXISTS course_students (
    id SERIAL NOT NULL PRIMARY KEY,
    course_id INT NOT NULL,
    student_id INT NOT NULL,
    CONSTRAINT fk_course_s FOREIGN KEY (course_id)
        REFERENCES courses (id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_student FOREIGN KEY (student_id)
        REFERENCES students (id) ON DELETE CASCADE ON UPDATE CASCADE
);