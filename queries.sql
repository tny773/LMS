-- Create database
USE `lms`;
-- Table: category
CREATE TABLE `category` (
    `Category_ID` int(11) NOT NULL AUTO_INCREMENT,
    `Category_Name` varchar(100) NOT NULL,
    `Description` text DEFAULT NULL,
    PRIMARY KEY (`Category_ID`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
INSERT INTO `category` (`Category_ID`, `Category_Name`, `Description`)
VALUES (
        1,
        'Fiction',
        'Fictional and imaginative writing.'
    ),
    (2, 'Science', 'Scientific books and references.'),
    (
        3,
        'Technology',
        'Technology and computer-related books.'
    ),
    (4, 'History', 'Historical texts and records.'),
    (5, 'Art', 'Books on art and design.');
-- Table: publisher
CREATE TABLE `publisher` (
    `Publisher_ID` int(11) NOT NULL AUTO_INCREMENT,
    `Publisher_Name` varchar(100) NOT NULL,
    PRIMARY KEY (`Publisher_ID`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
INSERT INTO `publisher` (`Publisher_ID`, `Publisher_Name`)
VALUES (1, 'Pearson'),
    (2, 'McGraw-Hill'),
    (3, 'Oxford Press'),
    (4, 'Wiley'),
    (5, 'Springer');
-- Table: subject
CREATE TABLE `subject` (
    `Subject_ID` int(11) NOT NULL AUTO_INCREMENT,
    `Subject_Name` varchar(100) NOT NULL,
    PRIMARY KEY (`Subject_ID`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
INSERT INTO `subject` (`Subject_ID`, `Subject_Name`)
VALUES (1, 'Physics'),
    (2, 'Computer Science'),
    (3, 'History'),
    (4, 'Literature'),
    (5, 'Fine Arts');
-- Table: books
CREATE TABLE `books` (
    `Book_ID` int(11) NOT NULL AUTO_INCREMENT,
    `Book_Title` varchar(200) NOT NULL,
    `Subject_ID` int(11) DEFAULT NULL,
    `Publisher_ID` int(11) DEFAULT NULL,
    `Category_ID` int(11) DEFAULT NULL,
    `Purchase_Date` date DEFAULT NULL,
    `Available_Status` enum('Available', 'Issued', 'Lost') DEFAULT 'Available',
    `Total_Copies` int(11) DEFAULT 0,
    `Available_Copies` int(11) DEFAULT 0,
    PRIMARY KEY (`Book_ID`),
    FOREIGN KEY (`Subject_ID`) REFERENCES `subject`(`Subject_ID`),
    FOREIGN KEY (`Publisher_ID`) REFERENCES `publisher`(`Publisher_ID`),
    FOREIGN KEY (`Category_ID`) REFERENCES `category`(`Category_ID`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
-- Insert books (no trailing comma after last value!)
INSERT INTO books (
        Book_ID,
        Book_Title,
        Subject_ID,
        Publisher_ID,
        Category_ID,
        Purchase_Date,
        Available_Status,
        Total_Copies,
        Available_Copies
    )
VALUES (
        1,
        'Fundamentals of Physics',
        1,
        1,
        2,
        '2022-01-15',
        'Available',
        10,
        7
    ),
    (
        2,
        'Modern Physics Concepts',
        1,
        3,
        2,
        '2021-09-10',
        'Issued',
        5,
        0
    ),
    (
        3,
        'Applied Physics',
        1,
        4,
        2,
        '2023-03-20',
        'Available',
        6,
        6
    ),
    (
        4,
        'Physics for Engineers',
        1,
        5,
        2,
        '2020-07-11',
        'Lost',
        0,
        0
    ),
    (
        5,
        'Introduction to Mechanics',
        1,
        2,
        2,
        '2022-11-30',
        'Available',
        4,
        3
    ),
    (
        6,
        'Introduction to Algorithms',
        2,
        1,
        3,
        '2022-08-05',
        'Available',
        12,
        9
    ),
    (
        7,
        'Computer Networks',
        2,
        3,
        3,
        '2021-12-22',
        'Issued',
        7,
        4
    ),
    (
        8,
        'Operating Systems',
        2,
        2,
        3,
        '2023-01-10',
        'Available',
        6,
        6
    ),
    (
        9,
        'Database Systems',
        2,
        4,
        3,
        '2020-05-17',
        'Lost',
        0,
        0
    ),
    (
        10,
        'Artificial Intelligence',
        2,
        5,
        3,
        '2023-09-18',
        'Available',
        9,
        8
    ),
    (
        11,
        'History of Ancient Civilizations',
        3,
        2,
        4,
        '2022-04-01',
        'Available',
        3,
        3
    ),
    (
        12,
        'World Wars Explained',
        3,
        3,
        4,
        '2021-06-19',
        'Issued',
        6,
        0
    ),
    (
        13,
        'Renaissance to Revolution',
        3,
        1,
        4,
        '2020-10-30',
        'Available',
        4,
        4
    ),
    (
        14,
        'A Global History',
        3,
        5,
        4,
        '2023-02-25',
        'Available',
        7,
        7
    ),
    (
        15,
        'Historical Turning Points',
        3,
        4,
        4,
        '2021-11-05',
        'Lost',
        0,
        0
    ),
    (
        16,
        'English Classics',
        4,
        3,
        1,
        '2023-03-12',
        'Available',
        5,
        5
    ),
    (
        17,
        'Poetry Through Ages',
        4,
        1,
        1,
        '2022-07-14',
        'Available',
        4,
        4
    ),
    (
        18,
        'Modern Literary Theory',
        4,
        5,
        1,
        '2021-08-08',
        'Issued',
        6,
        0
    ),
    (
        19,
        'Shakespearean Works',
        4,
        2,
        1,
        '2020-09-15',
        'Lost',
        0,
        0
    ),
    (
        20,
        'World Literature Anthology',
        4,
        4,
        1,
        '2022-10-01',
        'Available',
        7,
        6
    ),
    (
        21,
        'Art History Basics',
        5,
        1,
        5,
        '2023-01-20',
        'Available',
        6,
        6
    ),
    (
        22,
        'Modern Art Movements',
        5,
        3,
        5,
        '2022-06-11',
        'Available',
        5,
        5
    ),
    (
        23,
        'Painting Techniques',
        5,
        5,
        5,
        '2021-05-09',
        'Issued',
        8,
        0
    ),
    (
        24,
        'Sculpture and Form',
        5,
        2,
        5,
        '2020-03-22',
        'Lost',
        0,
        0
    ),
    (
        25,
        'Visual Arts Exploration',
        5,
        4,
        5,
        '2023-04-16',
        'Available',
        4,
        4
    );
-- Table: member
CREATE TABLE `member` (
    `Member_ID` int(11) NOT NULL AUTO_INCREMENT,
    `Member_Name` varchar(100) NOT NULL,
    `Member_Contact` varchar(100) DEFAULT NULL,
    `Status` enum('Active', 'Inactive') DEFAULT 'Active',
    `Books_Issued_Count` int(11) DEFAULT 0,
    PRIMARY KEY (`Member_ID`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
INSERT INTO `member` (
        `Member_ID`,
        `Member_Name`,
        `Member_Contact`,
        `Status`,
        `Books_Issued_Count`
    )
VALUES (101, 'ALLEN', '9898989898', 'Active', 12),
    (102, 'ALICE', '9898910121', 'Active', 15),
    (103, 'BROOKE', '9820202898', 'Active', 20),
    (104, 'CARINA', '8985090598', 'Inactive', 0),
    (105, 'CHARLIE', '9898900000', 'Active', 14),
    (106, 'DAVE', '8878878878', 'Inactive', 2),
    (107, 'ELICE', '4567112397', 'Inactive', 8),
    (108, 'FINN', '9898007654', 'Active', 5),
    (109, 'GRACE', '7894561230', 'Active', 6);
-- Table: accounts
CREATE TABLE `accounts` (
    `Account_ID` int(11) NOT NULL AUTO_INCREMENT,
    `Member_ID` int(11) DEFAULT NULL,
    `Payment_Description` text DEFAULT NULL,
    `Payment_Amount` decimal(10, 2) DEFAULT NULL,
    `Payment_Date` date DEFAULT NULL,
    PRIMARY KEY (`Account_ID`),
    FOREIGN KEY (`Member_ID`) REFERENCES `member`(`Member_ID`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
INSERT INTO `accounts` (
        `Account_ID`,
        `Member_ID`,
        `Payment_Description`,
        `Payment_Amount`,
        `Payment_Date`
    )
VALUES (
        1,
        101,
        'Late fee for overdue book',
        15.00,
        '2025-03-15'
    ),
    (
        2,
        102,
        'Membership renewal',
        50.00,
        '2025-01-10'
    ),
    (3, 103, 'Lost book fine', 120.00, '2025-02-20'),
    (4, 105, 'Damage charge', 30.00, '2025-04-01'),
    (5, 108, 'Late return fine', 10.00, '2025-03-25'),
    (
        6,
        109,
        'Membership renewal',
        50.00,
        '2025-02-01'
    ),
    (
        7,
        106,
        'Late fee for overdue book',
        20.00,
        '2025-03-05'
    ),
    (
        8,
        107,
        'Lost book replacement',
        150.00,
        '2025-02-15'
    ),
    (
        9,
        104,
        'Membership cancellation refund',
        -20.00,
        '2025-01-30'
    ),
    (10, 103, 'Fine adjustment', -10.00, '2025-04-10');
-- Table: book_demand
CREATE TABLE `book_demand` (
    `Demand_ID` int(11) NOT NULL AUTO_INCREMENT,
    `Book_ID` int(11) DEFAULT NULL,
    `Demand_Count` int(11) DEFAULT 0,
    `Last_Updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`Demand_ID`),
    FOREIGN KEY (`Book_ID`) REFERENCES `books`(`Book_ID`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;
INSERT INTO book_demand (Book_ID, Demand_Count, Last_Updated)
VALUES (1, 12, '2025-04-25'),
    (2, 5, '2025-04-26'),
    (3, 9, '2025-04-27'),
    (4, 20, '2025-04-28'),
    (5, 0, '2025-04-20');