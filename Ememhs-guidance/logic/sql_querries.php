<?php
// Database table name constants
define('TBL_STUDENTS', 'students');
define('TBL_PARENTS', 'parents');
define('TBL_COMPLAINTS_CONCERNS', 'complaints_concerns');
define('TBL_USERS', 'users');
define('TBL_LOST_ITEMS', 'lost_items');
define('TBL_NOTIFICATIONS', 'notifications');

// Notification Queries
define(
    'SQL_INSERT_NOTIFICATION',
    "INSERT INTO " . TBL_NOTIFICATIONS . " (
        user_id,
        reference_id,
        reference_type,
        type,
        message,
        is_read,
        date_created,
        time_created
    ) VALUES (?, ?, ?, ?, ?, 0, ?, ?)"
);

define(
    'SQL_GET_STUDENT_NOTIFICATIONS',
    "SELECT n.*, 
        CASE 
            WHEN n.reference_type = 'complaint' THEN cc.type
            WHEN n.reference_type = 'lost_item' THEN li.category
        END as reference_type_detail,
        CASE 
            WHEN n.reference_type = 'complaint' THEN cc.status
            WHEN n.reference_type = 'lost_item' THEN li.status
        END as reference_status,
        CASE 
            WHEN n.reference_type = 'complaint' THEN cc.description
            WHEN n.reference_type = 'lost_item' THEN li.description
        END as description,
        CASE 
            WHEN n.reference_type = 'lost_item' THEN li.item_name
            ELSE NULL
        END as item_name,
        CASE 
            WHEN n.reference_type = 'lost_item' THEN li.location
            ELSE NULL
        END as location_found,
        CASE 
            WHEN n.reference_type = 'complaint' THEN cc.evidence
            WHEN n.reference_type = 'lost_item' THEN li.photo
            ELSE NULL
        END as photo,
        CASE 
            WHEN n.reference_type = 'complaint' THEN cc.mime_type
            WHEN n.reference_type = 'lost_item' THEN li.mime_type
            ELSE NULL
        END as mime_type
     FROM " . TBL_NOTIFICATIONS . " n
     LEFT JOIN " . TBL_COMPLAINTS_CONCERNS . " cc ON n.reference_id = cc.id AND n.reference_type = 'complaint'
     LEFT JOIN " . TBL_LOST_ITEMS . " li ON n.reference_id = li.id AND n.reference_type = 'lost_item'
     WHERE n.user_id = ?
     ORDER BY n.date_created DESC, n.time_created DESC"
);

define(
    'SQL_GET_UNREAD_NOTIFICATIONS_COUNT',
    "SELECT COUNT(*) as unread_count
     FROM " . TBL_NOTIFICATIONS . "
     WHERE user_id = ? AND is_read = 0"
);

define(
    'SQL_MARK_NOTIFICATION_AS_READ',
    "UPDATE " . TBL_NOTIFICATIONS . "
     SET is_read = 1
     WHERE id = ? AND user_id = ?"
);

define(
    'SQL_MARK_ALL_NOTIFICATIONS_AS_READ',
    "UPDATE " . TBL_NOTIFICATIONS . "
     SET is_read = 1
     WHERE user_id = ? AND is_read = 0"
);

// SQL Query Constants for Students Registration
define(
    'SQL_CHECK_EMAIL_EXISTS',
    "SELECT email FROM " . TBL_STUDENTS . " WHERE email = ?"
);

define(
    'SQL_CHECK_STUDENT_ID_EXISTS',
    "SELECT student_id FROM " . TBL_STUDENTS . " WHERE student_id = ?"
);

define(
    'SQL_INSERT_COMPLAINTS_CONCERNS',
    "INSERT INTO " . TBL_COMPLAINTS_CONCERNS . " (student_id, type, description, preferred_counseling_date, evidence, mime_type, status, date_created, time_created)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
);

