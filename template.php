<?php
function renderHeader($title = "Kurssienhallinta") {
    ?>
<!DOCTYPE html>
<html lang="fi">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($title) ?></title>
<style>
    body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f6f8; color: #333; }
    a { text-decoration: none; color: #0070c0; } a:hover { text-decoration: underline; }
    nav { background-color: #dde6f1; width: 220px; height: 100vh; position: fixed; top: 0; left: 0; padding-top: 60px; box-shadow: 2px 0 5px rgba(0,0,0,0.1); }
    nav ul { list-style: none; padding: 0; }
    nav li { padding: 10px 20px; }
    nav li:hover { background-color: #c7d9f0; }
    main { margin-left: 240px; padding: 20px; }
    table { border-collapse: collapse; width: 100%; background: white; box-shadow: 0 0 5px rgba(0,0,0,0.1); }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    th { background-color: #e2e9f7; }
    tr:nth-child(even) { background-color: #f9f9f9; }
    .btn { background-color: #0070c0; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; display: inline-block; }
    .btn:hover { background-color: #005a8f; text-decoration: none; }
    .warning { color: red; font-weight: bold; }
    form input, form select { padding: 5px; margin-bottom: 10px; width: 300px; }
    
    /* Viikkonäkymän erityistyylit */
    .week-navigation { margin-bottom: 20px; text-align: center; }
    .schedule-table { width: 100%; table-layout: fixed; border-collapse: collapse; }
    .schedule-table th { width: 80px; border: 1px solid #ddd; padding: 5px; background: #f5f5f5; }
    .schedule-table .day-header { border: 1px solid #ddd; padding: 5px; background: #f5f5f5; }
    .schedule-table .time-header { border: 1px solid #ddd; padding: 5px; background: #f9f9f9; }
    .schedule-table td { 
        border: 1px solid #ddd; 
        padding: 2px; 
        height: 60px; 
        vertical-align: top; 
        position: relative; 
    }
    .session-title { font-weight: bold; }
    .session-time { font-size: 0.9em; }
    .session-room { font-size: 0.8em; color: #333; }
    
    /* Sessioiden hallinnan tyylit */
    .session-form { margin-bottom: 20px; }
    .session-form label { 
        display: inline-block; 
        margin-right: 15px; 
        margin-bottom: 10px;
        vertical-align: top;
    }
    .session-form select { 
        padding: 5px; 
        margin-left: 5px;
        width: auto;
        min-width: 120px;
    }
    .sessions-table { 
        border-collapse: collapse; 
        width: 100%; 
        background: white;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }
    .sessions-table th, .sessions-table td { 
        border: 1px solid #ddd; 
        padding: 8px; 
        text-align: left;
    }
    .sessions-table th { 
        background-color: #e2e9f7; 
    }
    .back-link { 
        margin-top: 20px; 
        display: inline-block; 
    }
    
    .teacher-session-title { font-weight: bold; }
    .teacher-session-time { font-size: 0.9em; }
    .teacher-session-room { font-size: 0.8em; color: #333; }

    .student-session-title { font-weight: bold; }
    .student-session-time { font-size: 0.9em; }
    .student-session-room { font-size: 0.8em; color: #333; }

    .room-session-title { font-weight: bold; }
    .room-session-time { font-size: 0.9em; }
    .room-session-teacher { font-size: 0.8em; color: #333; }

    /* Yhteiset sessioblokkien tyylit */
.session-block,
.teacher-session-block,
.room-session-block,
.student-session-block {
    position: absolute;
    top: 2px;
    overflow: hidden;
    padding: 3px 5px;
    margin: 1px;
    border-radius: 4px;
    font-size: 0.85em;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Yleiset tekstit */
.session-title,
.teacher-session-title,
.room-session-title,
.student-session-title {
    font-weight: bold;
}

.session-time,
.teacher-session-time,
.room-session-time,
.student-session-time {
    font-size: 0.9em;
}

.session-room,
.teacher-session-room,
.room-session-teacher,
.student-session-room {
    font-size: 0.8em;
    color: #333;
}

/* Opettajan viikkonäkymän sessiot */
.teacher-session-block {
    background: linear-gradient(135deg, #ffe7ba, #ffd591);
    border-left: 4px solid #cc6600;
}

/* Tilan viikkonäkymän sessiot */
.room-session-block {
    background: linear-gradient(135deg, #e6f7ff, #bae7ff);
    border-left: 4px solid #1890ff;
}

/* Opiskelijan viikkonäkymän sessiot */
.student-session-block {
    background: linear-gradient(135deg, #e1ffc7, #b8f5a3);
    border-left: 4px solid #339900;
}

/* Yleinen sessio (jos käytössä esim. muissa näkymissä) */
.session-block {
    background: linear-gradient(135deg, #c7e1ff, #a3d0ff);
    border-left: 4px solid #0066cc;
}


</style>
</head>
<body>

<nav>
    <ul>
        <li><a href="../index.html">Etusivu</a></li>
        <li><a href="../opettajat/lista.php">Opettajat</a></li>
        <li><a href="../opiskelijat/lista.php">Opiskelijat</a></li>
        <li><a href="../kurssit/lista.php">Kurssit</a></li>
        <li><a href="../tilat/lista.php">Tilat</a></li>
    </ul>
</nav>
<main>
<h1><?= htmlspecialchars($title) ?></h1>
<?php
}

function renderFooter() {
    ?>
</main>
</body>
</html>
<?php
}