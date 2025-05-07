-- Create the database and use it
CREATE DATABASE CharityMangement;
USE CharityMangement;

-- Admin Table
CREATE TABLE Admin (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100)
);

-- User Table
CREATE TABLE User (
    user_id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    first_name VARCHAR(100),
    last_name VARCHAR(100),
    phone_number VARCHAR(20),
    is_donor BOOLEAN DEFAULT FALSE,
    is_volunteer BOOLEAN DEFAULT FALSE
);

-- Donor Table
CREATE TABLE Donor (
    donor_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    preferred_donation_type VARCHAR(100),
    last_donation_date DATETIME,
    total_donation_amount INT DEFAULT 0,
    donation_count INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES User(user_id)
);

-- Campaign Table (must be before Volunteer)
CREATE TABLE Campaign (
    campaign_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    description TEXT,
    target_amount INT,
    total_collected INT DEFAULT 0,
    total_direct_donations INT DEFAULT 0,
    event_fundraising INT,
    remaining_target INT,
    start_date DATE,
    end_date DATE,
    status VARCHAR(50),
    admin_id INT,
    FOREIGN KEY (admin_id) REFERENCES Admin(admin_id)
);

-- Volunteer Table with ON DELETE SET NULL
CREATE TABLE Volunteer (
    volunteer_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    Skills TEXT,
    Status VARCHAR(50) DEFAULT 'inactive',
    campaign_id INT DEFAULT NULL,
    FOREIGN KEY (user_id) REFERENCES User(user_id),
    FOREIGN KEY (campaign_id) REFERENCES Campaign(campaign_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
);

-- Donation Table
CREATE TABLE Donation (
    donation_id INT PRIMARY KEY AUTO_INCREMENT,
    donor_id INT,
    campaign_id INT,
    payment_method VARCHAR(100),
    donation_date DATETIME,
    status VARCHAR(50),
    receipt_number VARCHAR(100),
    donor_message TEXT,
    FOREIGN KEY (donor_id) REFERENCES Donor(donor_id),
    FOREIGN KEY (campaign_id) REFERENCES Campaign(campaign_id)
);

-- One-Time Donation Table
CREATE TABLE OneTime_Donation (
    onetime_donation_id INT PRIMARY KEY AUTO_INCREMENT,
    donation_id INT,
    amount INT,
    FOREIGN KEY (donation_id) REFERENCES Donation(donation_id)
);

-- Recurring Donation Table
CREATE TABLE Recurring_Donation (
    recurring_donation_id INT PRIMARY KEY AUTO_INCREMENT,
    donation_id INT,
    amount INT,
    frequency VARCHAR(50),
    donation_date DATE,
    next_donation_date DATE,
    FOREIGN KEY (donation_id) REFERENCES Donation(donation_id)
);

-- Event Table
CREATE TABLE Event (
    event_id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255),
    campaign_id INT,
    location VARCHAR(255),
    description TEXT,
    event_date DATETIME,
    fundraised_amount INT DEFAULT 0,
    status VARCHAR(50),
    FOREIGN KEY (campaign_id) REFERENCES Campaign(campaign_id)
);

-- Volunteer Event Table
CREATE TABLE Volunteer_Event (
    volunteer_event_id INT PRIMARY KEY AUTO_INCREMENT,
    volunteer_id INT,
    event_id INT,
    role VARCHAR(255),
    FOREIGN KEY (volunteer_id) REFERENCES Volunteer(volunteer_id),
    FOREIGN KEY (event_id) REFERENCES Event(event_id)
);

-- Payment Table
CREATE TABLE Payment (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    donation_id INT,
    amount INT,
    payment_method VARCHAR(100),
    transaction_id VARCHAR(100),
    payment_date DATETIME,
    status VARCHAR(100),
    FOREIGN KEY (donation_id) REFERENCES Donation(donation_id)
);
