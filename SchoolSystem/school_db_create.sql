-- Database: school_management_system

CREATE DATABASE IF NOT EXISTS school_management_system;
USE school_management_system;

-- Teachers table
CREATE TABLE Teachers (
    TeacherID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Address VARCHAR(255),
    Email VARCHAR(100),
    PhoneNumber VARCHAR(20),
    AnnualSalary DECIMAL(10,2),
    BackgroundCheckStatus ENUM('Completed', 'Pending', 'Failed') DEFAULT 'Pending',
    HireDate DATE,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Classes table
CREATE TABLE Classes (
    ClassID INT AUTO_INCREMENT PRIMARY KEY,
    ClassName VARCHAR(50) NOT NULL,
    Capacity INT NOT NULL,
    TeacherID INT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (TeacherID) REFERENCES Teachers(TeacherID) ON DELETE SET NULL
);

-- Parents table
CREATE TABLE Parents (
    ParentID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    Address VARCHAR(255),
    Email VARCHAR(100),
    Telephone VARCHAR(20),
    RelationshipToPupil VARCHAR(50),
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Pupils table
CREATE TABLE Pupils (
    PupilID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    DateOfBirth DATE,
    Address VARCHAR(255),
    MedicalInformation TEXT,
    ClassID INT,
    Parent1ID INT,
    Parent2ID INT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ClassID) REFERENCES Classes(ClassID) ON DELETE SET NULL,
    FOREIGN KEY (Parent1ID) REFERENCES Parents(ParentID) ON DELETE SET NULL,
    FOREIGN KEY (Parent2ID) REFERENCES Parents(ParentID) ON DELETE SET NULL
);

-- Enrollment table (bridge between Pupils and Classes)
CREATE TABLE Enrollment (
    EnrollmentID INT AUTO_INCREMENT PRIMARY KEY,
    PupilID INT NOT NULL,
    ClassID INT NOT NULL,
    AcademicYear VARCHAR(9) NOT NULL, -- e.g., "2024/2025"
    EnrollmentDate DATE NOT NULL,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (PupilID) REFERENCES Pupils(PupilID) ON DELETE CASCADE,
    FOREIGN KEY (ClassID) REFERENCES Classes(ClassID) ON DELETE CASCADE,
    UNIQUE KEY unique_enrollment (PupilID, ClassID, AcademicYear)
);

-- Attendance table
CREATE TABLE Attendance (
    AttendanceID INT AUTO_INCREMENT PRIMARY KEY,
    PupilID INT NOT NULL,
    Date DATE NOT NULL,
    Status ENUM('Present', 'Absent', 'Late') NOT NULL,
    Notes TEXT,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (PupilID) REFERENCES Pupils(PupilID) ON DELETE CASCADE,
    UNIQUE KEY unique_attendance (PupilID, Date)
);

-- Performance table
CREATE TABLE Performance (
    PerformanceID INT AUTO_INCREMENT PRIMARY KEY,
    PupilID INT NOT NULL,
    Subject VARCHAR(50) NOT NULL,
    Grade VARCHAR(2),
    Term VARCHAR(20),
    Comments TEXT,
    AssessmentDate DATE,
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (PupilID) REFERENCES Pupils(PupilID) ON DELETE CASCADE
);

-- Optional: Teaching Assistants table
CREATE TABLE TeachingAssistants (
    AssistantID INT AUTO_INCREMENT PRIMARY KEY,
    FirstName VARCHAR(50) NOT NULL,
    LastName VARCHAR(50) NOT NULL,
    ClassID INT,
    PhoneNumber VARCHAR(20),
    BackgroundCheckStatus ENUM('Completed', 'Pending', 'Failed') DEFAULT 'Pending',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ClassID) REFERENCES Classes(ClassID) ON DELETE SET NULL
);

-- Optional: Library Books table
CREATE TABLE LibraryBooks (
    BookID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(255) NOT NULL,
    Author VARCHAR(100),
    ISBN VARCHAR(20),
    Status ENUM('Available', 'Checked Out', 'Lost') DEFAULT 'Available',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Optional: Book Loans table
CREATE TABLE BookLoans (
    LoanID INT AUTO_INCREMENT PRIMARY KEY,
    BookID INT NOT NULL,
    PupilID INT NOT NULL,
    CheckoutDate DATE NOT NULL,
    DueDate DATE NOT NULL,
    ReturnDate DATE,
    Status ENUM('Active', 'Returned', 'Overdue') DEFAULT 'Active',
    CreatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UpdatedAt TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (BookID) REFERENCES LibraryBooks(BookID) ON DELETE CASCADE,
    FOREIGN KEY (PupilID) REFERENCES Pupils(PupilID) ON DELETE CASCADE
);