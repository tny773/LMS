# LMS
### Category table: 
Category_ID (PK),
Category_Name,
Description

### Subject table:
Subject_ID (PK),
Subject_Name

### Publisher table: 
Publisher_ID (PK),
Publisher_Name,
Publisher_Contact

### Books table:
Book_ID (PK),
Book_Title,
Subject_ID (FK),
Publisher_ID (FK),
Category_ID (FK),
Purchase_Date,
Available_Status (ENUM: 'Available', 'Issued', 'Lost', etc.),
Total_Copies,
Available_Copies

### Member table:
Member_ID (PK),
Member_Name,
Member_Contact,
Status (ENUM: 'Active', 'Inactive'),
Books_Issued_Count

### Transaction table:
Transaction_ID (PK),
Member_ID (FK),
Book_ID (FK),
Account_ID (FK, NULL if no payment/fine),
Issue_Date,
Due_Date,
Return_Date,
Status (ENUM: 'Issued', 'Returned', 'Overdue'),
Fine_Amount

### Accounts table:
Account_ID (PK),
Member_ID (FK),
Payment_Description,
Payment_Amount,
Payment_Date

### Review table (for recommendation): 
Review_ID (PK),
Book_ID (FK),
Member_ID (FK),
Rating (1–5),
Review_Text (optional),
Review_Date

### Table for Book demand: To keep track of the book demands for the next order batch (which books are being issued the most).
Demand_ID (PK),
Book_ID (FK),
Demand_Count
Last_Updated (timestamp)