define(
    'SQL_UPDATE_COMPLAINTS_CONCERNS',
    "UPDATE " . TBL_COMPLAINTS_CONCERNS . " SET student_id = ?, type = ?, description = ?, preferred_counseling_date = ?, evidence = ?, mime_type = ?, status = ?, date_created = ?, time_created = ? WHERE id = ?"
);

define(
    'SQL_INSERT_USER',
    "INSERT INTO " . TBL_USERS . " (email, password, role) 
     VALUES (?, ?, ?)"
);

define(
    'SQL_INSERT_STUDENT',
    "INSERT INTO " . TBL_STUDENTS .
        " (user_id, first_name, middle_name, last_name, grade_level, section, phone_number) 
     VALUES (?, ?, ?, ?, ?, ?, ?)"
);

define(
    'SQL_INSERT_PARENT',
    "INSERT INTO " . TBL_PARENTS .
        " (parent_name, contact_number, student_id) 
     VALUES (?, ?, ?)"
);

define(
    'SQL_GET_STUDENT',
    "SELECT s.*, p.parent_name, p.contact_number, u.email, u.password
     FROM " . TBL_STUDENTS . " s
     LEFT JOIN " . TBL_PARENTS . " p ON s.id = p.student_id
     LEFT JOIN " . TBL_USERS . " u ON s.user_id = u.id
     WHERE s.id = ?"
);


define(
    'SQL_GET_STUDENT_BY_ID',
    "SELECT id, email, password, first_name, middle_name, last_name, grade_level, section from students where id = ?"
);

define(
    'SQL_UPDATE_STUDENT',
    "UPDATE " . TBL_STUDENTS .
        " SET first_name = ?, middle_name = ?, last_name = ?, 
          grade_level = ?, section = ?, phone_number = ?
     WHERE student_id = ?"
);

define(
    'SQL_UPDATE_PARENT',
    "UPDATE " . TBL_PARENTS .
        " SET parent_name = ?, contact_number = ?
     WHERE student_id = ?"
);

define(
    'SQL_UPDATE_USER',
    "UPDATE " . TBL_USERS .
        " SET email = ?
     WHERE id = (SELECT user_id FROM " . TBL_STUDENTS . " WHERE student_id = ?)"
);

define(
    'SQL_DELETE_STUDENT',
    "DELETE s, p, u 
     FROM " . TBL_STUDENTS . " s
     LEFT JOIN " . TBL_PARENTS . " p ON s.student_id = p.student_id
     LEFT JOIN " . TBL_USERS . " u ON s.user_id = u.id
     WHERE s.student_id = ?"
);


define('SQL_LOGIN',
    "SELECT u.id as user_id, u.email, u.role, s.id as student_id, s.first_name, s.last_name 
     FROM " . TBL_USERS . " u
     LEFT JOIN " . TBL_STUDENTS . " s ON u.id = s.user_id
     WHERE u.email = ? AND u.password = ?"
);
// define(
//     'SQL_LOGIN',
//     "SELECT id, student_id from ".TBL_USERS." where email = ? and password = ?"
// );

// Search Queries
define(
    'SQL_SEARCH_STUDENTS',
    "SELECT s.*, p.parent_name, p.contact_number 
     FROM " . TBL_STUDENTS . " s
     LEFT JOIN " . TBL_PARENTS . " p ON s.student_id = p.student_id
     WHERE s.first_name LIKE ? OR s.last_name LIKE ? OR s.student_id LIKE ?"
);

// List Queries

define(
    'SQL_LIST_STUDENTS',
    "SELECT s.*, p.parent_name, p.contact_number 
     FROM " . TBL_STUDENTS . " s
     LEFT JOIN " . TBL_PARENTS . " p ON s.student_id = p.student_id
     ORDER BY s.grade_level, s.section, s.last_name"
);

define(
    'SQL_LIST_COMPLAINTS_CONCERNS',
    "SELECT cc.*, s.first_name, s.last_name,  s.grade_level, s.section
    FROM " . TBL_COMPLAINTS_CONCERNS . " cc
    JOIN " . TBL_STUDENTS . " s ON cc.student_id = s.id
    ORDER BY cc.date_created DESC"
    );

