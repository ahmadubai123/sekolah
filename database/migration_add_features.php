<?php
include dirname(__DIR__) . "/config/database.php";

$queries = [];

$queries[] = "ALTER TABLE users MODIFY COLUMN role ENUM('admin','guru','siswa') NOT NULL";

$check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'profile_pic'");
if (mysqli_num_rows($check) == 0) {
    $queries[] = "ALTER TABLE users ADD COLUMN profile_pic VARCHAR(255) DEFAULT NULL AFTER email";
}
$check = mysqli_query($conn, "SHOW COLUMNS FROM users LIKE 'bio'");
if (mysqli_num_rows($check) == 0) {
    $queries[] = "ALTER TABLE users ADD COLUMN bio TEXT DEFAULT NULL AFTER profile_pic";
}

$res = mysqli_query($conn, "SHOW TABLES LIKE 'subjects'");
if (mysqli_num_rows($res) == 0) {
    $queries[] = "CREATE TABLE subjects (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(100) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

$res = mysqli_query($conn, "SHOW TABLES LIKE 'classes'");
if (mysqli_num_rows($res) == 0) {
    $queries[] = "CREATE TABLE classes (
        id INT(11) NOT NULL AUTO_INCREMENT,
        name VARCHAR(50) NOT NULL,
        grade INT(2) NOT NULL,
        teacher_id INT(11) DEFAULT NULL,
        capacity INT(3) DEFAULT 30,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

$res = mysqli_query($conn, "SHOW TABLES LIKE 'class_student'");
if (mysqli_num_rows($res) == 0) {
    $queries[] = "CREATE TABLE class_student (
        id INT(11) NOT NULL AUTO_INCREMENT,
        class_id INT(11) NOT NULL,
        student_id INT(11) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        UNIQUE KEY unique_enrollment (class_id, student_id),
        FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

$res = mysqli_query($conn, "SHOW TABLES LIKE 'schedules'");
if (mysqli_num_rows($res) == 0) {
    $queries[] = "CREATE TABLE schedules (
        id INT(11) NOT NULL AUTO_INCREMENT,
        class_id INT(11) NOT NULL,
        teacher_id INT(11) NOT NULL,
        subject_id INT(11) NOT NULL,
        day_of_week VARCHAR(20) NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        qr_token VARCHAR(100) DEFAULT NULL,
        qr_expired_at DATETIME DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE,
        FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

$check = mysqli_query($conn, "SHOW COLUMNS FROM absensi LIKE 'schedule_id'");
if (mysqli_num_rows($check) == 0) {
    $queries[] = "ALTER TABLE absensi ADD COLUMN schedule_id INT(11) DEFAULT NULL AFTER user_id";
    $queries[] = "ALTER TABLE absensi ADD FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE SET NULL";
}

$res = mysqli_query($conn, "SHOW TABLES LIKE 'performance'");
if (mysqli_num_rows($res) == 0) {
    $queries[] = "CREATE TABLE performance (
        id INT(11) NOT NULL AUTO_INCREMENT,
        teacher_id INT(11) NOT NULL,
        class_id INT(11) DEFAULT NULL,
        student_feedback_avg DECIMAL(3,2) DEFAULT 0,
        attendance_rate DECIMAL(5,2) DEFAULT 0,
        academic_achievement_score DECIMAL(5,2) DEFAULT 0,
        ranking_score DECIMAL(5,2) DEFAULT 0,
        category VARCHAR(50) DEFAULT 'Perlu Perbaikan',
        period VARCHAR(20) DEFAULT 'month',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

$res = mysqli_query($conn, "SHOW TABLES LIKE 'feedback'");
if (mysqli_num_rows($res) == 0) {
    $queries[] = "CREATE TABLE feedback (
        id INT(11) NOT NULL AUTO_INCREMENT,
        teacher_id INT(11) NOT NULL,
        student_id INT(11) NOT NULL,
        schedule_id INT(11) DEFAULT NULL,
        rating INT(1) NOT NULL,
        comment TEXT,
        anonymous TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (teacher_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

$res = mysqli_query($conn, "SHOW TABLES LIKE 'trends'");
if (mysqli_num_rows($res) == 0) {
    $queries[] = "CREATE TABLE trends (
        id INT(11) NOT NULL AUTO_INCREMENT,
        entity_type ENUM('guru','kelas','siswa') NOT NULL,
        entity_id INT(11) NOT NULL,
        metric_type ENUM('absensi','nilai','feedback','ranking') NOT NULL,
        data_points JSON,
        date_range VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

$res = mysqli_query($conn, "SHOW TABLES LIKE 'notifications'");
if (mysqli_num_rows($res) == 0) {
    $queries[] = "CREATE TABLE notifications (
        id INT(11) NOT NULL AUTO_INCREMENT,
        user_id INT(11) NOT NULL,
        title VARCHAR(100) NOT NULL,
        message TEXT,
        type ENUM('info','warning','success','error') DEFAULT 'info',
        is_read TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

$res = mysqli_query($conn, "SHOW TABLES LIKE 'grades'");
if (mysqli_num_rows($res) == 0) {
    $queries[] = "CREATE TABLE grades (
        id INT(11) NOT NULL AUTO_INCREMENT,
        student_id INT(11) NOT NULL,
        subject_id INT(11) NOT NULL,
        schedule_id INT(11) DEFAULT NULL,
        nilai DECIMAL(5,2) DEFAULT 0,
        semester VARCHAR(20),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
        FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE,
        FOREIGN KEY (schedule_id) REFERENCES schedules(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
}

$success = true;
$errors = [];
$executed = 0;

foreach ($queries as $sql) {
    $result = mysqli_query($conn, $sql);
    $executed++;
    if (!$result) {
        $errors[] = mysqli_error($conn);
        $success = false;
    }
}

if ($success) {
    echo "Migrasi database berhasil! ($executed query dieksekusi)";
} else {
    echo "Migrasi gagal pada query ke-$executed:<br>";
    foreach ($errors as $e) {
        echo "- $e<br>";
    }
}
?>