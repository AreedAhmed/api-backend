TRUNCATE TABLE user;
INSERT INTO user (id, full_name, username, password, email, role)
VALUES (1, 'Administrator', 'admin', '$2y$10$J/DF8J/Az8DiSEpXel18NOcN0qbYt5VSvKCc8oJFarXDtj7HkmCmK', 'admin@localhost',
        'creator');

TRUNCATE TABLE audit_log;
INSERT INTO audit_log (user_id, client_ip, action)
VALUES (0, INET_ATON('127.0.0.1'), 'Initialised system');

TRUNCATE TABLE vulnerability_category;
INSERT INTO vulnerability_category (name, description)
VALUES ('Access Controls', 'Related to authorization of users, and assessment of rights.'),
       ('Auditing and Logging', 'Related to auditing of actions, or logging of problems.'),
       ('Authentication', 'Related to the identification of users.'),
       ('Configuration', 'Related to security configurations of servers, devices, or software.'),
       ('Cryptography', 'Related to mathematical protections for data.'),
       ('Data Exposure', 'Related to unintended exposure of sensitive information.'),
       ('Data Validation', 'Related to improper reliance on the structure or values of data.'),
       ('Denial of Service', 'Related to causing system failure.'),
       ('Error Reporting', 'Related to the reporting of error conditions in a secure fashion.'),
       ('Patching', 'Related to keeping software up to date.'),
       ('Session Management', 'Related to the identification of authenticated users.'),
       ('Timing', 'Related to race conditions, locking, or order of operations.');

TRUNCATE TABLE organisation;
INSERT INTO organisation (name, url, contact_email)
VALUES ('Reconmap default org', 'https://reconmap.org', 'no-reply@reconmap.org');
