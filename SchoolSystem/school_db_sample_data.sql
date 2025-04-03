-- Insert sample data for testing

-- Insert Teachers
INSERT INTO Teachers (FirstName, LastName, Email, PhoneNumber, AnnualSalary, BackgroundCheckStatus, HireDate)
VALUES 
('Sarah', 'Johnson', 's.johnson@school.edu', '07123456789', 35000.00, 'Completed', '2020-09-01'),
('Michael', 'Brown', 'm.brown@school.edu', '07234567890', 38000.00, 'Completed', '2019-09-01'),
('Emily', 'Davis', 'e.davis@school.edu', '07345678901', 36000.00, 'Completed', '2021-09-01');

-- Insert Classes
INSERT INTO Classes (ClassName, Capacity, TeacherID)
VALUES 
('Reception Year', 20, 1),
('Year One', 25, 2),
('Year Two', 25, 3);

-- Insert Parents
INSERT INTO Parents (FirstName, LastName, Email, Telephone, RelationshipToPupil)
VALUES 
('David', 'Smith', 'd.smith@email.com', '07456789012', 'Father'),
('Jennifer', 'Smith', 'j.smith@email.com', '07567890123', 'Mother'),
('Robert', 'Wilson', 'r.wilson@email.com', '07678901234', 'Father');

-- Insert Pupils
INSERT INTO Pupils (FirstName, LastName, DateOfBirth, ClassID, Parent1ID, Parent2ID)
VALUES 
('James', 'Smith', '2018-05-15', 1, 1, 2),
('Sophie', 'Smith', '2017-03-22', 2, 1, 2),
('Oliver', 'Wilson', '2017-07-10', 2, 3, NULL);

-- Insert Enrollment records
INSERT INTO Enrollment (PupilID, ClassID, AcademicYear, EnrollmentDate)
VALUES 
(1, 1, '2024/2025', '2024-09-05'),
(2, 2, '2024/2025', '2024-09-05'),
(3, 2, '2024/2025', '2024-09-05');

-- Insert Attendance records
INSERT INTO Attendance (PupilID, Date, Status)
VALUES 
(1, '2024-09-10', 'Present'),
(1, '2024-09-11', 'Present'),
(2, '2024-09-10', 'Present'),
(2, '2024-09-11', 'Absent'),
(3, '2024-09-10', 'Late'),
(3, '2024-09-11', 'Present');

-- Insert Performance records
INSERT INTO Performance (PupilID, Subject, Grade, Term, AssessmentDate)
VALUES 
(1, 'Reading', 'A', 'Autumn', '2024-10-15'),
(1, 'Math', 'B', 'Autumn', '2024-10-17'),
(2, 'Reading', 'A+', 'Autumn', '2024-10-15'),
(2, 'Math', 'A', 'Autumn', '2024-10-17'),
(3, 'Reading', 'B', 'Autumn', '2024-10-15'),
(3, 'Math', 'C', 'Autumn', '2024-10-17');