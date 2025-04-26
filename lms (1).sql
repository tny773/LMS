-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 26, 2025 at 06:25 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lms`
--
CREATE DATABASE IF NOT EXISTS `lms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `lms`;

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

DROP TABLE IF EXISTS `accounts`;
CREATE TABLE `accounts` (
  `Account_ID` int(11) NOT NULL,
  `Member_ID` int(11) DEFAULT NULL,
  `Payment_Description` text DEFAULT NULL,
  `Payment_Amount` decimal(10,2) DEFAULT NULL,
  `Payment_Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- dumping data for table accounts
--
INSERT INTO accounts (Account_ID, Member_ID, Payment_Description, Payment_Amount, Payment_Date) VALUES
(1, 101, 'Late fee for overdue book', 15.00, '2025-03-15'),
(2, 102, 'Membership renewal', 50.00, '2025-01-10'),
(3, 103, 'Lost book fine', 120.00, '2025-02-20'),
(4, 105, 'Damage charge', 30.00, '2025-04-01'),
(5, 108, 'Late return fine', 10.00, '2025-03-25'),
(6, 109, 'Membership renewal', 50.00, '2025-02-01'),
(7, 106, 'Late fee for overdue book', 20.00, '2025-03-05'),
(8, 107, 'Lost book replacement', 150.00, '2025-02-15'),
(9, 104, 'Membership cancellation refund', -20.00, '2025-01-30'),
(10, 103, 'Fine adjustment', -10.00, '2025-04-10');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

DROP TABLE IF EXISTS `books`;
CREATE TABLE `books` (
  `Book_ID` int(11) NOT NULL,
  `Book_Title` varchar(200) NOT NULL,
  `Subject_ID` int(11) DEFAULT NULL,
  `Publisher_ID` int(11) DEFAULT NULL,
  `Category_ID` int(11) DEFAULT NULL,
  `Purchase_Date` date DEFAULT NULL,
  `Available_Status` enum('Available','Issued','Lost') DEFAULT 'Available',
  `Total_Copies` int(11) DEFAULT NULL CHECK (`Total_Copies` >= 0),
  `Available_Copies` int(11) DEFAULT NULL CHECK (`Available_Copies` >= 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
--dumping data for table books
--
INSERT INTO `books` (`Book_ID`, `Book_Title`, `Subject_ID`, `Publisher_ID`, `Category_ID`, `Purchase_Date`, `Available_Status`, `Total_Copies`, `Available_Copies`) VALUES
-- Physics (Category: Science = 2)
(1, 'Fundamentals of Physics', 1, 1, 2, '2022-01-15', 'Available', 10, 7),
(2, 'Modern Physics Concepts', 1, 3, 2, '2021-09-10', 'Issued', 5, 2),
(3, 'Applied Physics', 1, 4, 2, '2023-03-20', 'Available', 6, 6),
(4, 'Physics for Engineers', 1, 5, 2, '2020-07-11', 'Lost', 8, 0),
(5, 'Introduction to Mechanics', 1, 2, 2, '2022-11-30', 'Available', 4, 3),

-- Computer Science (Category: Technology = 3)
(6, 'Introduction to Algorithms', 2, 1, 3, '2022-08-05', 'Available', 12, 9),
(7, 'Computer Networks', 2, 3, 3, '2021-12-22', 'Issued', 7, 4),
(8, 'Operating Systems', 2, 2, 3, '2023-01-10', 'Available', 6, 6),
(9, 'Database Systems', 2, 4, 3, '2020-05-17', 'Lost', 5, 0),
(10, 'Artificial Intelligence', 2, 5, 3, '2023-09-18', 'Available', 9, 8),

-- World History (Category: History = 4)
(11, 'History of Ancient Civilizations', 3, 2, 4, '2022-04-01', 'Available', 3, 3),
(12, 'World Wars Explained', 3, 3, 4, '2021-06-19', 'Issued', 6, 2),
(13, 'Renaissance to Revolution', 3, 1, 4, '2020-10-30', 'Available', 4, 4),
(14, 'A Global History', 3, 5, 4, '2023-02-25', 'Available', 7, 7),
(15, 'Historical Turning Points', 3, 4, 4, '2021-11-05', 'Lost', 2, 0),

-- Literature (Category: Fiction = 1)
(16, 'English Classics', 4, 3, 1, '2023-03-12', 'Available', 5, 5),
(17, 'Poetry Through Ages', 4, 1, 1, '2022-07-14', 'Available', 4, 4),
(18, 'Modern Literary Theory', 4, 5, 1, '2021-08-08', 'Issued', 6, 2),
(19, 'Shakespearean Works', 4, 2, 1, '2020-09-15', 'Lost', 3, 0),
(20, 'World Literature Anthology', 4, 4, 1, '2022-10-01', 'Available', 7, 6),

-- Fine Arts (Category: Art = 5)
(21, 'Art History Basics', 5, 1, 5, '2023-01-20', 'Available', 6, 6),
(22, 'Modern Art Movements', 5, 3, 5, '2022-06-11', 'Available', 5, 5),
(23, 'Painting Techniques', 5, 5, 5, '2021-05-09', 'Issued', 8, 4),
(24, 'Sculpture and Form', 5, 2, 5, '2020-03-22', 'Lost', 3, 0),
(25, 'Visual Arts Exploration', 5, 4, 5, '2023-04-16', 'Available', 4, 4);

-- --------------------------------------------------------

--
-- Table structure for table `book_demand`
--

DROP TABLE IF EXISTS `book_demand`;
CREATE TABLE `book_demand` (
  `Demand_ID` int(11) NOT NULL,
  `Book_ID` int(11) DEFAULT NULL,
  `Demand_Count` int(11) DEFAULT 0,
  `Last_Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE `category` (
  `Category_ID` int(11) NOT NULL,
  `Category_Name` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`Category_ID`, `Category_Name`, `Description`) VALUES
(1, 'Fiction', 'Fictional and imaginative writing.'),
(2, 'Science', 'Scientific books and references.'),
(3, 'Technology', 'Technology and computer-related books.'),
(4, 'History', 'Historical texts and records.'),
(5, 'Art', 'Books on art and design.');

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

DROP TABLE IF EXISTS `member`;
CREATE TABLE `member` (
  `Member_ID` int(11) NOT NULL,
  `Member_Name` varchar(100) NOT NULL,
  `Member_Contact` varchar(100) DEFAULT NULL,
  `Status` enum('Active','Inactive') DEFAULT 'Active',
  `Books_Issued_Count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE member 
CHANGE COLUMN Member_ID Member_ID INT(11) NOT NULL AUTO_INCREMENT;

--
--dumping data for table member
--
INSERT INTO member
VALUES(101, "ALLEN", 9898989898, "Active", 12),
(102, "ALICE", 9898910121, "Active", 15),
(103, "BROOKE", 9820202898, "Active", 20),
(104, "CARINA", 8985090598, "Inactive", 0),
(105, "CHARLIE", 9898900000, "Active", 14),
(106, "DAVE", 8878878878, "Inactive", 2),
(107, "ELICE", 4567112397, "Inactive", 8),
(108, "POOJA", 9019019019, "Active", 22),
(109, "ZOYA", 7878787878, "Active", 14);

-- --------------------------------------------------------
--
-- Table structure for table `publisher`
--

DROP TABLE IF EXISTS `publisher`;
CREATE TABLE `publisher` (
  `Publisher_ID` int(11) NOT NULL,
  `Publisher_Name` varchar(100) NOT NULL,
  `Publisher_Contact` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `publisher`
--

INSERT INTO `publisher` (`Publisher_ID`, `Publisher_Name`, `Publisher_Contact`) VALUES
(1, 'Pearson', '1234567890'),
(2, 'Penguin Random House', '2345678901'),
(3, 'Oxford University Press', '3456789012'),
(4, 'McGraw Hill', '4567890123'),
(5, 'Cambridge University Press', '5678901234');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

DROP TABLE IF EXISTS `review`;
CREATE TABLE `review` (
  `Review_ID` int(11) NOT NULL,
  `Book_ID` int(11) DEFAULT NULL,
  `Member_ID` int(11) DEFAULT NULL,
  `Rating` int(11) DEFAULT NULL CHECK (`Rating` between 1 and 5),
  `Review_Text` text DEFAULT NULL,
  `Review_Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
--dumping data for table review
--
INSERT INTO review (Review_ID, Book_ID, Member_ID, Rating, Review_Text, Review_Date) VALUES
(1, 2, 101, 5, 'Excellent', '2024-06-10'),
(2, 8, 105, 3, 'Okay', '2023-11-25'),
(3, 12, 103, 4, 'Good read', '2025-01-15'),
(4, 17, 108, 2, 'Boring', '2024-03-02'),
(5, 21, 109, 5, 'Loved it', '2025-04-01'),
(6, 3, 102, 4, 'Nice', '2023-08-20'),
(7, 5, 105, 1, 'Disliked', '2024-01-12'),
(8, 9, 108, 5, 'Awesome', '2023-09-30'),
(9, 14, 101, 3, 'Okay', '2025-03-10'),
(10, 18, 103, 2, 'Bad', '2023-12-01'),
(11, 7, 102, 5, 'Great', '2024-05-06'),
(12, 6, 109, 4, 'Cool', '2024-10-18'),
(13, 10, 108, 5, 'Loved it', '2025-02-22'),
(14, 16, 105, 2, 'Meh', '2023-06-15'),
(15, 25, 101, 4, 'Informative', '2024-07-03');

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
CREATE TABLE `subject` (
  `Subject_ID` int(11) NOT NULL,
  `Subject_Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subject`
--

INSERT INTO `subject` (`Subject_ID`, `Subject_Name`) VALUES
(1, 'Physics'),
(2, 'Computer Science'),
(3, 'World History'),
(4, 'Literature'),
(5, 'Fine Arts');

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

DROP TABLE IF EXISTS `transaction`;
CREATE TABLE `transaction` (
  `Transaction_ID` int(11) NOT NULL,
  `Member_ID` int(11) DEFAULT NULL,
  `Book_ID` int(11) DEFAULT NULL,
  `Account_ID` int(11) DEFAULT NULL,
  `Issue_Date` date DEFAULT NULL,
  `Due_Date` date DEFAULT NULL,
  `Return_Date` date DEFAULT NULL,
  `Status` enum('Issued','Returned','Overdue') DEFAULT 'Issued',
  `Fine_Amount` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
--dumping data for table transaction
--

INSERT INTO transaction (Transaction_ID, Member_ID, Book_ID, Account_ID, Issue_Date, Due_Date, Return_Date, Status, Fine_Amount) VALUES
(1, 101, 2, 1, '2025-02-20', '2025-03-01', '2025-03-10', 'Overdue', 15.00),
(2, 102, 5, 2, '2025-01-01', '2025-01-15', '2025-01-14', 'Returned', 0.00),
(3, 103, 8, 3, '2025-02-01', '2025-02-10', NULL, 'Issued', 0.00),
(4, 105, 14, 4, '2025-03-10', '2025-03-20', '2025-03-19', 'Returned', 0.00),
(5, 108, 21, 5, '2025-03-01', '2025-03-10', '2025-03-20', 'Overdue', 10.00),
(6, 109, 6, 6, '2025-01-05', '2025-01-20', '2025-01-19', 'Returned', 0.00),
(7, 106, 11, 7, '2025-02-25', '2025-03-10', '2025-03-15', 'Overdue', 20.00),
(8, 107, 3, 8, '2025-02-10', '2025-02-20', NULL, 'Issued', 0.00),
(9, 104, 17, 9, '2025-01-15', '2025-01-25', '2025-01-20', 'Returned', 0.00),
(10, 103, 1, 10, '2025-04-01', '2025-04-10', '2025-04-09', 'Returned', 0.00);

----------------------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`Account_ID`),
  ADD KEY `Member_ID` (`Member_ID`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`Book_ID`),
  ADD KEY `Subject_ID` (`Subject_ID`),
  ADD KEY `Publisher_ID` (`Publisher_ID`),
  ADD KEY `Category_ID` (`Category_ID`);

--
-- Indexes for table `book_demand`
--
ALTER TABLE `book_demand`
  ADD PRIMARY KEY (`Demand_ID`),
  ADD KEY `Book_ID` (`Book_ID`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`Category_ID`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`Member_ID`);

--
-- Indexes for table `publisher`
--
ALTER TABLE `publisher`
  ADD PRIMARY KEY (`Publisher_ID`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`Review_ID`),
  ADD KEY `Book_ID` (`Book_ID`),
  ADD KEY `Member_ID` (`Member_ID`);

--
-- Indexes for table `subject`
--
ALTER TABLE `subject`
  ADD PRIMARY KEY (`Subject_ID`);

--
-- Indexes for table `transaction`
--
ALTER TABLE `transaction`
  ADD PRIMARY KEY (`Transaction_ID`),
  ADD KEY `Member_ID` (`Member_ID`),
  ADD KEY `Book_ID` (`Book_ID`),
  ADD KEY `Account_ID` (`Account_ID`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `accounts_ibfk_1` FOREIGN KEY (`Member_ID`) REFERENCES `member` (`Member_ID`);

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`Subject_ID`) REFERENCES `subject` (`Subject_ID`),
  ADD CONSTRAINT `books_ibfk_2` FOREIGN KEY (`Publisher_ID`) REFERENCES `publisher` (`Publisher_ID`),
  ADD CONSTRAINT `books_ibfk_3` FOREIGN KEY (`Category_ID`) REFERENCES `category` (`Category_ID`);

--
-- Constraints for table `book_demand`
--
ALTER TABLE `book_demand`
  ADD CONSTRAINT `book_demand_ibfk_1` FOREIGN KEY (`Book_ID`) REFERENCES `books` (`Book_ID`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `review_ibfk_1` FOREIGN KEY (`Book_ID`) REFERENCES `books` (`Book_ID`),
  ADD CONSTRAINT `review_ibfk_2` FOREIGN KEY (`Member_ID`) REFERENCES `member` (`Member_ID`);

--
-- Constraints for table `transaction`
--
ALTER TABLE `transaction`
  ADD CONSTRAINT `transaction_ibfk_1` FOREIGN KEY (`Member_ID`) REFERENCES `member` (`Member_ID`),
  ADD CONSTRAINT `transaction_ibfk_2` FOREIGN KEY (`Book_ID`) REFERENCES `books` (`Book_ID`),
  ADD CONSTRAINT `transaction_ibfk_3` FOREIGN KEY (`Account_ID`) REFERENCES `accounts` (`Account_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