define(
    'SQL_LIST_COMPLAINTS_CONCERNS_BY_STUDENT',
    "SELECT * FROM " . TBL_COMPLAINTS_CONCERNS . " WHERE student_id = ?"
);

define(
    'SQL_LIST_COMPLAINTS_CONCERNS_BY_ID',
    "SELECT * FROM " . TBL_COMPLAINTS_CONCERNS . " WHERE id = ?"
);

define(
    'SQL_SUM_LIST_COMPLAINTS_CONCERNS_BY_STATUS',
    "SELECT COUNT(*) FROM " . TBL_COMPLAINTS_CONCERNS . " AS total_complaints WHERE status = ?"
);
define(
    'SQL_SUM_LIST_COMPLAINTS_CONCERNS_BY_STUDENT',
    "SELECT COUNT(*) FROM " . TBL_COMPLAINTS_CONCERNS . " AS total_complaints WHERE student_id = ?"
);

define(
    'SQL_SUM_LIST_COMPLAINTS_CONCERNS_BY_STUDENT_STATUS',
    "SELECT COUNT(*) FROM " . TBL_COMPLAINTS_CONCERNS . " AS total_complaints WHERE student_id = ? AND status = ?"
);



define(
    'SQL_LIST_STUDENTS_BY_GRADE',
    "SELECT s.*, p.parent_name, p.contact_number 
     FROM " . TBL_STUDENTS . " s
     LEFT JOIN " . TBL_PARENTS . " p ON s.student_id = p.student_id
     WHERE s.grade_level = ?
     ORDER BY s.section, s.last_name"
);

// Password Reset Query
define(
    'SQL_UPDATE_PASSWORD',
    "UPDATE " . TBL_USERS .
        " SET password = ?
     WHERE email = ?"
);

//Lost Item
define('SQL_INSERT_LOST_ITEMS', "INSERT INTO " . TBL_LOST_ITEMS . " (
    student_id,
    item_name,
    category,
    date_lost,
    time_lost,
    location,
    description,
    photo,
    mime_type,
    receive_sms,
    phone_number,
    status,
    date,
    time
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

define('SQL_UPDATE_LOST_ITEMS', "UPDATE " . TBL_LOST_ITEMS . " SET
    student_id = ?,
    item_name = ?,
    category = ?,
    date_lost = ?,
    time_lost = ?,
    location = ?,
    description = ?,
    photo = ?,
    mime_type = ?,
    receive_sms = ?,
    phone_number = ?,
    status = ?,
    date = ?,
    time = ?
    WHERE id = ? ");



define('SQL_GET_LOST_ITEMS', "SELECT * FROM " . TBL_LOST_ITEMS . "  WHERE student_id = ? ORDER BY date_reported DESC, time_reported DESC");

define('SQL_GET_LOST_ITEMS_BY_ID', "SELECT * FROM " . TBL_LOST_ITEMS . "  WHERE id = ?");

define('SQL_UPDATE_LOST_ITEM_STATUS', "UPDATE " . TBL_LOST_ITEMS . " SET status = ? WHERE id = ? AND student_id = ?");

define(
    'SQL_LIST_LOST_ITEMS_BY_STATUS',
    "SELECT * FROM " . TBL_LOST_ITEMS . " WHERE status = ?"
);


define(
    'SQL_LIST_LOST_ITEMS_BY_STUDENT',
    "SELECT * FROM " . TBL_LOST_ITEMS . " WHERE student_id = ?"
);

define(
    'SQL_SUM_LIST_LOST_ITEMS_BY_STUDENT',
    "SELECT COUNT(*) FROM " . TBL_LOST_ITEMS . " AS total_lost_items WHERE student_id = ?"
);

define(
    'SQL_SUM_LIST_LOST_ITEMS_BY_STUDENT_STATUS',
    "SELECT COUNT(*) FROM " . TBL_LOST_ITEMS . " AS total_lost_items WHERE student_id = ? AND status = ?"
);
