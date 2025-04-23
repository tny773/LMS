-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 23, 2025 at 05:18 PM
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

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `Account_ID` int(11) NOT NULL,
  `Member_ID` int(11) DEFAULT NULL,
  `Payment_Description` text DEFAULT NULL,
  `Payment_Amount` decimal(10,2) DEFAULT NULL,
  `Payment_Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

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

-- --------------------------------------------------------

--
-- Table structure for table `book_demand`
--

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

CREATE TABLE `category` (
  `Category_ID` int(11) NOT NULL,
  `Category_Name` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `Member_ID` int(11) NOT NULL,
  `Member_Name` varchar(100) NOT NULL,
  `Member_Contact` varchar(100) DEFAULT NULL,
  `Status` enum('Active','Inactive') DEFAULT 'Active',
  `Books_Issued_Count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `publisher`
--

CREATE TABLE `publisher` (
  `Publisher_ID` int(11) NOT NULL,
  `Publisher_Name` varchar(100) NOT NULL,
  `Publisher_Contact` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `Review_ID` int(11) NOT NULL,
  `Book_ID` int(11) DEFAULT NULL,
  `Member_ID` int(11) DEFAULT NULL,
  `Rating` int(11) DEFAULT NULL CHECK (`Rating` between 1 and 5),
  `Review_Text` text DEFAULT NULL,
  `Review_Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subject`
--

CREATE TABLE `subject` (
  `Subject_ID` int(11) NOT NULL,
  `Subject_Name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaction`
--

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
