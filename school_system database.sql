-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2025 at 12:41 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_system`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `ArchiveInactivePupils` ()   BEGIN
  UPDATE `pupils`
  SET IsArchived = TRUE,
      ArchivedDate = CURDATE()
  WHERE `PupilID` IN (
    SELECT p.PupilID
    FROM `pupils` p
    WHERE p.CurrentStatus IN ('Graduated', 'Transferred', 'Withdrawn')
    AND NOT EXISTS (
      SELECT 1 FROM `attendance` a
      WHERE a.PupilID = p.PupilID
      AND a.Date > DATE_SUB(CURDATE(), INTERVAL 2 YEAR)
    )
  );
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GeneratePasswordReset` (IN `p_email` VARCHAR(100))   BEGIN
  DECLARE v_token VARCHAR(100);
  SET v_token = UUID();
  
  UPDATE `users`
  SET PasswordResetToken = v_token,
      TokenExpiry = DATE_ADD(NOW(), INTERVAL 1 HOUR)
  WHERE Email = p_email;
  
  SELECT v_token AS ResetToken;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ProcessBookReturn` (IN `p_loan_id` INT, IN `p_condition` ENUM('Good','Damaged','Lost'))   BEGIN
    DECLARE v_due_date DATE;
    DECLARE v_days_late INT;
    DECLARE v_fine_amount DECIMAL(10,2);
    
    SELECT DueDate INTO v_due_date FROM BookLoans WHERE LoanID = p_loan_id;
    
    SET v_days_late = DATEDIFF(CURDATE(), v_due_date);
    SET v_fine_amount = 0;
    
    IF v_days_late > 0 THEN
        SET v_fine_amount = LEAST(v_days_late * 0.50, 20.00); -- Max £20 fine
    END IF;
    
    IF p_condition = 'Damaged' THEN
        SET v_fine_amount = v_fine_amount + 5.00;
    ELSEIF p_condition = 'Lost' THEN
        -- Get book replacement cost
        SELECT Price INTO v_fine_amount FROM LibraryBooks 
        WHERE BookID = (SELECT BookID FROM BookLoans WHERE LoanID = p_loan_id);
    END IF;
    
    -- Update loan record
    UPDATE BookLoans 
    SET ReturnDate = CURDATE(),
        Status = IF(p_condition = 'Lost', 'Lost', 'Returned'),
        FineAmount = v_fine_amount
    WHERE LoanID = p_loan_id;
    
    -- Record in history
    INSERT INTO LoanHistory (LoanID, BookID, PupilID, Action, ActionDate, Details)
    SELECT LoanID, BookID, PupilID, 
           IF(p_condition = 'Lost', 'Fine Applied', 'Returned'),
           NOW(),
           CONCAT('Condition: ', p_condition, IF(v_fine_amount > 0, 
                 CONCAT(' | Fine: £', v_fine_amount), ''))
    FROM BookLoans WHERE LoanID = p_loan_id;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `ValidatePasswordReset` (IN `p_token` VARCHAR(100), IN `p_new_password` VARCHAR(255))   BEGIN
  UPDATE `users`
  SET PasswordHash = p_new_password,
      PasswordResetToken = NULL,
      TokenExpiry = NULL
  WHERE PasswordResetToken = p_token
    AND TokenExpiry > NOW();
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `decrypt_data` (`data` VARBINARY(255), `secret_key` VARCHAR(255)) RETURNS TEXT CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
RETURN AES_DECRYPT(data, secret_key);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `encrypt_data` (`data` TEXT, `secret_key` VARCHAR(255)) RETURNS VARBINARY(255) DETERMINISTIC BEGIN
RETURN AES_ENCRYPT(data, secret_key);
END$$

CREATE DEFINER=`root`@`localhost` FUNCTION `hash_password` (`pwd` VARCHAR(255), `salt` VARCHAR(64)) RETURNS VARCHAR(255) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC BEGIN
  RETURN SHA2(CONCAT(pwd, salt), 256);
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Salt` varchar(64) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Role` enum('Admin','Teacher','Assistant','Parent') NOT NULL,
  `AssociatedID` int(11) DEFAULT NULL,
  `LastLogin` datetime DEFAULT NULL,
  `AccountLocked` tinyint(1) DEFAULT 0,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`UserID`, `Username`, `PasswordHash`, `Salt`, `Email`, `Role`, `AssociatedID`, `LastLogin`, `AccountLocked`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'admin', '$2y$10$D1yA9HQ2z9E9TXH/THwOEuRAse8b4Vgd12iP0PzzFi89wOjwFqIqO', '', 'admin@example.com', 'Admin', NULL, NULL, 0, '2025-04-10 14:19:31', '2025-04-10 14:19:31');

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `AttendanceID` int(11) NOT NULL,
  `PupilID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Status` enum('Present','Absent','Late') NOT NULL,
  `Notes` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`AttendanceID`, `PupilID`, `Date`, `Status`, `Notes`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 1, '2024-09-10', 'Present', NULL, '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(2, 1, '2024-09-11', 'Present', NULL, '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(3, 2, '2024-09-10', 'Present', NULL, '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(4, 2, '2024-09-11', 'Absent', NULL, '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(5, 3, '2024-09-10', 'Late', NULL, '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(6, 3, '2024-09-11', 'Present', NULL, '2025-04-03 13:06:22', '2025-04-03 13:06:22');

-- --------------------------------------------------------

--
-- Stand-in structure for view `attendancesummary`
-- (See below for the actual view)
--
CREATE TABLE `attendancesummary` (
`PupilID` int(11)
,`PupilName` varchar(101)
,`ClassName` varchar(50)
,`DaysPresent` bigint(21)
,`DaysAbsent` bigint(21)
,`DaysLate` bigint(21)
,`TotalDays` bigint(21)
,`AttendancePercentage` decimal(25,1)
);

-- --------------------------------------------------------

--
-- Table structure for table `bookcategories`
--

CREATE TABLE `bookcategories` (
  `CategoryID` int(11) NOT NULL,
  `CategoryName` varchar(50) NOT NULL,
  `Description` text DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bookloans`
--

CREATE TABLE `bookloans` (
  `LoanID` int(11) NOT NULL,
  `BookID` int(11) NOT NULL,
  `PupilID` int(11) NOT NULL,
  `CheckoutDate` date NOT NULL,
  `DueDate` date NOT NULL,
  `ReturnDate` date DEFAULT NULL,
  `Status` enum('Active','Returned','Overdue') DEFAULT 'Active',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `ClassID` int(11) NOT NULL,
  `ClassName` varchar(50) NOT NULL,
  `Capacity` int(11) NOT NULL,
  `TeacherID` int(11) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`ClassID`, `ClassName`, `Capacity`, `TeacherID`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'Reception Year', 20, 1, '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(2, 'Year One', 25, 2, '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(3, 'Year Two', 25, 3, '2025-04-03 13:06:22', '2025-04-03 13:06:22');

-- --------------------------------------------------------

--
-- Table structure for table `enrollment`
--

CREATE TABLE `enrollment` (
  `EnrollmentID` int(11) NOT NULL,
  `PupilID` int(11) NOT NULL,
  `ClassID` int(11) NOT NULL,
  `AcademicYear` varchar(9) NOT NULL,
  `EnrollmentDate` date NOT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrollment`
--

INSERT INTO `enrollment` (`EnrollmentID`, `PupilID`, `ClassID`, `AcademicYear`, `EnrollmentDate`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 1, 1, '2024/2025', '2024-09-05', '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(2, 2, 2, '2024/2025', '2024-09-05', '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(3, 3, 2, '2024/2025', '2024-09-05', '2025-04-03 13:06:22', '2025-04-03 13:06:22');

-- --------------------------------------------------------

--
-- Table structure for table `librarybooks`
--

CREATE TABLE `librarybooks` (
  `BookID` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Author` varchar(100) DEFAULT NULL,
  `ISBN` varchar(20) DEFAULT NULL,
  `Status` enum('Available','Checked Out','Lost') DEFAULT 'Available',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `CategoryID` int(11) DEFAULT NULL,
  `DeweyDecimal` varchar(20) DEFAULT NULL,
  `AcquisitionDate` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `loanhistory`
--

CREATE TABLE `loanhistory` (
  `LoanHistoryID` int(11) NOT NULL,
  `LoanID` int(11) NOT NULL,
  `BookID` int(11) NOT NULL,
  `PupilID` int(11) NOT NULL,
  `Action` enum('Checked Out','Returned','Renewed','Fine Applied') NOT NULL,
  `ActionDate` datetime NOT NULL,
  `Details` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parents`
--

CREATE TABLE `parents` (
  `ParentID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Telephone` varchar(20) DEFAULT NULL,
  `RelationshipToPupil` varchar(50) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `parents`
--

INSERT INTO `parents` (`ParentID`, `FirstName`, `LastName`, `Address`, `Email`, `Telephone`, `RelationshipToPupil`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'David', 'Smith', NULL, 'd.smith@email.com', '07456789012', 'Father', '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(2, 'Jennifer', 'Smith', NULL, 'j.smith@email.com', '07567890123', 'Mother', '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(3, 'Robert', 'Wilson', NULL, 'r.wilson@email.com', '07678901234', 'Father', '2025-04-03 13:06:22', '2025-04-03 13:06:22');

-- --------------------------------------------------------

--
-- Table structure for table `performance`
--

CREATE TABLE `performance` (
  `PerformanceID` int(11) NOT NULL,
  `PupilID` int(11) NOT NULL,
  `Subject` varchar(50) NOT NULL,
  `Grade` varchar(2) DEFAULT NULL,
  `Term` varchar(20) DEFAULT NULL,
  `Comments` text DEFAULT NULL,
  `AssessmentDate` date DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `performance`
--

INSERT INTO `performance` (`PerformanceID`, `PupilID`, `Subject`, `Grade`, `Term`, `Comments`, `AssessmentDate`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 1, 'Reading', 'A', 'Autumn', NULL, '2024-10-15', '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(2, 1, 'Math', 'B', 'Autumn', NULL, '2024-10-17', '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(3, 2, 'Reading', 'A+', 'Autumn', NULL, '2024-10-15', '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(4, 2, 'Math', 'A', 'Autumn', NULL, '2024-10-17', '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(5, 3, 'Reading', 'B', 'Autumn', NULL, '2024-10-15', '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(6, 3, 'Math', 'C', 'Autumn', NULL, '2024-10-17', '2025-04-03 13:06:22', '2025-04-03 13:06:22');

-- --------------------------------------------------------

--
-- Table structure for table `pupilparent`
--

CREATE TABLE `pupilparent` (
  `PupilID` int(11) NOT NULL,
  `ParentID` int(11) NOT NULL,
  `Relationship` varchar(50) DEFAULT 'Parent/Guardian',
  `IsPrimary` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pupils`
--

CREATE TABLE `pupils` (
  `PupilID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `DateOfBirth` date DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `MedicalInformation` text DEFAULT NULL,
  `ClassID` int(11) DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Age` int(11) GENERATED ALWAYS AS (timestampdiff(YEAR,`DateOfBirth`,curdate())) VIRTUAL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pupils`
--

INSERT INTO `pupils` (`PupilID`, `FirstName`, `LastName`, `DateOfBirth`, `Address`, `MedicalInformation`, `ClassID`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'James', 'Smith', '2018-05-15', NULL, NULL, 1, '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(2, 'Sophie', 'Smith', '2017-03-22', NULL, NULL, 2, '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(3, 'Oliver', 'Wilson', '2017-07-10', NULL, NULL, 2, '2025-04-03 13:06:22', '2025-04-03 13:06:22'),
(5, 'Jana', 'Cve', '2000-02-02', 'Merseyside Liverpool', 'weak arm ', 1, '2025-04-10 21:35:04', '2025-04-10 21:35:04');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `SubjectID` int(11) NOT NULL,
  `SubjectName` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`SubjectID`, `SubjectName`) VALUES
(6, 'Art'),
(2, 'English'),
(5, 'Geography'),
(4, 'History'),
(1, 'Mathematics'),
(7, 'Physical Education'),
(3, 'Science');

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `TeacherID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `AnnualSalary` decimal(10,2) DEFAULT NULL,
  `BackgroundCheckStatus` enum('Completed','Pending','Failed') DEFAULT 'Pending',
  `HireDate` date DEFAULT NULL,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `SubjectID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`TeacherID`, `FirstName`, `LastName`, `Address`, `Email`, `PhoneNumber`, `AnnualSalary`, `BackgroundCheckStatus`, `HireDate`, `CreatedAt`, `UpdatedAt`, `SubjectID`) VALUES
(1, 'Sarah', 'Johnson', NULL, 's.johnson@school.edu', '07123456789', 35000.00, 'Completed', '2020-09-01', '2025-04-03 13:06:22', '2025-04-03 13:06:22', NULL),
(2, 'Michael', 'Brown', NULL, 'm.brown@school.edu', '07234567890', 38000.00, 'Completed', '2019-09-01', '2025-04-03 13:06:22', '2025-04-03 13:06:22', NULL),
(3, 'Emily', 'Davis', NULL, 'e.davis@school.edu', '07345678901', 36000.00, 'Completed', '2021-09-01', '2025-04-03 13:06:22', '2025-04-03 13:06:22', NULL),
(5, 'Patricia ', 'Bean', NULL, 'Patriciabean@gmail.com', '7869482748', 25000.00, 'Failed', '2025-04-10', '2025-04-10 18:23:12', '2025-04-10 18:23:12', 3);

--
-- Triggers `teachers`
--
DELIMITER $$
CREATE TRIGGER `after_teacher_insert` AFTER INSERT ON `teachers` FOR EACH ROW BEGIN
  DECLARE v_salt VARCHAR(64);
  SET v_salt = SUBSTRING(SHA2(RAND(), 256), 1, 64); -- Generate random salt
  
  INSERT INTO Users (Username, PasswordHash, Salt, Email, Role, AssociatedID)
  VALUES (
    LOWER(CONCAT(NEW.FirstName, '.', NEW.LastName)),
    hash_password(UUID(), v_salt), -- Temporary random password
    v_salt,
    NEW.Email,
    'Teacher',
    NEW.TeacherID
  );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `teachingassistants`
--

CREATE TABLE `teachingassistants` (
  `AssistantID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `ClassID` int(11) DEFAULT NULL,
  `PhoneNumber` varchar(20) DEFAULT NULL,
  `BackgroundCheckStatus` enum('Completed','Pending','Failed') DEFAULT 'Pending',
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL COMMENT 'BCrypt hashed',
  `Salt` varchar(64) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Role` enum('Admin','Teacher','Assistant','Parent') NOT NULL,
  `AssociatedID` int(11) DEFAULT NULL COMMENT 'Links to existing Teacher/Parent records',
  `LastLogin` datetime DEFAULT NULL,
  `AccountLocked` tinyint(1) DEFAULT 0,
  `CreatedAt` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdatedAt` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `Username`, `PasswordHash`, `Salt`, `Email`, `Role`, `AssociatedID`, `LastLogin`, `AccountLocked`, `CreatedAt`, `UpdatedAt`) VALUES
(1, 'admin', 'ba4ba5208ab48a144b04944847e7f72b152a38a86847bbd174ad70059e4769f0', 'a1b2c3d4e5f6', 'admin@school.edu', 'Admin', NULL, NULL, 0, '2025-04-08 13:26:22', '2025-04-09 16:26:54'),
(3, 'patricia .bean', '1301d7d7d9936650ea90e04b99f8b7837a9c4939be559ae33c8e8dced317005c', '68ea0a68c57b27bd114193f762fc2375deac6432ab0542a705f3e5e70dccd791', 'Patriciabean@gmail.com', 'Teacher', 5, NULL, 0, '2025-04-10 18:23:12', '2025-04-10 18:23:12');

-- --------------------------------------------------------

--
-- Structure for view `attendancesummary`
--
DROP TABLE IF EXISTS `attendancesummary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `attendancesummary`  AS SELECT `p`.`PupilID` AS `PupilID`, concat(`p`.`FirstName`,' ',`p`.`LastName`) AS `PupilName`, `c`.`ClassName` AS `ClassName`, count(case when `a`.`Status` = 'Present' then 1 end) AS `DaysPresent`, count(case when `a`.`Status` = 'Absent' then 1 end) AS `DaysAbsent`, count(case when `a`.`Status` = 'Late' then 1 end) AS `DaysLate`, count(0) AS `TotalDays`, round(count(case when `a`.`Status` = 'Present' then 1 end) / count(0) * 100,1) AS `AttendancePercentage` FROM ((`pupils` `p` join `classes` `c` on(`p`.`ClassID` = `c`.`ClassID`)) left join `attendance` `a` on(`p`.`PupilID` = `a`.`PupilID`)) WHERE `a`.`Date` between curdate() - interval 3 month and curdate() GROUP BY `p`.`PupilID` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`AttendanceID`),
  ADD UNIQUE KEY `unique_attendance` (`PupilID`,`Date`);

--
-- Indexes for table `bookcategories`
--
ALTER TABLE `bookcategories`
  ADD PRIMARY KEY (`CategoryID`);

--
-- Indexes for table `bookloans`
--
ALTER TABLE `bookloans`
  ADD PRIMARY KEY (`LoanID`),
  ADD KEY `BookID` (`BookID`),
  ADD KEY `PupilID` (`PupilID`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`ClassID`),
  ADD KEY `TeacherID` (`TeacherID`);

--
-- Indexes for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD PRIMARY KEY (`EnrollmentID`),
  ADD UNIQUE KEY `unique_enrollment` (`PupilID`,`ClassID`,`AcademicYear`),
  ADD KEY `ClassID` (`ClassID`);

--
-- Indexes for table `librarybooks`
--
ALTER TABLE `librarybooks`
  ADD PRIMARY KEY (`BookID`),
  ADD KEY `fk_book_category` (`CategoryID`);

--
-- Indexes for table `loanhistory`
--
ALTER TABLE `loanhistory`
  ADD PRIMARY KEY (`LoanHistoryID`),
  ADD KEY `BookID` (`BookID`),
  ADD KEY `PupilID` (`PupilID`),
  ADD KEY `idx_loan_history` (`LoanID`,`ActionDate`),
  ADD KEY `idx_loanhistory_actiondate` (`ActionDate`);

--
-- Indexes for table `parents`
--
ALTER TABLE `parents`
  ADD PRIMARY KEY (`ParentID`);

--
-- Indexes for table `performance`
--
ALTER TABLE `performance`
  ADD PRIMARY KEY (`PerformanceID`),
  ADD KEY `PupilID` (`PupilID`),
  ADD KEY `idx_performance_term` (`Term`);

--
-- Indexes for table `pupilparent`
--
ALTER TABLE `pupilparent`
  ADD PRIMARY KEY (`PupilID`,`ParentID`),
  ADD KEY `ParentID` (`ParentID`);

--
-- Indexes for table `pupils`
--
ALTER TABLE `pupils`
  ADD PRIMARY KEY (`PupilID`),
  ADD KEY `ClassID` (`ClassID`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`SubjectID`),
  ADD UNIQUE KEY `SubjectName` (`SubjectName`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`TeacherID`),
  ADD KEY `SubjectID` (`SubjectID`);

--
-- Indexes for table `teachingassistants`
--
ALTER TABLE `teachingassistants`
  ADD PRIMARY KEY (`AssistantID`),
  ADD KEY `ClassID` (`ClassID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `idx_user_role` (`Role`),
  ADD KEY `idx_user_associated` (`AssociatedID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `AttendanceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `bookcategories`
--
ALTER TABLE `bookcategories`
  MODIFY `CategoryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bookloans`
--
ALTER TABLE `bookloans`
  MODIFY `LoanID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `ClassID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `enrollment`
--
ALTER TABLE `enrollment`
  MODIFY `EnrollmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `librarybooks`
--
ALTER TABLE `librarybooks`
  MODIFY `BookID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `loanhistory`
--
ALTER TABLE `loanhistory`
  MODIFY `LoanHistoryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parents`
--
ALTER TABLE `parents`
  MODIFY `ParentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `performance`
--
ALTER TABLE `performance`
  MODIFY `PerformanceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pupils`
--
ALTER TABLE `pupils`
  MODIFY `PupilID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `SubjectID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `TeacherID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `teachingassistants`
--
ALTER TABLE `teachingassistants`
  MODIFY `AssistantID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`PupilID`) REFERENCES `pupils` (`PupilID`) ON DELETE CASCADE;

--
-- Constraints for table `bookloans`
--
ALTER TABLE `bookloans`
  ADD CONSTRAINT `bookloans_ibfk_1` FOREIGN KEY (`BookID`) REFERENCES `librarybooks` (`BookID`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookloans_ibfk_2` FOREIGN KEY (`PupilID`) REFERENCES `pupils` (`PupilID`) ON DELETE CASCADE;

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`TeacherID`) REFERENCES `teachers` (`TeacherID`) ON DELETE SET NULL;

--
-- Constraints for table `enrollment`
--
ALTER TABLE `enrollment`
  ADD CONSTRAINT `enrollment_ibfk_1` FOREIGN KEY (`PupilID`) REFERENCES `pupils` (`PupilID`) ON DELETE CASCADE,
  ADD CONSTRAINT `enrollment_ibfk_2` FOREIGN KEY (`ClassID`) REFERENCES `classes` (`ClassID`) ON DELETE CASCADE;

--
-- Constraints for table `librarybooks`
--
ALTER TABLE `librarybooks`
  ADD CONSTRAINT `fk_book_category` FOREIGN KEY (`CategoryID`) REFERENCES `bookcategories` (`CategoryID`) ON DELETE SET NULL;

--
-- Constraints for table `loanhistory`
--
ALTER TABLE `loanhistory`
  ADD CONSTRAINT `loanhistory_ibfk_1` FOREIGN KEY (`BookID`) REFERENCES `librarybooks` (`BookID`),
  ADD CONSTRAINT `loanhistory_ibfk_2` FOREIGN KEY (`PupilID`) REFERENCES `pupils` (`PupilID`);

--
-- Constraints for table `performance`
--
ALTER TABLE `performance`
  ADD CONSTRAINT `performance_ibfk_1` FOREIGN KEY (`PupilID`) REFERENCES `pupils` (`PupilID`) ON DELETE CASCADE;

--
-- Constraints for table `pupilparent`
--
ALTER TABLE `pupilparent`
  ADD CONSTRAINT `pupilparent_ibfk_1` FOREIGN KEY (`PupilID`) REFERENCES `pupils` (`PupilID`) ON DELETE CASCADE,
  ADD CONSTRAINT `pupilparent_ibfk_2` FOREIGN KEY (`ParentID`) REFERENCES `parents` (`ParentID`) ON DELETE CASCADE;

--
-- Constraints for table `pupils`
--
ALTER TABLE `pupils`
  ADD CONSTRAINT `pupils_ibfk_1` FOREIGN KEY (`ClassID`) REFERENCES `classes` (`ClassID`) ON DELETE SET NULL;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`SubjectID`) REFERENCES `subjects` (`SubjectID`);

--
-- Constraints for table `teachingassistants`
--
ALTER TABLE `teachingassistants`
  ADD CONSTRAINT `teachingassistants_ibfk_1` FOREIGN KEY (`ClassID`) REFERENCES `classes` (`ClassID`) ON DELETE SET NULL;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `QuarterlyMaintenance` ON SCHEDULE EVERY 3 MONTH STARTS '2025-04-09 00:00:00' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    CALL ArchiveInactivePupils();
    ANALYZE TABLE Teachers, Classes, Pupils, Parents, Attendance;
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
